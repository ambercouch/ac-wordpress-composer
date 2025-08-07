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

class Ai1wmfe_Import_Incremental_FTP {

	public static function execute( $params, Ai1wmfe_FTP_Factory $ftp = null ) {

		// Set progress
		Ai1wm_Status::info( __( 'Preparing incremental backup files...', AI1WMFE_PLUGIN_NAME ) );

		// Validate file path
		if ( ! isset( $params['file_path'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'FTP File Path is not specified.', AI1WMFE_PLUGIN_NAME ) );
		}

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

		$params['folder_path'] = dirname( $params['file_path'] );

		// Download incremental files
		try {
			$ftp->download_file( ai1wm_incremental_backups_list_path( $params ), sprintf( '%s/incremental.backups.list', $params['folder_path'] ) );
		} catch ( Ai1wmfe_Error_Exception $e ) {
		}

		$incremental_files = array();

		// Get incremental files
		if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'rb' ) ) ) {
			while ( list( $file_index, $file_path, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
				$incremental_files[ $file_index ] = array( $file_path, $file_size, $file_mtime );
			}

			ai1wm_close( $incremental_list );
		}

		$total_backups_files_size = 1;

		// Get total backups files size
		if ( isset( $params['file_index'] ) ) {
			for ( $i = 0; $i <= $params['file_index']; $i++ ) {
				$total_backups_files_size += $incremental_files[ $i ][1];
			}
		}

		// Set total backups files size
		$params['total_backups_files_size'] = $total_backups_files_size;

		// Set file path
		$params['file_path'] = sprintf( '%s/%s', $params['folder_path'], $incremental_files[0][0] );

		// Set file size
		$params['file_size'] = $incremental_files[0][1];

		// Set progress
		Ai1wm_Status::info( __( 'Done preparing incremental backup files.', AI1WMFE_PLUGIN_NAME ) );

		return $params;
	}
}
