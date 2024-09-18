<?php
/**
 * Plugin Name: Cloudflare Helper
 * Description: The Cloudflare Helper Plugin is specifically designed for Cloudways Autonomous, providing automated cache-clearing functionality for Cloudflare Enterprise.
 * Author: DigitalOcean, Cloudways
 * Version: 1.0
 *
 * @package cloudflare-cache-helper
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
register_activation_hook( __FILE__, 'check_breeze_compatibility_on_activation' );
add_action( 'admin_init', 'check_breeze_compatibility' );
/**
 * Check if breeze is active when user tries to activate this plugin.
 * Show a message and return back link.
 */
function check_breeze_compatibility_on_activation() {
	if ( is_plugin_active( 'breeze/breeze.php' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'The Cloudflare Helper plugin will not activate if the Breeze plugin is active. We strongly recommend using only one plugin at a time. ', 'Plugin Activation Error', array( 'back_link' => true ) );
	}
}
/**
 * When user tries to activate breeze while this plugin is active
 * just silently deactivate this plugin.
 */
function check_breeze_compatibility() {
	if ( is_plugin_active( 'breeze/breeze.php' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}
define( 'CLOUDFLARE_CACHE_HELPER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BREEZE_CF_HELPER_VERSION', '1.0' );
require_once ABSPATH . 'wp-admin/includes/plugin.php';
/**
 * Main Cloudflare Cache Helper Class
 *
 * @since 2.0
 */
if ( ! class_exists( 'Cloudflare_Cache_Helper_Functionality' ) && ! is_plugin_active( 'breeze/breeze.php' ) ) {
	require_once CLOUDFLARE_CACHE_HELPER_PLUGIN_DIR . 'class-custom-breeze-cloudflare-helper.php';
	require_once CLOUDFLARE_CACHE_HELPER_PLUGIN_DIR . 'class-clear-cf-cache-cli.php';
	require_once CLOUDFLARE_CACHE_HELPER_PLUGIN_DIR . 'class-cloudflare-cache-helper-functionality.php';
	Cloudflare_Cache_Helper_Functionality::instance();
}
