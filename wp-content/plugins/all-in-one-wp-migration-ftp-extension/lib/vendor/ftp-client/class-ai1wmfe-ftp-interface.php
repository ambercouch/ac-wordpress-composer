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

interface Ai1wmfe_FTP_Interface {

	/**
	 * Create folder
	 *
	 * @param  string $folder_path Folder path
	 * @return boolean
	 */
	public function create_folder( $folder_path );

	/**
	 * Retrieve files and folders metadata
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function list_folder( $folder_path );

	/**
	 * Retrieve files and folders raw metadata
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function raw_list_folder( $folder_path );

	/**
	 * Upload file
	 *
	 * @param  string  $local_file_path  Local file path
	 * @param  string  $remote_file_path Remote file path
	 * @param  integer $file_size        File size
	 * @return boolean
	 */
	public function upload_file( $local_file_path, $remote_file_path, $file_size );

	/**
	 * Upload file chunk
	 *
	 * @param  string  $file_chunk_data  File chunk data
	 * @param  string  $remote_file_path Remote file path
	 * @param  integer $file_range_start File range start
	 * @return boolean
	 */
	public function upload_file_chunk( $file_chunk_data, $remote_file_path, $file_range_start = 0 );

	/**
	 * Download file
	 *
	 * @param  string  $local_file_path  Local file path
	 * @param  string  $remote_file_path Remote file path
	 * @return boolean
	 */
	public function download_file( $local_file_path, $remote_file_path );

	/**
	 * Download file chunk
	 *
	 * @param  resource $file_stream      File stream
	 * @param  string   $remote_file_path Remote file path
	 * @param  integer  $file_range_start File range start
	 * @param  integer  $file_chunk_size  File chunk size
	 * @return boolean
	 */
	public function download_file_chunk( $file_stream, $remote_file_path, $file_range_start = 0, $file_chunk_size = 0 );

	/**
	 * Remove file
	 *
	 * @param  string  $file_path File path
	 * @return boolean
	 */
	public function remove_file( $file_path );

	/**
	 * Test FTP connection
	 *
	 * @return boolean
	 */
	public function test_connection();

	/**
	 * Test FTP append file
	 *
	 * @param  string  $remote_file_path Remote file path
	 * @return boolean
	 */
	public function test_append_file( $remote_file_path = '.servmask' );
}
