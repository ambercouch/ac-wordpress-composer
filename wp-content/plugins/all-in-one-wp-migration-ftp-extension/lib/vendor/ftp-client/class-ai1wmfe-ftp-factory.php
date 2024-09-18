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

class Ai1wmfe_FTP_Factory {

	/**
	 * Create the FTP client depending on the type
	 *
	 * @param  string  $type           Connection type
	 * @param  string  $hostname       Server hostname
	 * @param  string  $username       Client username
	 * @param  string  $password       Client password
	 * @param  string  $authentication Client authentication
	 * @param  string  $key            Client private key
	 * @param  string  $passphrase     Client private key passphrase
	 * @param  string  $directory      Server directory
	 * @param  integer $port           Server port
	 * @param  boolean $active         Server active mode
	 * @return object                  The newly created client
	 */
	public static function create( $type, $hostname, $username, $password, $authentication = 'password', $key = null, $passphrase = null, $directory = '/', $port = 21, $active = false ) {
		switch ( $type ) {
			case 'ftp':
				if ( extension_loaded( 'curl' ) ) {
					return new Ai1wmfe_FTP_Curl( $hostname, $username, $password, $directory, $port, $active, false );
				} else {
					return new Ai1wmfe_FTP_Extension( $hostname, $username, $password, $directory, $port, $active, false );
				}

			case 'ftps':
				if ( extension_loaded( 'curl' ) ) {
					return new Ai1wmfe_FTP_Curl( $hostname, $username, $password, $directory, $port, $active, true );
				} else {
					return new Ai1wmfe_FTP_Extension( $hostname, $username, $password, $directory, $port, $active, true );
				}

			case 'sftp':
				return new Ai1wmfe_SFTP_Client( $hostname, $username, $password, $authentication, $key, $passphrase, $directory, $port );

			default:
				return new Ai1wmfe_Error_Exception( sprintf( __( 'You have supplied an invalid FTP factory type = %s', AI1WMFE_PLUGIN_NAME ), $type ) );
		}
	}
}
