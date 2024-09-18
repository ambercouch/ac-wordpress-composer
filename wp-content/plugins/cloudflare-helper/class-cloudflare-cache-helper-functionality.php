<?php
/**
 * This file contains the functionality class
 * for Cloudflare Cache Helper plugin.
 *
 * @package cloudflare-cahce-helper
 */

/**
 * Main class for Cloudflare Cache Helper Plugin.
 */
final class Cloudflare_Cache_Helper_Functionality {

	private static $instance;
	private function __construct() {
		// Private constructor to prevent multiple instances.
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
				self::$instance->hooks();
		}

		return self::$instance;
	}

	public function hooks() {
		add_action( 'admin_bar_menu', array( $this, 'register_admin_bar_menu' ), 500 );
		add_filter( 'removable_query_args', array( $this, 'register_removable_query_args' ) );
		add_action( 'switch_theme', array( &$this, 'clear_cf_on_changing_theme' ), 11, 3 );
		add_action( 'shutdown', array( $this, 'clear_cloudflare_cache' ) );
		add_action( 'save_post', array( $this, 'purge_post_on_update_content' ), 9, 3 );
		add_action( 'wp_trash_post', array( $this, 'purge_cloudflare_cache' ), 9, 1 );
	}

	public function clear_cf_on_changing_theme( string $new_name, string $new_theme, string $old_theme ) {
		$list_of_urls[] = get_home_url();
		Custom_Breeze_CloudFlare_Helper::reset_all_cache( $list_of_urls );
	}

	public function purge_post_on_update_content( int $post_id, WP_Post $post, bool $update ) {
		if ( true === $update ) {

			$post_type = get_post_type( $post_id );

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || 'revision' === $post_type ) {
				return;
			} elseif ( ! current_user_can( 'edit_post', $post_id ) && ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) ) {
				return;
			}

			$this->purge_cloudflare_cache( $post_id );

		}

	}

	public function purge_cloudflare_cache( $post_id ) {

		if ( false === get_permalink( $post_id ) ) {
			return;
		}
		// Reset CloudFlare cache.
		$get_permalink = get_permalink( $post_id );
		// On delete action, the permalink has "__trashed" added to the permalink.
		// We need to remove that.
		$get_permalink  = str_replace( '__trashed', '', $get_permalink );
		$list_of_urls   = array();
		$list_of_urls[] = $get_permalink;

		$noarchive_post_type = array( 'post', 'page' );
		$this_post_status    = get_post_status( $post_id );
		$this_post_type      = get_post_type( $post_id );
		$rest_api_route      = 'wp/v2';
		$valid_post_status   = array( 'publish', 'private', 'trash' );

		$post_type_object = get_post_type_object( $this_post_type );
		if ( isset( $post_type_object->rest_base ) && ! empty( $post_type_object->rest_base ) ) {
			$rest_permalink = get_rest_url() . $rest_api_route . '/' . $post_type_object->rest_base . '/' . $post_id . '/';
		} elseif ( 'post' === $this_post_type ) {
			$rest_permalink = get_rest_url() . $rest_api_route . '/posts/' . $post_id . '/';
		} elseif ( 'page' === $this_post_type ) {
			$rest_permalink = get_rest_url() . $rest_api_route . '/views/' . $post_id . '/';
		}
		if ( isset( $rest_permalink ) ) {
			$list_of_urls[] = $rest_permalink;
		}

		// Add in AMP permalink if Automattic's AMP is installed
		if ( function_exists( 'amp_get_permalink' ) ) {
			$list_of_urls[] = amp_get_permalink( $post_id );
			// Regular AMP url for posts
			$list_of_urls[] = get_permalink( $post_id ) . 'amp/';
		}
		if ( 'trash' === $this_post_status ) {
			$list_of_urls[] = $get_permalink . 'feed/';
		}

		$author_id = get_post_field( 'post_author', $post_id );
		array_push(
			$list_of_urls,
			get_author_posts_url( $author_id ),
			get_author_feed_link( $author_id ),
			get_rest_url() . $rest_api_route . '/users/' . $author_id . '/'
		);

		$categories = get_the_category( $post_id );
		if ( $categories ) {
			foreach ( $categories as $cat ) {
				array_push(
					$list_of_urls,
					get_category_link( $cat->term_id ),
					get_rest_url() . $rest_api_route . '/categories/' . $cat->term_id . '/'
				);
			}
		}

		$tags = get_the_tags( $post_id );
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				array_push(
					$list_of_urls,
					get_tag_link( $tag->term_id ),
					get_rest_url() . $rest_api_route . '/tags/' . $tag->term_id . '/'
				);
			}
		}

		// Archives and their feeds
		if ( $this_post_type && ! in_array( $this_post_type, $noarchive_post_type, true ) ) {
			$get_archive_link      = get_post_type_archive_link( get_post_type( $post_id ) );
			$get_archive_feed_link = get_post_type_archive_feed_link( get_post_type( $post_id ) );
			if ( ! empty( $get_archive_link ) ) {
				$list_of_urls[] = $get_archive_link;
			}
			if ( ! empty( $get_archive_feed_link ) ) {
				$list_of_urls[] = $get_archive_feed_link;
			}
		}

		// Feeds
		array_push(
			$list_of_urls,
			get_bloginfo_rss( 'rdf_url' ),
			get_bloginfo_rss( 'rss_url' ),
			get_bloginfo_rss( 'rss2_url' ),
			get_bloginfo_rss( 'atom_url' ),
			get_bloginfo_rss( 'comments_rss2_url' ),
			get_post_comments_feed_link( $post_id )
		);
		// Home Pages and (if used) posts page
		array_push(
			$list_of_urls,
			get_rest_url(),
			trailingslashit( home_url() )
		);
		if ( 'page' === get_option( 'show_on_front' ) ) {
			// Ensure we have a page_for_posts setting to avoid empty URL
			if ( get_option( 'page_for_posts' ) ) {
				$list_of_urls[] = get_permalink( get_option( 'page_for_posts' ) );
			}
		}

		Custom_Breeze_CloudFlare_Helper::purge_cloudflare_cache_urls( $list_of_urls );
	}

	public function register_admin_bar_menu( WP_Admin_Bar $admin_bar ) {

		if (
			! is_admin()
			|| ! Custom_Breeze_CloudFlare_Helper::is_cloudflare_enabled()
			|| ! current_user_can( 'manage_options' ) 
		) {
			return;
		}

		// add parent item
		$admin_bar->add_menu(
			array(
				'id'    => 'clf-helper-topbar',
				'title' => esc_html( 'Cloudflare Helper' ),
			)
		);

		$admin_bar->add_menu(
			array(
				'id'     => 'cloudflare-purge-cache-helper',
				'title'  => esc_html( 'Purge Cloudflare Cache' ),
				'parent' => 'clf-helper-topbar',
				'href'   => $this->purge_cloudflare_cache_url(),
			)
		);
	}

	public function register_removable_query_args( $removable_query_args ): array {

		$cloudflare_cache_query_args = array(
			'_wpnonce',
			'cf_helper_purge_cloudflare',
			'cf_helper_purge_cache_cloudflare',
		);

		return array_merge( $removable_query_args, $cloudflare_cache_query_args );
	}

	public function clear_cloudflare_cache() {

		if ( isset( $_GET['cf_helper_purge_cloudflare'] ) && check_admin_referer( 'cf_helper_purge_cache_cloudflare' ) ) {
			$reset   = Custom_Breeze_CloudFlare_Helper::reset_all_cache();
			$json    = json_decode( trim( $reset ), true );
			$message = __( 'CloudWays - Cloudflare microservice was not reachable. ', 'breeze' );
			$class   = 'notice notice-error is-dismissible';
			if ( null !== $json && json_last_error() === JSON_ERROR_NONE && isset( $json['success'] ) ) {
				$success = filter_var( $json['success'], FILTER_VALIDATE_BOOLEAN );
				$class   = 'notice notice-warning is-dismissible';
				$message = __( 'Cloudflare cache data has not been purged. ', 'breeze' );
				if ( true === $success ) {
					$message = __( 'Cloudflare cache data has been purged. ', 'breeze' );
					$class   = 'notice notice-success is-dismissible';
				}
			}
			printf(
				'<div id="message-clear-cache-top" class="%1$s"><p>%2$s</p></div>',
				esc_attr( $class ),
				esc_html( $message )
			);
		}
	}

	public function purge_cloudflare_cache_url() {

		$current_params                   = $_SERVER['QUERY_STRING'];
		$current_params ? $current_params = '?' . $current_params : $current_params = '';
		$current_screen_base              = get_current_screen()->base;
		$is_network                       = is_multisite() && is_network_admin();
		if ( 'dashboard' === $current_screen_base ) {
			$current_screen_url = admin_url() . $current_params;
		} else {
			$current_screen_url = admin_url( basename( $_SERVER['REQUEST_URI'] ) );
		}
		if ( true === $is_network ) {
			$current_screen_url = network_admin_url( basename( $_SERVER['REQUEST_URI'] ) );
			// particular fix when network is found twice in the url.
			$current_screen_url = str_replace( 'network/network', 'network/', $current_screen_url );
		}

		$current_screen_url         = remove_query_arg(
			array(
				'_wpnonce',
				'cf_helper_purge_cloudflare',
				'cf_helper_purge_cache_cloudflare',
			),
			$current_screen_url
		);
		$purge_cloudflare_cache_url = esc_url( wp_nonce_url( add_query_arg( 'cf_helper_purge_cloudflare', 1, $current_screen_url ), 'cf_helper_purge_cache_cloudflare' ) );
		return $purge_cloudflare_cache_url;
	}
}
