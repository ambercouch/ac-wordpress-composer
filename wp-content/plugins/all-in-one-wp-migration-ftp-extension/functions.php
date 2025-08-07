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

/**
 * Check whether export/import is running
 *
 * @return boolean
 */
function ai1wmfe_is_running() {
	if ( isset( $_GET['ftp'] ) || isset( $_POST['ftp'] ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether export/import is incremental
 *
 * @return boolean
 */
function ai1wmfe_is_incremental() {
	if ( isset( $_GET['ftp'], $_GET['incremental'] ) || isset( $_POST['ftp'], $_POST['incremental'] ) ) {
		return true;
	}

	return false;
}

/**
 * Raise number into power
 * Fix for PHP (8.4.1, 8.3.14 and 8.2.26) bug with gmp_pow
 *
 * @param $num
 * @param $exponent
 *
 * @return \GMP|mixed|resource
 */
function ai1wmfe_gmp_pow( $num, $exponent ) {
	$buggy_versions = array( 80401, 80314, 80226 );

	if ( in_array( PHP_VERSION_ID, $buggy_versions, true ) ) {
		return $num ** $exponent;
	}

	return gmp_pow( $num, $exponent );
}
