<?php

// Do not proceed if it's not a WP-CLI command.
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

class Cf_Helper_Cli extends \WP_CLI_Command {

	function help( $args, $assoc_args ) {
		WP_CLI::line( '---' );
		WP_CLI::line( WP_CLI::colorize( '%Y"wp cloudflare-helper purge cache" is the full command.' ) );
		WP_CLI::line( '---' );
		return;
	}

	function purge( $args, $assoc_args ) {

		if ( ! empty( $args )
		&& ( 'help' === $args[0] || 'cache' === $args[0] )
			) {

			if ( 'help' === $args[0] ) {
				WP_CLI::line( '---' );
				WP_CLI::line( WP_CLI::colorize( '%Y" wp cloudflare-helper purge cache" is the full command.' ) );
				WP_CLI::line( '---' );
				return;
			}

			if ( 'cache' === $args[0] ) {
				if ( isset( $assoc_args['link'] ) && ! empty( $assoc_args['link'] ) ) {
					Custom_Breeze_CloudFlare_Helper::purge_cloudflare_cache_urls( array( $assoc_args['link'] ) );

				} elseif ( is_multisite() ) {
						$sites        = get_sites();
						$list_of_urls = array();
					foreach ( $sites as $blog_data ) {
						$url            = get_home_url( $blog_data->blog_id );
						$list_of_urls[] = trailingslashit( $url );
					}
						Custom_Breeze_CloudFlare_Helper::reset_all_cache( $list_of_urls );
				} else {
					$url            = get_home_url();
					$list_of_urls[] = trailingslashit( $url );
					Custom_Breeze_CloudFlare_Helper::reset_all_cache( $list_of_urls );
				}
				WP_CLI::success(
					__( 'Cloudflare cache has been purged.', 'breeze' )
				);
			}
		} else {
			WP_CLI::error( '"wp cloudflare-helper purge cache" is the correct command.' );
		}
	}
}

WP_CLI::add_command(
	'cloudflare-helper',
	'Cf_Helper_Cli',
	array( 'file-path' => '' )
);
