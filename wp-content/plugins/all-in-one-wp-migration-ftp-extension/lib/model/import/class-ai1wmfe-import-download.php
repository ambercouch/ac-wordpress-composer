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

class Ai1wmfe_Import_Download {

	public static function execute( $params, Ai1wmfe_FTP_Factory $ftp = null ) {

		$params['completed'] = false;

		// Validate file path
		if ( ! isset( $params['file_path'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'FTP File Path is not specified.', AI1WMFE_PLUGIN_NAME ) );
		}

		// Validate file size
		if ( ! isset( $params['file_size'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'FTP File Size is not specified.', AI1WMFE_PLUGIN_NAME ) );
		}

		// Set archive offset
		if ( ! isset( $params['archive_offset'] ) ) {
			$params['archive_offset'] = 0;
		}

		// Set download retries
		if ( ! isset( $params['download_retries'] ) ) {
			$params['download_retries'] = 0;
		}

		// Set download backoff
		if ( ! isset( $params['download_backoff'] ) ) {
			$params['download_backoff'] = 1;
		}

		// Set file chunk size for download
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

		// Check whether FTP server supports resumable downloads?
		if ( get_option( 'ai1wmfe_ftp_append', false ) ) {

			// Open the archive file for writing
			$archive = fopen( ai1wm_archive_path( $params ), 'cb' );

			// Write file chunk data
			if ( ( fseek( $archive, $params['archive_offset'] ) !== -1 ) ) {
				try {

					$params['download_retries'] += 1;
					$params['download_backoff'] *= 2;

					// Download file chunk data
					$ftp->download_file_chunk( $archive, $params['file_path'], $params['archive_offset'], $file_chunk_size );

					// Unset download retries
					unset( $params['download_retries'] );
					unset( $params['download_backoff'] );

				} catch ( Ai1wmfe_Connect_Exception $e ) {
					sleep( $params['download_backoff'] );
					if ( $params['download_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}
			}

			fflush( $archive );

			// Set archive offset
			$params['archive_offset'] = ai1wm_archive_bytes( $params );

			// Get progress
			$progress = (int) ( ( $params['archive_offset'] / $params['file_size'] ) * 100 );

			// Set progress
			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( __( 'Downloading %s (%s) [%d%% complete]', AI1WMFE_PLUGIN_NAME ), $params['file_path'], $params['file_size'], $progress ) );
			} else {
				Ai1wm_Status::progress( $progress );
			}

			// Completed?
			if ( $params['archive_offset'] === (int) $params['file_size'] ) {

				// Unset file path
				unset( $params['file_path'] );

				// Unset file size
				unset( $params['file_size'] );

				// Unset archive offset
				unset( $params['archive_offset'] );

				// Unset completed
				unset( $params['completed'] );
			}

			// Close the archive file
			fclose( $archive );

		} else {

			$model = new Ai1wmfe_Settings;

			// Set progress
			Ai1wm_Status::info( sprintf( __( 'Downloading from %s server...', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ) ) );

			try {

				$params['download_retries'] += 1;
				$params['download_backoff'] *= 2;

				// Download file data
				$ftp->download_file( ai1wm_archive_path( $params ), $params['file_path'] );

				// Unset download retries
				unset( $params['download_retries'] );
				unset( $params['download_backoff'] );

			} catch ( Ai1wmfe_Connect_Exception $e ) {
				sleep( $params['download_backoff'] );
				if ( $params['download_retries'] <= 3 ) {
					return $params;
				}

				throw $e;
			}

			// Unset file path
			unset( $params['file_path'] );

			// Unset file size
			unset( $params['file_size'] );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset completed
			unset( $params['completed'] );
		}

		return $params;
	}
}
