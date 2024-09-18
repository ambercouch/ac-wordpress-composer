<?php
/**
 * Plugin Name: Enable SVG Upload
 * Description: Enables uploading a SVG file in the administration area
 * Version: 1.0.0
 */


// Allow to upload svg files
function icreate_core_upload_mime_svg( $existing_mimes=array() ) {
	// Add the svg mime type to the array to allow it for upload
	$existing_mimes['svg'] = 'image/svg+xml';
	$existing_mimes['svgz'] = 'image/svg+xml';

	return $existing_mimes;
}
add_filter('upload_mimes', 'icreate_core_upload_mime_svg');

/**
 * Removes the width and height attributes of <img> tags for SVG
 * 
 * Without this filter, the width and height are set to "1" since
 * WordPress core can't seem to figure out an SVG file's dimensions.
 * 
 * For SVG:s, returns an array with file url, width and height set 
 * to null, and false for 'is_intermediate'.
 * 
 * @wp-hook image_downsize
 * @param mixed $out Value to be filtered
 * @param int $id Attachment ID for image.
 * @return bool|array False if not in admin or not SVG. Array otherwise.
 */
function icreate_core_fix_svg_size_attributes( $out, $id ) {
	$image_url  = wp_get_attachment_url( $id );
	$file_ext   = pathinfo( $image_url, PATHINFO_EXTENSION );

	if ( ! is_admin() || 'svg' !== $file_ext ) {
		return false;
	}

	return array( $image_url, null, null, false );
}
add_filter( 'image_downsize', 'icreate_core_fix_svg_size_attributes', 10, 2 );