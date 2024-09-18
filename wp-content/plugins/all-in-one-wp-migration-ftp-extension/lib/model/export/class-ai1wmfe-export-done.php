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

class Ai1wmfe_Export_Done {

	public static function execute( $params ) {

		$model = new Ai1wmfe_Settings;

		// Set progress
		Ai1wm_Status::done(
			sprintf( __( '%s', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ) ),
			sprintf( __( 'Your WordPress archive has been uploaded to the %s server.', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ) )
		);

		// Send notification
		Ai1wm_Notification::ok(
			sprintf( __( 'Backup to %s has completed (%s)', AI1WMFE_PLUGIN_NAME ), strtoupper( $model->get_type() ), parse_url( site_url(), PHP_URL_HOST ) . parse_url( site_url(), PHP_URL_PATH ) ),
			sprintf( __( '<p>Your site %s was successfully exported to %s.</p>', AI1WMFE_PLUGIN_NAME ), site_url(), strtoupper( $model->get_type() ) ) .
			sprintf( __( '<p>Date: %s</p>', AI1WMFE_PLUGIN_NAME ), date_i18n( 'r' ) ) .
			sprintf( __( '<p>Backup file: %s</p>', AI1WMFE_PLUGIN_NAME ), ai1wm_archive_name( $params ) ) .
			sprintf( __( '<p>Size: %s</p>', AI1WMFE_PLUGIN_NAME ), ai1wm_archive_size( $params ) )
		);

		do_action( 'ai1wm_status_export_done', $params );

		return $params;
	}
}
