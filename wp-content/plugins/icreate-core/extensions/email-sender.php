<?php
/**
 * Plugin Name: Change Default Email Sender Information
 * Description: Changes the default WordPress email sender details to be retrieved from the admin email and the name from the blog name
 * Version: 1.0.0
 */


function icreate_core_mail_from($old_from) {
	return get_option( 'admin_email' );
}
add_filter('wp_mail_from', 'icreate_core_mail_from');

function icreate_core_mail_from_name($old_from_name) {
	return get_bloginfo();
}
add_filter('wp_mail_from_name', 'icreate_core_mail_from_name');