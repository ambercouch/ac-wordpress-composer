<?php
/**
 * Copyright (C) 2014-2020 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wmfe_Export_Incremental_Content {

	public static function execute( $params, Ai1wmfe_FTP_Factory $ftp = null ) {

		$model = new Ai1wmfe_Settings;

		// Set progress
		Ai1wm_Status::info( __( 'Preparing incremental content files...', AI1WMFE_PLUGIN_NAME ) );

		// Set FTP client
		if ( is_null( $ftp ) ) {
			$ftp = Ai1wmfe_FTP_Factory::create(
				get_option( 'ai1wmfe_ftp_type', AI1WMFE_FTP_DEFAULT_TYPE ),
				get_option( 'ai1wmfe_ftp_hostname', false ),
				get_option( 'ai1wmfe_ftp_username', false ),
				get_option( 'ai1wmfe_ftp_password', false ),
				get_option( 'ai1wmfe_ftp_authentication', AI1WMFE_FTP_DEFAULT_AUTHENTICATION ),
				get_option( 'ai1wmfe_ftp_key', false ),
				get_option( 'ai1wmfe_ftp_passphrase', false ),
				get_option( 'ai1wmfe_ftp_directory', false ),
				get_option( 'ai1wmfe_ftp_port', AI1WMFE_FTP_DEFAULT_PORT ),
				get_option( 'ai1wmfe_ftp_active', false )
			);
		}

		// Download incremental files
		try {
			$ftp->download_file( ai1wm_incremental_content_list_path( $params ), sprintf( '%s/incremental-backups/incremental.content.list', ai1wm_archive_folder() ) );
		} catch ( Ai1wmfe_Error_Exception $e ) {
		}

		$incremental_files = array();

		// Get incremental files
		if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_content_list_path( $params ), 'rb' ) ) ) {
			while ( list( $file_abspath, $file_relpath, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
				$incremental_files[ $file_abspath ][] = array( $file_relpath, $file_size, $file_mtime );
			}

			ai1wm_close( $incremental_list );
		}

		$content_files = array();

		// Get content files
		if ( ( $content_list = ai1wm_open( ai1wm_content_list_path( $params ), 'rb' ) ) ) {
			while ( list( $file_abspath, $file_relpath, $file_size, $file_mtime ) = fgetcsv( $content_list ) ) {
				$content_files[ $file_abspath ][] = array( $file_relpath, $file_size, $file_mtime );
			}

			ai1wm_close( $content_list );
		}

		// Compare incremental files
		foreach ( $incremental_files as $file_abspath => $file_attributes ) {
			if ( ! isset( $content_files[ $file_abspath ] ) ) {
				unset( $incremental_files[ $file_abspath ] );
			}
		}

		// Compare content files
		foreach ( $content_files as $file_abspath => $file_attributes ) {
			if ( isset( $incremental_files[ $file_abspath ] ) ) {
				foreach ( $file_attributes as $file_meta ) {
					if ( in_array( $file_meta, $incremental_files[ $file_abspath ] ) ) {
						unset( $content_files[ $file_abspath ] );
					}
				}
			}
		}

		// Append content files to incremental files
		$incremental_files = array_merge_recursive( $incremental_files, $content_files );

		// Write incremental files
		if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_content_list_path( $params ), 'wb' ) ) ) {
			foreach ( $incremental_files as $file_abspath => $file_attributes ) {
				foreach ( $file_attributes as $file_meta ) {
					ai1wm_putcsv( $incremental_list, array( $file_abspath, $file_meta[0], $file_meta[1], $file_meta[2] ) );
				}
			}

			ai1wm_close( $incremental_list );
		}

		$total_content_files_count = $total_content_files_size = 1;

		// Write content files
		if ( ( $content_list = ai1wm_open( ai1wm_content_list_path( $params ), 'wb' ) ) ) {
			foreach ( $content_files as $file_abspath => $file_attributes ) {
				foreach ( $file_attributes as $file_meta ) {
					if ( ai1wm_putcsv( $content_list, array( $file_abspath, $file_meta[0], $file_meta[1], $file_meta[2] ) ) !== false ) {
						$total_content_files_count++;

						// Add current file size
						$total_content_files_size += $file_meta[1];
					}
				}
			}

			ai1wm_close( $content_list );
		}

		// Set progress
		Ai1wm_Status::info( __( 'Done preparing incremental content files.', AI1WMFE_PLUGIN_NAME ) );

		// Set total content files count
		$params['total_content_files_count'] = $total_content_files_count;

		// Set total content files size
		$params['total_content_files_size'] = $total_content_files_size;

		return $params;
	}
}
