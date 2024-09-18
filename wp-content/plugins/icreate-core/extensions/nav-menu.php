<?php
/**
 * Plugin Name: Navigation Menu
 * Description: Sets out the default iCreate menu
 * Version: 1.0.0
 */


class ICreate_Nav_Walker extends Walker_Nav_Menu {

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul>\n";
  }

}

function icreate_core_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);

  $classes[] = 'menu-' . $slug;

  $classes = array_unique($classes);

  return array_filter($classes, 'is_element_empty');
}
add_filter('nav_menu_css_class', 'icreate_core_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

function icreate_core_nav_menu_args($args = '') {
  $icreate_nav_menu_args['container'] = false;

  if (!$args['items_wrap']) {
    $icreate_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
  }

  if (!$args['depth']) {
    $icreate_nav_menu_args['depth'] = 0;
  }

  if (!$args['walker']) {
    $icreate_nav_menu_args['walker'] = new ICreate_Nav_Walker();
  }

  return array_merge($args, $icreate_nav_menu_args);
}
add_filter('wp_nav_menu_args', 'icreate_core_nav_menu_args', 5);



// TODO: Make this drop down script work for the menu for touch devices (can be seen working on Morgan Furniture)

// <script>
// $(function() {
// 	$('> a', jQuery('#menuMain li').has('.menuOuter2')).on("touchstart", function (e) {
// 		'use strict'; //satisfy code inspectors
// 		var link = jQuery(this);

// 		if (link.hasClass('taphover')) {
// 			return true;
// 		} else {
// 			link.addClass('taphover');
// 			$('#menuMain li a').not(this).removeClass('taphover');
// 			e.preventDefault();
// 			return false; //extra, and to make sure the function has consistent return points
// 		}
// 	});

// 	// Remove the taphover class if pressing outside of the link
// 	$('body').on("touchstart", function(e) {
// 		if(!$(e.target).closest('.taphover').length) {
// 			$('#menuMain li a').removeClass('taphover');
// 		}
// 	});
// });
// </script>