<?php
/**
 * Plugin Name: Creo CMS Branding
 * Description: Uses Creo Branding for the WordPress CMS
 * Version: 1.0.0
 */

function icreate_core_login_styles() { ?>
	<style type="text/css">
		body.login div#login h1 a {
			background-image: url(<?php echo ICreateCore::get_extension_url(); ?>assets/img/creo-login.png);
			background-position: bottom center;
			background-size: auto;
			width: auto!important;
		}

		body.login { background:#e0e0e0; }

		#login { width: 400px; }

		.login h1 {
			background: #fff;
			padding: 20px;
		}

		.login h1 a { margin-bottom: 0!important; }

		.login form {
			margin-top: 0;
			padding: 0px 48px 20px;
			border:none;
			-webkit-box-shadow:none;
			box-shadow:none;
		}

		.login form .forgetmenot {
			float: none;
			text-align: center;
		}

		input[type=text], input[type=password] {
			color: #555;
			border:none;
			-webkit-box-shadow:none;
			box-shadow:none;
			background-color:#f0f0f0!important;
			background: #f0f0f0!important;
			font-size: 18px;
			padding: 4px;
		}

		.login #login .button-primary {
			height: 48px;
			line-height: 0;
			background-image: none;
			background: #2a2a2a;
			border: none;
			border-radius: 0;
			text-shadow: none;
			box-shadow: none;
			width: 100%;
			margin-top: 20px;
		}

		.login  .button-primary:hover {
			background: #2a2a2a;
			border: none;
			border-radius: 0;
			text-shadow: none;
			box-shadow: none;
		 }

		.login #backtoblog, .login #nav {
			background: #FFF;
			font-size: 12px;
			margin: 0;
			padding: 0 0 15px 0;
			text-align: center;
		}

		.login #backtoblog a:hover, .login #nav a:hover {
			color: #00ef9d;
		}

		.login .message {
			border-left: 4px solid #00ef9d;
		}
	</style>
<?php }
add_action( 'login_enqueue_scripts', 'icreate_core_login_styles' );

function icreate_core_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'icreate_core_login_logo_url' );

function icreate_core_login_logo_url_title() {
	return get_bloginfo();
}
add_filter( 'login_headertitle', 'icreate_core_login_logo_url_title' );



// function custom_admin_logo() {
// 	echo '<style type="text/css">
// 		#wp-admin-bar-wp-logo { background: url(' . ICreateCore::get_extension_url() . 'assets/img/creo-icon.png) center center no-repeat !important; background-size: 80% auto !important; }
// 		#wp-admin-bar-wp-logo .ab-icon { visibility: hidden; }
// 		#wpadminbar .ab-top-menu>li:hover>.ab-item { background-color: rgba(51,51,51, 0.5); }
// 	</style>';
// }
// add_action('admin_head', 'custom_admin_logo');



function icreate_core_admin_footer_text () {
	echo '<img src="' . ICreateCore::get_extension_url() . 'assets/img/creo-dashboard.png">';
}
add_filter('admin_footer_text', 'icreate_core_admin_footer_text');