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

if ( defined( 'WP_CLI' ) && class_exists( 'Ai1wm_Backup_WP_CLI_Base' ) ) {
	class Ai1wmfe_FTP_WP_CLI_Command extends Ai1wm_Backup_WP_CLI_Base {
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Creates a new backup and uploads to FTP.
		 *
		 * ## OPTIONS
		 *
		 * [--sites[=<comma_separated_ids>]]
		 * : Export sites by id (Multisite only). To list sites use: wp site list --fields=blog_id,url
		 *
		 * [--password[=<password>]]
		 * : Encrypt backup with password
		 *
		 * [--exclude-spam-comments]
		 * : Do not export spam comments
		 *
		 * [--exclude-post-revisions]
		 * : Do not export post revisions
		 *
		 * [--exclude-media]
		 * : Do not export media library (files)
		 *
		 * [--exclude-themes]
		 * : Do not export themes (files)
		 *
		 * [--exclude-inactive-themes]
		 * : Do not export inactive themes (files)
		 *
		 * [--exclude-muplugins]
		 * : Do not export must-use plugins (files)
		 *
		 * [--exclude-plugins]
		 * : Do not export plugins (files)
		 *
		 * [--exclude-inactive-plugins]
		 * : Do not export inactive plugins (files)
		 *
		 * [--exclude-cache]
		 * : Do not export cache (files)
		 *
		 * [--exclude-database]
		 * : Do not export database (sql)
		 *
		 * [--exclude-tables[=<comma_separated_names>]]
		 * : Do not export selected database tables (sql)
		 *
		 * [--exclude-email-replace]
		 * : Do not replace email domain (sql)
		 *
		 * [--replace]
		 * : Find and replace text in the database
		 *
		 * [<find>...]
		 * : A string to search for within the database
		 *
		 * [<replace>...]
		 * : Replace instances of the first string with this new string
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm ftp backup --replace "wp" "WordPress"
		 * Backup in progress...
		 * FTP: Uploading wordpress-20181109-092410-450.wpress (17 MB) [29% complete]
		 * FTP: Uploading wordpress-20181109-092410-450.wpress (17 MB) [59% complete]
		 * FTP: Uploading wordpress-20181109-092410-450.wpress (17 MB) [89% complete]
		 * FTP: Uploading wordpress-20181109-092410-450.wpress (17 MB) [100% complete]
		 * Backup complete.
		 * Backup file: wordpress-20181109-082635-610.wpress
		 * Backup location: ftp://ftp.mysite.com/mysite.com/wordpress-20181109-082635-610.wpress
		 * @subcommand backup
		 */
		public function backup( $args = array(), $assoc_args = array() ) {
			$params = $this->run_backup(
				$this->build_export_params( $args, $assoc_args )
			);

			WP_CLI::log( sprintf( __( 'Backup location: %s', AI1WMFE_PLUGIN_NAME ), $this->get_backup_uri( $params ) ) );
		}

		/**
		 * Get a list of FTP backup files.
		 *
		 * ## OPTIONS
		 *
		 * [--folder-path=<path>]
		 * : List backups in a specific FTP subfolder
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm ftp list-backups
		 * +------------------------------------------------+--------------+-----------+
		 * | Backup name                                    | Date created | Size      |
		 * +------------------------------------------------+--------------+-----------+
		 * | migration-wp-20170908-152313-435.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152103-603.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152036-162.wpress        | 4 days ago   | 536.77 MB |
		 * +------------------------------------------------+--------------+-----------+
		 *
		 * $ wp ai1wm ftp list-backups --folder-path=/backups/daily
		 * +------------------------------------------------+--------------+-----------+
		 * | Backup name                                    | Date created | Size      |
		 * +------------------------------------------------+--------------+-----------+
		 * | migration-wp-20170908-152313-435.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152103-603.wpress        | 4 days ago   | 536.77 MB |
		 * +------------------------------------------------+--------------+-----------+
		 *
		 * @subcommand list-backups
		 */
		public function list_backups( $args = array(), $assoc_args = array() ) {
			$backups = new cli\Table;

			$backups->setHeaders(
				array(
					'name' => __( 'Backup name', AI1WMFE_PLUGIN_NAME ),
					'date' => __( 'Date created', AI1WMFE_PLUGIN_NAME ),
					'size' => __( 'Size', AI1WMFE_PLUGIN_NAME ),
				)
			);

			$folder_path = $this->get_folder_path( $assoc_args );
			$items       = $this->list_items( $folder_path );

			// Set folder structure
			$response = array( 'items' => array(), 'num_hidden_files' => 0 );

			foreach ( $items as $item ) {
				if ( pathinfo( $item['name'], PATHINFO_EXTENSION ) === 'wpress' ) {
					$backups->addRow(
						array(
							'name' => $item['name'],
							'date' => sprintf( __( '%s ago', AI1WMFE_PLUGIN_NAME ), human_time_diff( $item['date'] ) ),
							'size' => ai1wm_size_format( $item['bytes'], 2 ),
						)
					);
				}
			}

			$backups->display();
		}

		/**
		 * Restores a backup from FTP.
		 *
		 * ## OPTIONS
		 *
		 * <file>
		 * : Name of the backup file
		 *
		 * [--folder-path=<path>]
		 * : Download a backup from a specific FTP subfolder
		 *
		 * [--yes]
		 * : Automatically confirm the restore operation
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm ftp restore migration-wp-20170913-095743-931.wpress
		 * Restore in progress...
		 * Restore complete.
		 *
		 * $ wp ai1wm ftp restore migration-wp-20170913-095743-931.wpress --folder-path /backups/daily
		 * @subcommand restore
		 */
		public function restore( $args = array(), $assoc_args = array() ) {
			if ( ! isset( $args[0] ) ) {
				WP_CLI::error_multi_line(
					array(
						__( 'A backup name must be provided in order to proceed with the restore process.', AI1WMFE_PLUGIN_NAME ),
						__( 'Example: wp ai1wm ftp restore migration-wp-20170913-095743-931.wpress', AI1WMFE_PLUGIN_NAME ),
					)
				);
				exit;
			}

			$folder_path = $this->get_folder_path( $assoc_args );
			$items       = $this->list_items( $folder_path );

			$file = null;
			foreach ( $items as $item ) {
				if ( $item['name'] === $args[0] ) {
					$file = $item;
					break;
				}
			}

			if ( is_null( $file ) ) {
				WP_CLI::error_multi_line(
					array(
						__( "The backup file could not be located in $folder_path folder.", AI1WMFE_PLUGIN_NAME ),
						__( 'To list available backups use: wp ai1wm ftp list-backups', AI1WMFE_PLUGIN_NAME ),
					)
				);
				exit;
			}

			$params = array(
				'archive'    => $args[0],
				'storage'    => ai1wm_storage_folder(),
				'file_path'  => $file['path'],
				'file_size'  => $file['bytes'],
				'cli_args'   => $assoc_args,
				'secret_key' => get_option( AI1WM_SECRET_KEY, false ),
			);

			WP_CLI::log( 'Restore in progress...' );

			try {

				// Disable completed timeout
				add_filter( 'ai1wm_completed_timeout', '__return_zero' );

				// Run import filters
				$params = Ai1wm_Import_Controller::import( $params );

			} catch ( Exception $e ) {
				WP_CLI::error( __( sprintf( 'Unable to import: %s', $e->getMessage() ), AI1WM_PLUGIN_NAME ) );
				exit;
			}

			WP_CLI::log( 'Restore complete.' );
		}

		/**
		 * Get backup items list
		 *
		 * @param  string $folder_path Folder path where backups located
		 * @return array  Backup items
		 */
		protected function list_items( $folder_path ) {
			// Set FTP client
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

			try {
				$items = $ftp->list_folder( $folder_path );
				Ai1wmve_File_Sorter::sort( $items, Ai1wmve_File_Sorter::by_date_desc() );
			} catch ( Exception $e ) {
				WP_CLI::error( $e->getMessage() );
				exit;
			}

			return $items;
		}

		/**
		 * Get folder path from command-line or WP settings
		 *
		 * @param  array  $assoc_args CLI params
		 * @return string Folder path
		 */
		protected function get_folder_path( $assoc_args ) {
			if ( isset( $assoc_args['folder-path'] ) ) {
				return sprintf( '/%s', trim( $assoc_args['folder-path'], '/' ) );
			}
			return ai1wm_archive_folder();
		}

		/**
		 * Get backup file location URL
		 *
		 * @param  array  $params Params
		 * @return string Absolute URL to the resulting backup file location
		 */
		protected function get_backup_uri( $params ) {
			// Set file path
			$folder_path = get_option( 'ai1wmfe_ftp_directory', '' );
			$folder_path = trim( sprintf( '%s/%s', $folder_path, ai1wm_archive_folder() ), '/' );
			$file_path   = trim( sprintf( '%s/%s', $folder_path, ai1wm_archive_name( $params ) ), '/' );

			return sprintf(
				'%s://%s:%d/%s',
				get_option( 'ai1wmfe_ftp_type', AI1WMFE_FTP_DEFAULT_TYPE ),
				get_option( 'ai1wmfe_ftp_hostname', false ),
				get_option( 'ai1wmfe_ftp_port', AI1WMFE_FTP_DEFAULT_PORT ),
				$file_path
			);
		}
	}
}
