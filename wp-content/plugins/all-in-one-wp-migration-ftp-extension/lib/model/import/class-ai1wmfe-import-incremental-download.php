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

class Ai1wmfe_Import_Incremental_Download {

	public static function execute( $params, Ai1wmfe_FTP_Factory $ftp = null ) {

		$params['completed'] = false;

		// Validate file index
		if ( ! isset( $params['file_index'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'FTP File Index is not specified.', AI1WMFE_PLUGIN_NAME ) );
		}

		// Validate file/folder path
		if ( ! isset( $params['file_path'], $params['folder_path'] ) ) {
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

		// Set file range start
		if ( ! isset( $params['file_range_start'] ) ) {
			$params['file_range_start'] = 0;
		}

		// Set incremental index
		if ( ! isset( $params['incremental_index'] ) ) {
			$params['incremental_index'] = 0;
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
					$ftp->download_file_chunk( $archive, $params['file_path'], $params['file_range_start'], $file_chunk_size );

					// Unset download retries
					unset( $params['download_retries'] );
					unset( $params['download_backoff'] );

				} catch ( Ai1wmfe_Connect_Exception $e ) {
					if ( $params['download_retries'] <= 3 ) {
						sleep( $params['download_backoff'] );

						return $params;
					}

					throw $e;
				}
			}

			fflush( $archive );

			// We need to calculate how many bytes are actually downloaded
			$total_downloaded = ai1wm_archive_bytes( $params );
			$file_chunk_size  = $total_downloaded - $params['archive_offset'];

			// Set archive offset
			$params['archive_offset'] = $total_downloaded;

			// Set file range start
			$params['file_range_start'] = min( $params['file_range_start'] + $file_chunk_size, $params['file_size'] - 1 );

			// Get progress
			$progress = (int) ( ( $params['archive_offset'] / $params['total_backups_files_size'] ) * 100 );

			// Set progress
			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::log( sprintf( __( 'Downloading %s (%s) [%d%% complete]', AI1WMFE_PLUGIN_NAME ), $params['file_path'], $params['file_size'], $progress ) );
			} else {
				Ai1wm_Status::progress( $progress );
			}

			// Completed?
			if ( $params['file_range_start'] === ( $params['file_size'] - 1 ) ) {

				$incremental_files = array();

				// Get incremental files
				if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'rb' ) ) ) {
					while ( list( $file_index, $file_path, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
						$incremental_files[ $file_index ] = array( $file_path, $file_size, $file_mtime );
					}

					ai1wm_close( $incremental_list );
				}

				// Unset file parameters
				if ( ++$params['incremental_index'] <= $params['file_index'] ) {
					if ( isset( $incremental_files[ $params['incremental_index'] ][0] ) ) {
						$params['file_path'] = sprintf( '%s/%s', $params['folder_path'], $incremental_files[ $params['incremental_index'] ][0] );
					}

					if ( isset( $incremental_files[ $params['incremental_index'] ][1] ) ) {
						$params['file_size'] = $incremental_files[ $params['incremental_index'] ][1];
					}

					if ( isset( $params['archive_offset'] ) ) {
						$params['archive_offset'] -= 4377;
					}

					unset( $params['file_range_start'] );
				} else {
					unset( $params['file_path'] );
					unset( $params['file_size'] );
					unset( $params['file_range_start'] );
					unset( $params['archive_offset'] );
					unset( $params['incremental_index'] );
					unset( $params['completed'] );
				}
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

			$incremental_files = array();

			// Get incremental files
			if ( ( $incremental_list = ai1wm_open( ai1wm_incremental_backups_list_path( $params ), 'rb' ) ) ) {
				while ( list( $file_index, $file_path, $file_size, $file_mtime ) = fgetcsv( $incremental_list ) ) {
					$incremental_files[ $file_index ] = array( $file_path, $file_size, $file_mtime );
				}

				ai1wm_close( $incremental_list );
			}

			// Unset file parameters
			if ( ++$params['incremental_index'] <= $params['file_index'] ) {
				if ( isset( $incremental_files[ $params['incremental_index'] ][0] ) ) {
					$params['file_path'] = sprintf( '%s/%s', $params['folder_path'], $incremental_files[ $params['incremental_index'] ][0] );
				}

				if ( isset( $incremental_files[ $params['incremental_index'] ][1] ) ) {
					$params['file_size'] = $incremental_files[ $params['incremental_index'] ][1];
				}

				if ( isset( $params['archive_offset'] ) ) {
					$params['archive_offset'] -= 4377;
				}

				unset( $params['file_range_start'] );
			} else {
				unset( $params['file_path'] );
				unset( $params['file_size'] );
				unset( $params['file_range_start'] );
				unset( $params['archive_offset'] );
				unset( $params['incremental_index'] );
				unset( $params['completed'] );
			}
		}

		return $params;
	}
}
