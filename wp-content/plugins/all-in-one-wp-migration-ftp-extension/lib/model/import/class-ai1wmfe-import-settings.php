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

class Ai1wmfe_Import_Settings {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Getting FTP settings...', AI1WMFE_PLUGIN_NAME ) );

		$settings = array(
			'ai1wmfe_ftp_cron_timestamp'       => get_option( 'ai1wmfe_ftp_cron_timestamp', time() ),
			'ai1wmfe_ftp_cron'                 => get_option( 'ai1wmfe_ftp_cron', array() ),
			'ai1wmfe_ftp_type'                 => get_option( 'ai1wmfe_ftp_type', AI1WMFE_FTP_DEFAULT_TYPE ),
			'ai1wmfe_ftp_hostname'             => get_option( 'ai1wmfe_ftp_hostname', false ),
			'ai1wmfe_ftp_username'             => get_option( 'ai1wmfe_ftp_username', false ),
			'ai1wmfe_ftp_password'             => get_option( 'ai1wmfe_ftp_password', false ),
			'ai1wmfe_ftp_authentication'       => get_option( 'ai1wmfe_ftp_authentication', AI1WMFE_FTP_DEFAULT_AUTHENTICATION ),
			'ai1wmfe_ftp_key'                  => get_option( 'ai1wmfe_ftp_key', false ),
			'ai1wmfe_ftp_passphrase'           => get_option( 'ai1wmfe_ftp_passphrase', false ),
			'ai1wmfe_ftp_directory'            => get_option( 'ai1wmfe_ftp_directory', false ),
			'ai1wmfe_ftp_port'                 => get_option( 'ai1wmfe_ftp_port', AI1WMFE_FTP_DEFAULT_PORT ),
			'ai1wmfe_ftp_active'               => get_option( 'ai1wmfe_ftp_active', false ),
			'ai1wmfe_ftp_connection'           => get_option( 'ai1wmfe_ftp_connection', false ),
			'ai1wmfe_ftp_append'               => get_option( 'ai1wmfe_ftp_append', false ),
			'ai1wmfe_ftp_backups'              => get_option( 'ai1wmfe_ftp_backups', false ),
			'ai1wmfe_ftp_total'                => get_option( 'ai1wmfe_ftp_total', false ),
			'ai1wmfe_ftp_days'                 => get_option( 'ai1wmfe_ftp_days', false ),
			'ai1wmfe_ftp_file_chunk_size'      => get_option( 'ai1wmfe_ftp_file_chunk_size', AI1WMFE_DEFAULT_FILE_CHUNK_SIZE ),
			'ai1wmfe_ftp_notify_toggle'        => get_option( 'ai1wmfe_ftp_notify_toggle', false ),
			'ai1wmfe_ftp_notify_error_toggle'  => get_option( 'ai1wmfe_ftp_notify_error_toggle', false ),
			'ai1wmfe_ftp_notify_error_subject' => get_option( 'ai1wmfe_ftp_notify_error_subject', false ),
			'ai1wmfe_ftp_notify_email'         => get_option( 'ai1wmfe_ftp_notify_email', false ),
		);

		// Save settings.json file
		$handle = ai1wm_open( ai1wm_settings_path( $params ), 'w' );
		ai1wm_write( $handle, json_encode( $settings ) );
		ai1wm_close( $handle );

		// Set progress
		Ai1wm_Status::info( __( 'Done getting FTP settings.', AI1WMFE_PLUGIN_NAME ) );

		return $params;
	}
}
