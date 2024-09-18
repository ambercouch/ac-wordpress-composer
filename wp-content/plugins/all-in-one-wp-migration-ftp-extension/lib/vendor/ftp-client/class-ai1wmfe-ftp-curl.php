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

class Ai1wmfe_FTP_Curl implements Ai1wmfe_FTP_Interface {

	/**
	 * FTP server hostname
	 *
	 * @var string
	 */
	protected $hostname = null;

	/**
	 * FTP client username
	 *
	 * @var string
	 */
	protected $username = null;

	/**
	 * FTP client password
	 *
	 * @var string
	 */
	protected $password = null;

	/**
	 * FTP server directory
	 *
	 * @var string
	 */
	protected $directory = '/';

	/**
	 * FTP server port
	 *
	 * @var integer
	 */
	protected $port = 21;

	/**
	 * FTP active mode
	 *
	 * @var boolean
	 */
	protected $active = false;

	/**
	 * FTP SSL mode
	 *
	 * @var boolean
	 */
	protected $ssl = false;

	/**
	 * FTP handler
	 *
	 * @var resource
	 */
	protected $handler = null;

	/**
	 * cURL messages
	 *
	 * @var array
	 */
	protected $messages = array(
		// [Informational 1xx]
		100 => '100 Continue',
		101 => '101 Switching Protocols',

		// [Successful 2xx]
		200 => '200 OK',
		201 => '201 Created',
		202 => '202 Accepted',
		203 => '203 Non-Authoritative Information',
		204 => '204 No Content',
		205 => '205 Reset Content',
		206 => '206 Partial Content',

		// [Redirection 3xx]
		300 => '300 Multiple Choices',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		303 => '303 See Other',
		304 => '304 Not Modified',
		305 => '305 Use Proxy',
		306 => '306 (Unused)',
		307 => '307 Temporary Redirect',

		// [Client Error 4xx]
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		402 => '402 Payment Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		405 => '405 Method Not Allowed',
		406 => '406 Not Acceptable',
		407 => '407 Proxy Authentication Required',
		408 => '408 Request Timeout',
		409 => '409 Conflict',
		410 => '410 Gone',
		411 => '411 Length Required',
		412 => '412 Precondition Failed',
		413 => '413 Request Entity Too Large',
		414 => '414 Request-URI Too Long',
		415 => '415 Unsupported Media Type',
		416 => '416 Requested Range Not Satisfiable',
		417 => '417 Expectation Failed',

		// [Server Error 5xx]
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
		502 => '502 Bad Gateway',
		503 => '503 Service Unavailable',
		504 => '504 Gateway Timeout',
		505 => '505 HTTP Version Not Supported',
	);

	/**
	 * Constructs the object
	 *
	 * @param string  $hostname  FTP server hostname
	 * @param string  $username  FTP client username
	 * @param string  $password  FTP client password
	 * @param string  $directory FTP server directory
	 * @param integer $port      FTP server port
	 * @param boolean $active    FTP active mode
	 * @param boolean $ssl       FTP SSL mode
	 */
	public function __construct( $hostname, $username, $password, $directory = '/', $port = 21, $active = false, $ssl = false ) {
		if ( ! extension_loaded( 'curl' ) ) {
			throw new Ai1wmfe_Error_Exception( __( 'FTP Extension requires PHP cURL extension. <a href="https://help.servmask.com/knowledgebase/curl-missing-in-php-installation/" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
		}

		$this->hostname  = $hostname;
		$this->username  = $username;
		$this->password  = $password;
		$this->directory = $directory;
		$this->port      = $port;
		$this->active    = $active;
		$this->ssl       = $ssl;
	}

	/**
	 * Create folder
	 *
	 * @param  string $folder_path Folder path
	 * @return boolean
	 */
	public function create_folder( $folder_path ) {
		$folder_path = $this->sanitize_path( $folder_path );

		try {
			$this->make_request(
				sprintf( '/%s/%s/', $this->directory, $folder_path ),
				array(
					CURLOPT_FTP_CREATE_MISSING_DIRS => true,
				)
			);
		} catch ( Ai1wmfe_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve files and folders metadata
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function list_folder( $folder_path ) {
		$folder_path = $this->sanitize_path( $folder_path );

		try {
			$items = $this->raw_list_folder( $folder_path );
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		// Loop over files and folders
		$objects = array();
		foreach ( $items as $item ) {
			if ( ( $meta = preg_split( '/\s+/', trim( $item ) ) ) && ( $name = array_pop( $meta ) ) ) {
				if ( ( $name = trim( $name ) ) && strpos( $name, '.' ) !== 0 ) {
					$matches = array();
					if ( preg_match( '/(.+?)-(\d+?)-(\d+?)-(.+?)\.wpress/', $name, $matches ) ) {
						$objects[] = array(
							'name'  => $name,
							'path'  => $this->sanitize_path( sprintf( '/%s/%s', $folder_path, $name ) ),
							'date'  => isset( $matches[2] ) && isset( $matches[3] ) ? strtotime( "{$matches[2]} {$matches[3]}" ) : null,
							'bytes' => isset( $meta[4] ) ? $meta[4] : null,
							'type'  => isset( $meta[0] ) && strpos( $meta[0], 'd' ) === 0 ? 'folder' : 'file',
						);
					} else {
						$objects[] = array(
							'name'  => $name,
							'path'  => $this->sanitize_path( sprintf( '/%s/%s', $folder_path, $name ) ),
							'date'  => isset( $meta[5] ) && isset( $meta[6] ) && isset( $meta[7] ) ? strtotime( "{$meta[5]} {$meta[6]} {$meta[7]}" ) : null,
							'bytes' => isset( $meta[4] ) ? $meta[4] : null,
							'type'  => isset( $meta[0] ) && strpos( $meta[0], 'd' ) === 0 ? 'folder' : 'file',
						);
					}
				}
			}
		}

		return $objects;
	}

	/**
	 * Retrieve files and folders raw metadata
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function raw_list_folder( $folder_path ) {
		$folder_path = $this->sanitize_path( $folder_path );

		try {
			$items = $this->make_request(
				sprintf( '/%s/%s/', $this->directory, $folder_path ),
				array(
					CURLOPT_CUSTOMREQUEST => 'LIST -tlA',
				)
			);
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return explode( "\n", $items );
	}

	/**
	 * Upload file
	 *
	 * @param  string  $local_file_path  Local file path
	 * @param  string  $remote_file_path Remote file path
	 * @param  integer $file_size        File size
	 * @return boolean
	 */
	public function upload_file( $local_file_path, $remote_file_path, $file_size ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		// Upload file from file stream
		if ( ( $file_stream = fopen( $local_file_path, 'rb' ) ) ) {
			$this->remote_file_path = $remote_file_path;

			try {

				$options = array(
					CURLOPT_UPLOAD     => true,
					CURLOPT_INFILE     => $file_stream,
					CURLOPT_INFILESIZE => $file_size,
				);

				/**
				 * The $handler parameter was added in PHP version 5.5.0 breaking backwards compatibility.
				 * If we are using PHP version lower than 5.5.0, we need to shift the arguments.
				 *
				 * @see http://php.net/manual/en/function.curl-setopt.php#refsect1-function.curl-setopt-changelog
				 */
				if ( version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
					$options[ CURLOPT_NOPROGRESS ]       = false;
					$options[ CURLOPT_PROGRESSFUNCTION ] = array( $this, 'upload_file_progress_callback_php55' );
				} elseif ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
					$options[ CURLOPT_NOPROGRESS ]       = false;
					$options[ CURLOPT_PROGRESSFUNCTION ] = array( $this, 'upload_file_progress_callback_php53' );
				}

				$this->make_request( sprintf( '/%s/%s', $this->directory, $remote_file_path ), $options );
			} catch ( Ai1wmfe_Error_Exception $e ) {
				throw $e;
			}

			fclose( $file_stream );
		}

		return true;
	}

	/**
	 * Upload file progress callback (PHP >= 5.5.0)
	 *
	 * @param  resource $handler              cURL handler
	 * @param  integer  $download_file_size   Download file size
	 * @param  integer  $download_file_offset Download file offset
	 * @param  integer  $upload_file_size     Upload file size
	 * @param  integer  $upload_file_offset   Upload file offset
	 * @return integer
	 */
	public function upload_file_progress_callback_php55( $handler, $download_file_size, $download_file_offset, $upload_file_size, $upload_file_size_offset ) {
		if ( $upload_file_size > 0 ) {
			// Get progress
			$upload_file_progress = (int) ( ( $upload_file_size_offset / $upload_file_size ) * 100 );

			// Get file base name
			$upload_file_name = basename( $this->remote_file_path );

			// Get human readable file size
			$upload_file_size = ai1wm_size_format( $upload_file_size );

			// Set progress
			Ai1wm_Status::info(
				sprintf(
					__(
						'<i class="ai1wmfe-icon-ftp"></i> ' .
						'Uploading <strong>%s</strong> (%s)<br />%d%% complete',
						AI1WMFE_PLUGIN_NAME
					),
					$upload_file_name,
					$upload_file_size,
					$upload_file_progress
				)
			);
		}

		return 0;
	}

	/**
	 * Upload file progress callback (PHP >= 5.3.0, PHP <= 5.5.0)
	 *
	 * @param  integer  $download_file_size   Download file size
	 * @param  integer  $download_file_offset Download file offset
	 * @param  integer  $upload_file_size     Upload file size
	 * @param  integer  $upload_file_offset   Upload file offset
	 * @return integer
	 */
	public function upload_file_progress_callback_php53( $download_file_size, $download_file_offset, $upload_file_size, $upload_file_size_offset ) {
		if ( $upload_file_size > 0 ) {
			// Get progress
			$upload_file_progress = (int) ( ( $upload_file_size_offset / $upload_file_size ) * 100 );

			// Get file base name
			$upload_file_name = basename( $this->remote_file_path );

			// Get human readable file size
			$upload_file_size = ai1wm_size_format( $upload_file_size );

			// Set progress
			Ai1wm_Status::info(
				sprintf(
					__(
						'<i class="ai1wmfe-icon-ftp"></i> ' .
						'Uploading <strong>%s</strong> (%s)<br />%d%% complete',
						AI1WMFE_PLUGIN_NAME
					),
					$upload_file_name,
					$upload_file_size,
					$upload_file_progress
				)
			);
		}

		return 0;
	}


	/**
	 * Upload file chunk
	 *
	 * @param  string  $file_chunk_data  File chunk data
	 * @param  string  $remote_file_path Remote file path
	 * @param  integer $file_range_start File range start
	 * @return boolean
	 */
	public function upload_file_chunk( $file_chunk_data, $remote_file_path, $file_range_start = 0 ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		// Copy file chunk data into file chunk stream
		if ( ( $file_chunk_stream = fopen( 'php://temp', 'wb+' ) ) ) {
			if ( ( $file_chunk_size = fwrite( $file_chunk_stream, $file_chunk_data ) ) ) {
				rewind( $file_chunk_stream );

				try {
					$this->make_request(
						sprintf( '/%s/%s', $this->directory, $remote_file_path ),
						array(
							CURLOPT_UPLOAD     => true,
							CURLOPT_FTPAPPEND  => true,
							CURLOPT_INFILE     => $file_chunk_stream,
							CURLOPT_INFILESIZE => $file_chunk_size,
						)
					);
				} catch ( Ai1wmfe_Error_Exception $e ) {
					throw $e;
				}
			}

			fclose( $file_chunk_stream );
		}

		return true;
	}

	/**
	 * Download file
	 *
	 * @param  string  $local_file_path  Local file path
	 * @param  string  $remote_file_path Remote file path
	 * @return boolean
	 */
	public function download_file( $local_file_path, $remote_file_path ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		// Download file to file stream
		if ( ( $file_stream = fopen( $local_file_path, 'wb' ) ) ) {
			$this->remote_file_path = $remote_file_path;

			try {

				$options = array( CURLOPT_FILE => $file_stream );

				/**
				 * The $handler parameter was added in PHP version 5.5.0 breaking backwards compatibility.
				 * If we are using PHP version lower than 5.5.0, we need to shift the arguments.
				 *
				 * @see http://php.net/manual/en/function.curl-setopt.php#refsect1-function.curl-setopt-changelog
				 */
				if ( version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
					$options[ CURLOPT_NOPROGRESS ]       = false;
					$options[ CURLOPT_PROGRESSFUNCTION ] = array( $this, 'download_file_progress_callback_php55' );
				} elseif ( version_compare( PHP_VERSION, '5.3.0', '>=' ) ) {
					$options[ CURLOPT_NOPROGRESS ]       = false;
					$options[ CURLOPT_PROGRESSFUNCTION ] = array( $this, 'download_file_progress_callback_php53' );
				}

				$this->make_request( sprintf( '/%s/%s', $this->directory, $remote_file_path ), $options );
			} catch ( Ai1wmfe_Error_Exception $e ) {
				throw $e;
			}

			fclose( $file_stream );
		}

		return true;
	}

	/**
	 * Download file progress callback (PHP >= 5.5.0)
	 *
	 * @param  resource $handler              cURL handler
	 * @param  integer  $download_file_size   Download file size
	 * @param  integer  $download_file_offset Download file offset
	 * @param  integer  $upload_file_size     Upload file size
	 * @param  integer  $upload_file_offset   Upload file offset
	 * @return integer
	 */
	public function download_file_progress_callback_php55( $handler, $download_file_size, $download_file_offset, $upload_file_size, $upload_file_size_offset ) {
		if ( $download_file_size > 0 ) {
			// Get progress
			$progress = (int) ( ( $download_file_offset / $download_file_size ) * 100 );

			// Set progress
			Ai1wm_Status::progress( $progress );
		}

		return 0;
	}

	/**
	 * Download file progress callback (PHP >= 5.3.0, PHP <= 5.5.0)
	 *
	 * @param  integer  $download_file_size   Download file size
	 * @param  integer  $download_file_offset Download file offset
	 * @param  integer  $upload_file_size     Upload file size
	 * @param  integer  $upload_file_offset   Upload file offset
	 * @return integer
	 */
	public function download_file_progress_callback_php53( $download_file_size, $download_file_offset, $upload_file_size, $upload_file_size_offset ) {
		if ( $download_file_size > 0 ) {
			// Get progress
			$progress = (int) ( ( $download_file_offset / $download_file_size ) * 100 );

			// Set progress
			Ai1wm_Status::progress( $progress );
		}

		return 0;
	}

	/**
	 * Download file chunk
	 *
	 * @param  resource $file_stream      File stream
	 * @param  string   $remote_file_path Remote file path
	 * @param  integer  $file_range_start File range start
	 * @param  integer  $file_chunk_size  File chunk size (not used)
	 * @return boolean
	 */
	public function download_file_chunk( $file_stream, $remote_file_path, $file_range_start = 0, $file_chunk_size = AI1WMFE_DEFAULT_FILE_CHUNK_SIZE ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		try {
			$this->make_request(
				sprintf( '/%s/%s', $this->directory, $remote_file_path ),
				array(
					CURLOPT_FILE        => $file_stream,
					CURLOPT_RESUME_FROM => $file_range_start,
					CURLOPT_TIMEOUT     => 10,
				)
			);
		} catch ( Ai1wmfe_Operation_Timedout_Exception $e ) {
			// This is needed in order to simulate chunked download process
		}

		return true;
	}

	/**
	 * Remove file
	 *
	 * @param  string  $file_path File path
	 * @return boolean
	 */
	public function remove_file( $file_path ) {
		$file_path = $this->sanitize_path( $file_path );

		try {
			$this->make_request(
				sprintf( '/%s/', $this->directory ),
				array(
					CURLOPT_QUOTE => array( sprintf( 'DELE /%s', $this->sanitize_path( sprintf( '/%s/%s', $this->directory, $file_path ) ) ) ),
				)
			);
		} catch ( Ai1wmfe_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Test FTP connection
	 *
	 * @return boolean
	 */
	public function test_connection() {
		try {
			$this->make_request(
				sprintf( '/%s/', $this->directory ),
				array(
					CURLOPT_TIMEOUT => 120,
				)
			);
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return true;
	}

	/**
	 * Test FTP append file
	 *
	 * @param  string  $remote_file_path Remote file path
	 * @return boolean
	 */
	public function test_append_file( $remote_file_path = '.servmask' ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		// Write PHP OS
		if ( ( $file_stream = fopen( 'php://temp', 'wb+' ) ) ) {
			if ( ( $file_size = fwrite( $file_stream, PHP_OS ) ) ) {
				rewind( $file_stream );
			}

			try {
				$this->make_request(
					sprintf( '/%s/%s', $this->directory, $remote_file_path ),
					array(
						CURLOPT_UPLOAD     => true,
						CURLOPT_INFILE     => $file_stream,
						CURLOPT_INFILESIZE => $file_size,
					)
				);
			} catch ( Ai1wmfe_Error_Exception $e ) {
			}

			fclose( $file_stream );
		}

		// Write PHP OS (append)
		if ( ( $file_stream = fopen( 'php://temp', 'wb+' ) ) ) {
			if ( ( $file_size = fwrite( $file_stream, PHP_OS ) ) ) {
				rewind( $file_stream );
			}

			try {
				$this->make_request(
					sprintf( '/%s/%s', $this->directory, $remote_file_path ),
					array(
						CURLOPT_UPLOAD     => true,
						CURLOPT_FTPAPPEND  => true,
						CURLOPT_INFILE     => $file_stream,
						CURLOPT_INFILESIZE => $file_size,
					)
				);
			} catch ( Ai1wmfe_Error_Exception $e ) {
			}

			fclose( $file_stream );
		}

		try {
			if ( ( $remote_file_data = $this->make_request( sprintf( '/%s/%s', $this->directory, $remote_file_path ) ) ) ) {
				if ( strlen( $remote_file_data ) === ( 2 * strlen( PHP_OS ) ) ) {
					return true;
				}
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
		}

		return false;
	}

	/**
	 * Make cURL request
	 *
	 * @param  string $path    FTP path
	 * @param  array  $options cURL options
	 * @return mixed
	 */
	protected function make_request( $path, $options = array() ) {
		$this->handler = curl_init();

		// Set FTP URL
		curl_setopt( $this->handler, CURLOPT_URL, sprintf( 'ftp://%s:%d/%s', $this->hostname, $this->port, $this->sanitize_path( $path ) ) );

		// Set username and password
		curl_setopt( $this->handler, CURLOPT_USERPWD, sprintf( '%s:%s', $this->username, $this->password ) );

		// Set default configuration
		curl_setopt( $this->handler, CURLOPT_HEADER, false );
		curl_setopt( $this->handler, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->handler, CURLOPT_BINARYTRANSFER, true );
		curl_setopt( $this->handler, CURLOPT_CONNECTTIMEOUT, 120 );
		curl_setopt( $this->handler, CURLOPT_TIMEOUT, 0 );

		// Add additional options to connect to FTP with SSL if SSL was selected
		if ( $this->ssl ) {
			curl_setopt( $this->handler, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $this->handler, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $this->handler, CURLOPT_FTP_SSL, CURLFTPSSL_TRY );
			curl_setopt( $this->handler, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS );
		}

		// Turn on active mode
		if ( $this->active ) {
			curl_setopt( $this->handler, CURLOPT_FTP_USE_EPRT, true );
			curl_setopt( $this->handler, CURLOPT_FTPPORT, 0 );
		} else {
			curl_setopt( $this->handler, CURLOPT_FTP_USE_EPSV, true );
		}

		// Apply cURL options
		foreach ( $options as $name => $value ) {
			curl_setopt( $this->handler, $name, $value );
		}

		// HTTP request
		$response = curl_exec( $this->handler );
		if ( $response === false ) {
			if ( ( $errno = curl_errno( $this->handler ) ) ) {
				switch ( $errno ) {
					case 6:
					case 7:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to connect to FTPS server. Please check your FTPS hostname, port, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						} else {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to connect to FTP server. Please check your FTP hostname, port, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						}

					case 9:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to change FTPS directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						} else {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to change FTP directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						}

					case 23:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Write_Error_Exception( __( 'Unable to download file from FTPS server. Please ensure that you have enough disk space. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#write-error" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						} else {
							throw new Ai1wmfe_Write_Error_Exception( __( 'Unable to download file from FTP server. Please ensure that you have enough disk space. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#write-error" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						}

					case 28:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Operation_Timedout_Exception( __( 'Connecting to FTPS server timed out. Please check FTPS hostname, port, username, password, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#operation-timedout" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						} else {
							throw new Ai1wmfe_Operation_Timedout_Exception( __( 'Connecting to FTP server timed out. Please check FTP hostname, port, username, password, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#operation-timedout" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						}

					case 67:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to login to FTPS server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						} else {
							throw new Ai1wmfe_Error_Exception( __( 'Unable to login to FTP server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
						}

					default:
						if ( $this->ssl ) {
							throw new Ai1wmfe_Connect_Exception( sprintf( __( 'Unable to connect to FTPS. Error code: %s. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $errno, $errno ) );
						} else {
							throw new Ai1wmfe_Connect_Exception( sprintf( __( 'Unable to connect to FTP. Error code: %s. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $errno, $errno ) );
						}
				}
			}
		}

		// HTTP errors
		$http_code = curl_getinfo( $this->handler, CURLINFO_HTTP_CODE );
		if ( $http_code === 429 ) {
			throw new Ai1wmfe_Rate_Limit_Exception( sprintf( __( 'Too many requests. Please try again later. Error code: %s <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $http_code, $http_code ) );
		}

		if ( $http_code >= 500 ) {
			throw new Ai1wmfe_Internal_Server_Error_Exception( sprintf( __( 'Internal Server Error. Please try again later. Error code: %s <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $http_code, $http_code ) );
		}

		if ( $http_code >= 400 ) {
			if ( isset( $this->messages[ $http_code ] ) ) {
				throw new Ai1wmfe_Error_Exception( sprintf( __( '%s. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $this->messages[ $http_code ], $http_code ) );
			} else {
				throw new Ai1wmfe_Error_Exception( sprintf( __( 'Error code: %s. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#%s" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ), $http_code, $http_code ) );
			}
		}

		return $response;
	}

	/**
	 * Sanitize FTP path
	 *
	 * @param  string $path FTP path
	 * @return string
	 */
	public function sanitize_path( $path ) {
		return ltrim( preg_replace( '/[\\\\\/]+/', '/', $path ), '/' );
	}

	/**
	 * Destroy cURL handler
	 *
	 * @return void
	 */
	public function __destruct() {
		if ( $this->handler !== null ) {
			curl_close( $this->handler );
		}
	}
}
