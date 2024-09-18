<?php
/**
Plugin Name: Date & Time Picker for Advanced Custom Fields
Plugin URI: https://github.com/toszcze/acf-date-time-picker
Description: Date & Time Picker field for Advanced Custom Fields 4 and 5 (Pro)
Version: 1.1.4
Author: Bartosz Romanowski
Author URI: http://romanowski.im/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Text Domain: acf-date-time-picker
Domain Path: /languages/
*/

// exit if accessed directly
if(!defined('ABSPATH')) exit;

if(!defined('ACF_DTP_VERSION')) {
	define('ACF_DTP_VERSION', '1.1.4');
}
if(!defined('ACF_DTP_URL')) {
	define('ACF_DTP_URL', plugin_dir_url(__FILE__));
}

if(!class_exists('acf_plugin_date_time_picker')):

class acf_plugin_date_time_picker {
	/**
	* Class constructor
	*
	* @since 1.0.0
	*/
	public function __construct() {
		load_plugin_textdomain('acf-date-time-picker', false, plugin_basename(dirname(__FILE__)).'/languages');
		
		add_action('acf/include_field_types', array($this, 'include_field_types'));	// ACF5
		add_action('acf/register_fields', array($this, 'include_field_types'));		// ACF4
	}
	
	/**
	* Include the field type class
	*
	* @since 1.0.0
	*
	* @param integer $version Major ACF version; defaults to 4
	*/
	public function include_field_types($version = 4) {
		if(empty($version)) {
			$version = 4;
		}
		include_once('fields/acf-date_time_picker-common.php');
		include_once('fields/acf-date_time_picker-v'.$version.'.php');
	}
	
}

// initialize plugin
new acf_plugin_date_time_picker();

endif;
