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

class Ai1wmfe_Import_Database {

	public static function execute( $params ) {

		$model = new Ai1wmfe_Settings;

		// Set progress
		Ai1wm_Status::info( __( 'Updating FTP settings...', AI1WMFE_PLUGIN_NAME ) );

		// Read settings.json file
		$handle = ai1wm_open( ai1wm_settings_path( $params ), 'r' );

		// Parse settings.json file
		$settings = ai1wm_read( $handle, filesize( ai1wm_settings_path( $params ) ) );
		$settings = json_decode( $settings, true );

		// Close handle
		ai1wm_close( $handle );

		// Update FTP settings
		$model->set_cron_timestamp( $settings['ai1wmfe_ftp_cron_timestamp'] );
		$model->set_cron( $settings['ai1wmfe_ftp_cron'] );
		$model->set_type( $settings['ai1wmfe_ftp_type'] );
		$model->set_hostname( $settings['ai1wmfe_ftp_hostname'] );
		$model->set_username( $settings['ai1wmfe_ftp_username'] );
		$model->set_password( $settings['ai1wmfe_ftp_password'] );
		$model->set_authentication( $settings['ai1wmfe_ftp_authentication'] );
		$model->set_key( $settings['ai1wmfe_ftp_key'] );
		$model->set_passphrase( $settings['ai1wmfe_ftp_passphrase'] );
		$model->set_directory( $settings['ai1wmfe_ftp_directory'] );
		$model->set_port( $settings['ai1wmfe_ftp_port'] );
		$model->set_active( $settings['ai1wmfe_ftp_active'] );
		$model->set_connection( $settings['ai1wmfe_ftp_connection'] );
		$model->set_append( $settings['ai1wmfe_ftp_append'] );
		$model->set_backups( $settings['ai1wmfe_ftp_backups'] );
		$model->set_total( $settings['ai1wmfe_ftp_total'] );
		$model->set_days( $settings['ai1wmfe_ftp_days'] );
		$model->set_file_chunk_size( $settings['ai1wmfe_ftp_file_chunk_size'] );
		$model->set_notify_ok_toggle( $settings['ai1wmfe_ftp_notify_toggle'] );
		$model->set_notify_error_toggle( $settings['ai1wmfe_ftp_notify_error_toggle'] );
		$model->set_notify_error_subject( $settings['ai1wmfe_ftp_notify_error_subject'] );
		$model->set_notify_email( $settings['ai1wmfe_ftp_notify_email'] );

		// Set progress
		Ai1wm_Status::info( __( 'Done updating FTP settings.', AI1WMFE_PLUGIN_NAME ) );

		return $params;
	}
}
