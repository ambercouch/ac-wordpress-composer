<?php
/**
 * Plugin Name: iCreate Core
 * Plugin URI: http://www.creo.co.uk
 * Description: iCreate Website Core Setup
 * Version: 1.0
 * Author: Creo Interactive
 * Author URI: http://www.creo.co.uk
 */


class ICreateCore {
	var $options_name = 'icreate-core-options';
	var $options;

	var $prefix = 'icreate_core_';
	var $custom_meta_fields;

	public function __construct() {
		defined('ABSPATH') or die("Invalid script access");

		// Create options on plugin activation
		register_activation_hook( __FILE__, array( $this, 'plugin_activation') );

		// add_action( 'admin_init', array( $this, 'settings_api_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ) );

		$this->options = get_option( $this->options_name );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// Loop through the active extensions and include that file
		if(count($this->options['active_extensions'])) {
			$dir = ICreateCore::get_extension_folder();
			$scan = scandir( $dir );

			foreach ($this->options['active_extensions'] as $file) {
				if(in_array($file, $scan)) {
					require_once($dir . $file);
				}
			}
		}
	}

	function plugin_activation() {
		if( !(isset($this->options['active_extensions']) ) ) {
			// Set the default extensions to be installed
			$this->options['active_extensions'] = array(
				"comments-disable.php",
				"creo-cms-branding.php",
				"email-sender.php",
				"svg-upload-enable.php",
			);
		}

		update_option($this->options_name, $this->options);
	}


	function add_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->options_name ) . '">Settings</a>',
		);

		return array_merge( $links, $plugin_links );
	}


	function admin_menu() {
		$page = add_options_page( 'Options', 'iCreate Core', 'manage_options', $this->options_name, array( $this, 'admin_page' ) );

		// // Using registered $page handle to hook stylesheet loading
		// add_action( 'admin_print_scripts-' . $page, array($this, 'style_preview_admin_scripts') );
		// add_action( 'admin_print_styles-' . $page, array($this, 'style_preview_admin_styles') );
	}


	function admin_post() {
			// echo "<pre>";
			// var_dump($_POST);
			// exit();
		if( isset($_POST['action']) && $_POST['action'] == 'icreate_core_settings' ) {
			$this->options['active_extensions'] = array();

			if(isset($_POST['extensions'])) {
				$this->options['active_extensions'] = $_POST['extensions'];
			}

			update_option($this->options_name, $this->options);

			wp_safe_redirect( add_query_arg('updated', 'true', wp_get_referer() ) );
		} else {
			wp_safe_redirect( wp_get_referer() );
		}

		exit();
	}


	function admin_page() {
		$dir = ICreateCore::get_extension_folder();
		$scan = scandir( $dir ); ?>

		<div class="wrap">
			<h2>iCreate Core</h2>

			<p>Select the extensions below to be included within the website:</p>

			<form method="POST" action="<?php echo admin_url( 'admin-post.php' );//echo admin_url( 'options-general.php?page=style-preview-options' );// echo admin_url( 'admin-post.php' ); ?>">
				<table class="wp-list-table widefat plugins">
					<thead>
						<tr>
							<th scope="col" class="manage-column column-cb check-column"></th>
							<th scope="col" class="manage-column column-name">Extension</th>
							<th scope="col" id="description" class="manage-column column-description">Description</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($scan as $file) {
						$file_path = $dir . $file;

						if ( !is_dir($dir . $file) ) {
							$file_data = get_plugin_data( $file_path );
							$is_selected = false;

							if( in_array($file, $this->options['active_extensions']) ) $is_selected = true; ?>
							<tr class="<?php if(!$is_selected) echo 'in'; ?>active">
								<td scope="row" class="check-column">
									<input type="checkbox" name="extensions[]" value="<?php echo $file; ?>"<?php if($is_selected) echo ' checked="checked"'; ?>>
								</td>
								<td>
									<?php echo $file_data['Name']; ?>
								</td>
								<td>
									<?php if($file_data['Description'] != '') { ?>
										<p><?php echo $file_data['Description']; ?></p>
									<?php } ?>

									<?php if($file_data['Version'] != '') { ?>
										Version <?php echo $file_data['Version']; ?>
									<?php } ?>
								</td>
							</tr>
						<?php }
					} ?>
					</tbody>
				</table>
				<input type="hidden" name="action" value="icreate_core_settings">
				<?php submit_button(); ?>
			</form>

		</div>
	<?php }

	function admin_init() {
		add_action( 'admin_post_icreate_core_settings', array( $this, 'admin_post' ) );
	}

	public static function get_extension_folder() {
		return plugin_dir_path( __FILE__ ) . 'extensions/';
	}

	public static function get_extension_url() {
		return plugin_dir_url( __FILE__ ) . 'extensions/';
	}
}

$ICreateCore = new ICreateCore();