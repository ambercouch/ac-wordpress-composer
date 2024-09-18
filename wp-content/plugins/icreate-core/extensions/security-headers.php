<?php
/**
 * Plugin Name: Security headers
 * Description: Adds 3 security related headers
 * Version: 1.0.0
 */



function icreate_core_security_headers() {
	//Provides Clickjacking protection. Values: deny - no rendering within a frame, sameorigin - no rendering if origin mismatch, allow-from: DOMAIN - allow rendering if framed by frame loaded from DOMAIN
    header('X-Frame-Options: SAMEORIGIN');
    // 	This header enables the Cross-site scripting (XSS) filter built into most recent web browsers. It's usually enabled by default anyway, so the role of this header is to re-enable the filter for this particular website if it was disabled by the user. This header is supported in IE 8+, and in Chrome (not sure which versions). The anti-XSS filter was added in Chrome 4. Its unknown if that version honored this header. 
    header('X-XSS-Protection: 1; mode=block');
    //The only defined value, "nosniff", prevents Internet Explorer and Google Chrome from MIME-sniffing a response away from the declared content-type. This also applies to Google Chrome, when downloading extensions. This reduces exposure to drive-by download attacks and sites serving user uploaded content that, by clever naming, could be treated by MSIE as executable or dynamic HTML files. 
    header('X-Content-Type-Options: nosniff');
}

add_action( 'send_headers', 'icreate_core_security_headers', 1 );