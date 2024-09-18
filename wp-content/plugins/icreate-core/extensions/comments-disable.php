<?php
/**
 * Plugin Name: Disable Comments
 * Description: Disables comments from the menu, posts and pages in the administration area
 * Version: 1.0.0
 */

function icreate_core_remove_comment_menu_item() {
	remove_menu_page('edit-comments.php');
}
add_action( 'admin_menu', 'icreate_core_remove_comment_menu_item', 999 );


// Remove comments from post and pages
function icreate_core_remove_comment_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
}
add_action('init', 'icreate_core_remove_comment_support', 100);


// Remove comments from admin bar
function icreate_core_comments_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'icreate_core_comments_admin_bar_render' );