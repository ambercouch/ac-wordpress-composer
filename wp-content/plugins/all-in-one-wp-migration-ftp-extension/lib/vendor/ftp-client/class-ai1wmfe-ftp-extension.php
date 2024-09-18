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

class Ai1wmfe_FTP_Extension implements Ai1wmfe_FTP_Interface {

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
		if ( ! extension_loaded( 'ftp' ) ) {
			throw new Ai1wmfe_Error_Exception( __( 'FTP Extension requires PHP FTP extension. <a href="https://help.servmask.com/knowledgebase/ftp-missing-in-php-installation/" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
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
			if ( @ftp_nlist( $this->get_connection(), $folder_path ) === false ) {
				@ftp_mkdir( $this->get_connection(), $folder_path );
			}
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
			if ( empty( $folder_path ) ) {
				$items = $this->raw_list_folder( '.' );
			} else {
				$items = $this->raw_list_folder( $folder_path );
			}
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
			if ( ( $items = @ftp_rawlist( $this->get_connection(), sprintf( '-tlA %s', $folder_path ) ) ) === false ) {
				throw new Ai1wmfe_Error_Exception( __( 'FTP list has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return $items;
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

		try {
			if ( @ftp_put( $this->get_connection(), $remote_file_path, $local_file_path, FTP_BINARY ) === false ) {
				throw new Ai1wmfe_Error_Exception( __( 'FTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return true;
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
					if ( @ftp_fput( $this->get_connection(), $file_path, $file_chunk_stream, FTP_BINARY, $file_range_start ) === false ) {
						throw new Ai1wmfe_Error_Exception( __( 'FTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
					}
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

		try {
			if ( @ftp_get( $this->get_connection(), $local_file_path, $remote_file_path, FTP_BINARY ) === false ) {
				throw new Ai1wmfe_Error_Exception( __( 'FTP download has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return true;
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
			if ( ( $status = @ftp_nb_fget( $this->get_connection(), $file_stream, $remote_file_path, FTP_BINARY, $file_range_start ) ) === FTP_FAILED ) {
				throw new Ai1wmfe_Error_Exception( __( 'FTP download has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		$start = microtime( true );
		while ( $status === FTP_MOREDATA ) {
			try {
				if ( ( $status = @ftp_nb_continue( $this->get_connection() ) ) === FTP_FAILED ) {
					throw new Ai1wmfe_Error_Exception( __( 'FTP download has failed', AI1WMFE_PLUGIN_NAME ) );
				}
			} catch ( Ai1wmfe_Error_Exception $e ) {
				throw $e;
			}

			// More than 10 seconds have passed, break and do another request
			if ( ( $timeout = apply_filters( 'ai1wm_completed_timeout', 10 ) ) ) {
				if ( ( microtime( true ) - $start ) > $timeout ) {
					break;
				}
			}
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
			@ftp_delete( $this->get_connection(), $file_path );
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
			$this->get_connection();
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
				if ( ! @ftp_fput( $this->get_connection(), $remote_file_path, $file_stream, FTP_BINARY ) ) {
					throw new Ai1wmfe_Error_Exception( __( 'FTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
				}
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
				if ( ! @ftp_fput( $this->get_connection(), $remote_file_path, $file_stream, FTP_BINARY, $file_size ) ) {
					throw new Ai1wmfe_Error_Exception( __( 'FTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
				}
			} catch ( Ai1wmfe_Error_Exception $e ) {
			}

			fclose( $file_stream );
		}

		try {
			if ( ( $remote_file_size = @ftp_size( $this->get_connection(), $remote_file_path ) ) ) {
				if ( $remote_file_size === ( 2 * strlen( PHP_OS ) ) ) {
					return true;
				}
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
		}

		return false;
	}

	/**
	 * Get FTP connection (lazy loading)
	 *
	 * @return resource
	 */
	protected function get_connection() {
		if ( $this->handler === null ) {
			if ( $this->ssl ) {
				if ( ( $this->handler = @ftp_ssl_connect( $this->hostname, $this->port, 120 ) ) === false ) {
					throw new Ai1wmfe_Connect_Exception( __( 'Unable to connect to FTPS server. Please check your FTPS hostname, port, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
				}
			} else {
				if ( ( $this->handler = @ftp_connect( $this->hostname, $this->port, 120 ) ) === false ) {
					throw new Ai1wmfe_Connect_Exception( __( 'Unable to connect to FTP server. Please check your FTP hostname, port, and active mode settings. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
				}
			}

			// Login to FTP server
			if ( @ftp_login( $this->handler, $this->username, $this->password ) === false ) {
				if ( $this->ssl ) {
					throw new Ai1wmfe_Error_Exception( __( 'Unable to login to FTPS server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
				} else {
					throw new Ai1wmfe_Error_Exception( __( 'Unable to login to FTP server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
				}
			}

			// Turn on active mode
			if ( $this->active ) {
				@ftp_pasv( $this->handler, false );
			} else {
				@ftp_pasv( $this->handler, true );
			}

			// Change FTP directory
			if ( $this->directory ) {
				if ( @ftp_chdir( $this->handler, $this->directory ) === false ) {
					if ( $this->ssl ) {
						throw new Ai1wmfe_Error_Exception( __( 'Unable to change FTPS directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					} else {
						throw new Ai1wmfe_Error_Exception( __( 'Unable to change FTP directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}
				}
			}

			// When enabled, GET or PUT requests with a startpos parameter will first seek to the requested position within the file
			@ftp_set_option( $this->handler, FTP_AUTOSEEK, false );
		}

		return $this->handler;
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
	 * Close FTP connection
	 *
	 * @return void
	 */
	public function __destruct() {
		if ( $this->handler !== null ) {
			@ftp_close( $this->handler );
		}
	}
}
