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

class Ai1wmfe_Export_Upload {

	public static function execute( $params, Ai1wmfe_FTP_Factory $ftp = null ) {

		$params['completed'] = false;

		// Set archive offset
		if ( ! isset( $params['archive_offset'] ) ) {
			$params['archive_offset'] = 0;
		}

		// Set archive size
		if ( ! isset( $params['archive_size'] ) ) {
			$params['archive_size'] = ai1wm_archive_bytes( $params );
		}

		// Set upload retries
		if ( ! isset( $params['upload_retries'] ) ) {
			$params['upload_retries'] = 0;
		}

		// Set upload backoff
		if ( ! isset( $params['upload_backoff'] ) ) {
			$params['upload_backoff'] = 1;
		}

		// Set file chunk size for upload
		$file_chunk_size = get_option( 'ai1wmfe_ftp_file_chunk_size', AI1WMFE_DEFAULT_FILE_CHUNK_SIZE );

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

		if ( ai1wmfe_is_incremental() ) {
			$remote_file = sprintf( '%s/incremental-backups/%s', ai1wm_archive_folder(), ai1wm_archive_name( $params ) );
		} else {
			$remote_file = sprintf( '%s/%s', ai1wm_archive_folder(), ai1wm_archive_name( $params ) );
		}

		// Check whether FTP server supports resumable uploads?
		if ( get_option( 'ai1wmfe_ftp_append', false ) ) {

			// Open the archive file for reading
			$archive = fopen( ai1wm_archive_path( $params ), 'rb' );

			// Read file chunk data
			if ( ( fseek( $archive, $params['archive_offset'] ) !== -1 )
					&& ( $file_chunk_data = fread( $archive, $file_chunk_size ) ) ) {

				try {

					$params['upload_retries'] += 1;
					$params['upload_backoff'] *= 2;

					// Upload file chunk data
					$ftp->upload_file_chunk( $file_chunk_data, $remote_file, $params['archive_offset'] );

					// Unset upload retries
					unset( $params['upload_retries'] );
					unset( $params['upload_backoff'] );

				} catch ( Ai1wmfe_Connect_Exception $e ) {
					sleep( $params['upload_backoff'] );
					if ( $params['upload_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}

				// Set archive offset
				$params['archive_offset'] = ftell( $archive );

				// Set archive details
				$name = ai1wm_archive_name( $params );
				$size = ai1wm_archive_size( $params );

				// Get progress
				$progress = (int) ( ( $params['archive_offset'] / $params['archive_size'] ) * 100 );

				// Set progress
				if ( defined( 'WP_CLI' ) ) {
					WP_CLI::log( sprintf( __( 'Uploading %s (%s) [%d%% complete]', AI1WMFE_PLUGIN_NAME ), $name, $size, $progress ) );
				} else {
					Ai1wm_Status::info( sprintf( __( '<i class="ai1wmfe-icon-ftp"></i> Uploading <strong>%s</strong> (%s)<br />%d%% complete', AI1WMFE_PLUGIN_NAME ), $name, $size, $progress ) );
				}
			} else {

				// Set last backup date
				update_option( 'ai1wmfe_ftp_timestamp', time() );

				// Unset archive offset
				unset( $params['archive_offset'] );

				// Unset archive size
				unset( $params['archive_size'] );

				// Unset completed
				unset( $params['completed'] );
			}

			// Close the archive file
			fclose( $archive );

		} else {

			$model = new Ai1wmfe_Settings;

			// Set progress
			Ai1wm_Status::info( sprintf( __( 'Uploading to %s server...', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ) ) );

			try {

				$params['upload_retries'] += 1;
				$params['upload_backoff'] *= 2;

				// Upload file data
				$ftp->upload_file( ai1wm_archive_path( $params ), $remote_file, $params['archive_size'] );

				// Unset upload retries
				unset( $params['upload_retries'] );
				unset( $params['upload_backoff'] );

			} catch ( Ai1wmfe_Connect_Exception $e ) {
				sleep( $params['upload_backoff'] );
				if ( $params['upload_retries'] <= 3 ) {
					return $params;
				}

				throw $e;
			}

			// Set last backup date
			update_option( 'ai1wmfe_ftp_timestamp', time() );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset archive size
			unset( $params['archive_size'] );

			// Unset completed
			unset( $params['completed'] );
		}

		return $params;
	}
}
