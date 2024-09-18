<?php
/**
 * Plugin Name: Disable Frontend Admin Bar
 * Description: Disables the Admin Bar on the frontend of the site when signed in to the CMS
 * Version: 1.0.0
 */

add_filter('show_admin_bar', '__return_false');