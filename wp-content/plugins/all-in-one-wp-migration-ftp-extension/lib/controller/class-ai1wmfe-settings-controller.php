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

class Ai1wmfe_Settings_Controller {

	public static function index() {
		$model = new Ai1wmfe_Settings;

		$ftp_backup_schedules  = get_option( 'ai1wmfe_ftp_cron', array() );
		$ftp_cron_timestamp    = get_option( 'ai1wmfe_ftp_cron_timestamp', time() );
		$last_backup_timestamp = get_option( 'ai1wmfe_ftp_timestamp', false );

		$last_backup_date = $model->get_last_backup_date( $last_backup_timestamp );
		$next_backup_date = $model->get_next_backup_date( $ftp_backup_schedules );

		Ai1wm_Template::render(
			'settings/index',
			array(
				'connection'           => get_option( 'ai1wmfe_ftp_connection', false ),
				'ftp_backup_schedules' => $ftp_backup_schedules,
				'ftp_cron_timestamp'   => $ftp_cron_timestamp,
				'notify_ok_toggle'     => get_option( 'ai1wmfe_ftp_notify_toggle', false ),
				'notify_error_toggle'  => get_option( 'ai1wmfe_ftp_notify_error_toggle', false ),
				'notify_email'         => get_option( 'ai1wmfe_ftp_notify_email', get_option( 'admin_email', false ) ),
				'last_backup_date'     => $last_backup_date,
				'next_backup_date'     => $next_backup_date,
				'type'                 => get_option( 'ai1wmfe_ftp_type', AI1WMFE_FTP_DEFAULT_TYPE ),
				'hostname'             => get_option( 'ai1wmfe_ftp_hostname', false ),
				'username'             => get_option( 'ai1wmfe_ftp_username', false ),
				'password'             => get_option( 'ai1wmfe_ftp_password', false ),
				'authentication'       => get_option( 'ai1wmfe_ftp_authentication', AI1WMFE_FTP_DEFAULT_AUTHENTICATION ),
				'key'                  => get_option( 'ai1wmfe_ftp_key', false ),
				'passphrase'           => get_option( 'ai1wmfe_ftp_passphrase', false ),
				'directory'            => get_option( 'ai1wmfe_ftp_directory', false ),
				'port'                 => get_option( 'ai1wmfe_ftp_port', AI1WMFE_FTP_DEFAULT_PORT ),
				'active'               => get_option( 'ai1wmfe_ftp_active', false ),
				'backups'              => get_option( 'ai1wmfe_ftp_backups', false ),
				'total'                => get_option( 'ai1wmfe_ftp_total', false ),
				'days'                 => get_option( 'ai1wmfe_ftp_days', false ),
				'file_chunk_size'      => get_option( 'ai1wmfe_ftp_file_chunk_size', AI1WMFE_DEFAULT_FILE_CHUNK_SIZE ),
			),
			AI1WMFE_TEMPLATES_PATH
		);
	}

	public static function connection( $params = array(), $files = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set files
		if ( empty( $files ) ) {
			$files = stripslashes_deep( $_FILES );
		}

		// Test FTP connection
		if ( isset( $params['ai1wmfe_ftp_link'] ) ) {
			$model = new Ai1wmfe_Settings;

			// Type
			if ( isset( $params['ai1wmfe_ftp_type'] ) ) {
				$model->set_type( trim( $params['ai1wmfe_ftp_type'] ) );
			}

			// Hostname
			if ( isset( $params['ai1wmfe_ftp_hostname'] ) ) {
				$model->set_hostname( trim( $params['ai1wmfe_ftp_hostname'] ) );
			}

			// Username
			if ( isset( $params['ai1wmfe_ftp_username'] ) ) {
				$model->set_username( trim( $params['ai1wmfe_ftp_username'] ) );
			}

			// Password
			if ( ! empty( $params['ai1wmfe_ftp_password'] ) ) {
				$model->set_password( trim( $params['ai1wmfe_ftp_password'] ) );
			}

			// Authentication
			if ( isset( $params['ai1wmfe_ftp_authentication'] ) ) {
				$model->set_authentication( trim( $params['ai1wmfe_ftp_authentication'] ) );
			}

			// Private key
			if ( is_uploaded_file( $files['ai1wmfe_ftp_key']['tmp_name'] ) ) {
				$model->set_key( file_get_contents( $files['ai1wmfe_ftp_key']['tmp_name'] ) );

				// Unset empty password
				if ( empty( $params['ai1wmfe_ftp_password'] ) ) {
					$model->set_password( trim( $params['ai1wmfe_ftp_password'] ) );
				}
			}

			// Passphrase
			if ( ! empty( $params['ai1wmfe_ftp_passphrase'] ) ) {
				$model->set_passphrase( trim( $params['ai1wmfe_ftp_passphrase'] ) );
			}

			// Directory
			if ( isset( $params['ai1wmfe_ftp_directory'] ) ) {
				$model->set_directory( trim( $params['ai1wmfe_ftp_directory'] ) );
			}

			// Port
			if ( isset( $params['ai1wmfe_ftp_port'] ) ) {
				$model->set_port( intval( $params['ai1wmfe_ftp_port'] ) );
			}

			// Active mode
			if ( isset( $params['ai1wmfe_ftp_active'] ) ) {
				$model->set_active( 1 );
			} else {
				$model->set_active( 0 );
			}

			try {

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

				// Test FTP connection
				$model->set_connection( (int) $ftp->test_connection() );

				// FTP append file
				$model->set_append( (int) $ftp->test_append_file() );

				// Set message
				Ai1wm_Message::flash( 'success', sprintf( __( '%s connection is successfully established.', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ) ) );
			} catch ( Ai1wmfe_Error_Exception $e ) {
				Ai1wm_Message::flash( 'error', $e->getMessage() );
			}
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmfe_settings' ) );
		exit;
	}

	public static function settings( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// FTP update
		if ( isset( $params['ai1wmfe_ftp_update'] ) ) {
			$model = new Ai1wmfe_Settings;

			// Cron timestamp update
			if ( ! empty( $params['ai1wmfe_ftp_cron_timestamp'] ) && ( $cron_timestamp = strtotime( $params['ai1wmfe_ftp_cron_timestamp'], current_time( 'timestamp' ) ) ) ) {
				$model->set_cron_timestamp( strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $cron_timestamp ) ) ) );
			} else {
				$model->set_cron_timestamp( time() );
			}

			// Cron update
			if ( ! empty( $params['ai1wmfe_ftp_cron'] ) ) {
				$model->set_cron( (array) $params['ai1wmfe_ftp_cron'] );
			} else {
				$model->set_cron( array() );
			}

			// Set number of backups
			if ( ! empty( $params['ai1wmfe_ftp_backups'] ) ) {
				$model->set_backups( (int) $params['ai1wmfe_ftp_backups'] );
			} else {
				$model->set_backups( 0 );
			}

			// Set size of backups
			if ( ! empty( $params['ai1wmfe_ftp_total'] ) && ! empty( $params['ai1wmfe_ftp_total_unit'] ) ) {
				$model->set_total( (int) $params['ai1wmfe_ftp_total'] . trim( $params['ai1wmfe_ftp_total_unit'] ) );
			} else {
				$model->set_total( 0 );
			}

			// Set age of backups
			if ( ! empty( $params['ai1wmfe_ftp_days'] ) ) {
				$model->set_days( (int) $params['ai1wmfe_ftp_days'] );
			} else {
				$model->set_days( 0 );
			}

			// Set file chunk size
			if ( ! empty( $params['ai1wmfe_ftp_file_chunk_size'] ) ) {
				$model->set_file_chunk_size( $params['ai1wmfe_ftp_file_chunk_size'] );
			} else {
				$model->set_file_chunk_size( AI1WMFE_DEFAULT_FILE_CHUNK_SIZE );
			}

			// Set notify ok toggle
			$model->set_notify_ok_toggle( isset( $params['ai1wmfe_ftp_notify_toggle'] ) );

			// Set notify error toggle
			$model->set_notify_error_toggle( isset( $params['ai1wmfe_ftp_notify_error_toggle'] ) );

			// Set notify email
			$model->set_notify_email( trim( $params['ai1wmfe_ftp_notify_email'] ) );

			// Set message
			Ai1wm_Message::flash( 'settings', __( 'Your changes have been saved.', AI1WMFE_PLUGIN_NAME ) );
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmfe_settings' ) );
		exit;
	}

	public static function init_cron() {
		$model = new Ai1wmfe_Settings;
		return $model->init_cron();
	}

	public static function notify_ok_toggle() {
		$model = new Ai1wmfe_Settings;
		return $model->get_notify_ok_toggle();
	}

	public static function notify_error_toggle() {
		$model = new Ai1wmfe_Settings;
		return $model->get_notify_error_toggle();
	}

	public static function notify_error_subject() {
		$model = new Ai1wmfe_Settings;
		return $model->get_notify_error_subject();
	}

	public static function notify_email() {
		$model = new Ai1wmfe_Settings;
		return $model->get_notify_email();
	}
}
