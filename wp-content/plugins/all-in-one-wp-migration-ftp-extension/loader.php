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

// Include all the files that you want to load in here
if ( defined( 'WP_CLI' ) ) {
	require_once AI1WMFE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'ai1wm-wp-cli.php';

	require_once AI1WMFE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'class-ai1wmfe-ftp-wp-cli-command.php';

	require_once AI1WMFE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'class-ai1wmfe-ftp-wp-cli-incremental-command.php';
}

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'servmask' .
			DIRECTORY_SEPARATOR .
			'pro' .
			DIRECTORY_SEPARATOR .
			'ai1wmve.php';

require_once AI1WMFE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-main-controller.php';

require_once AI1WMFE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-controller.php';

require_once AI1WMFE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-controller.php';

require_once AI1WMFE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-settings-controller.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-ftp.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-upload.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-retention.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-done.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-incremental-content.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-incremental-media.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-incremental-plugins.php';

require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-incremental-themes.php';


require_once AI1WMFE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-export-incremental-backups.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-ftp.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-download.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-database.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-settings.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-incremental-ftp.php';

require_once AI1WMFE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-import-incremental-download.php';

require_once AI1WMFE_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-settings.php';

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'ftp-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-ftp-interface.php';

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'ftp-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-ftp-curl.php';

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'ftp-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-ftp-extension.php';

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'ftp-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-ftp-factory.php';

require_once AI1WMFE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'ftp-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmfe-sftp-client.php';

if ( ! class_exists( 'Crypt_RSA' ) ) {
	require_once AI1WMFE_PHPSECLIB_PATH .
				DIRECTORY_SEPARATOR .
				'Crypt' .
				DIRECTORY_SEPARATOR .
				'RSA.php';
}

if ( ! class_exists( 'Net_SFTP' ) ) {
	require_once AI1WMFE_PHPSECLIB_PATH .
				DIRECTORY_SEPARATOR .
				'Net' .
				DIRECTORY_SEPARATOR .
				'SFTP.php';
}
