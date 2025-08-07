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

class Ai1wmfe_SFTP_Client implements Ai1wmfe_FTP_Interface {

	/**
	 * SFTP server hostname
	 *
	 * @var string
	 */
	protected $hostname = null;

	/**
	 * SFTP client username
	 *
	 * @var string
	 */
	protected $username = null;

	/**
	 * SFTP client password
	 *
	 * @var string
	 */
	protected $password = null;

	/**
	 * SFTP client authentication
	 *
	 * @var string
	 */
	protected $authentication = 'password';

	/**
	 * SFTP client private key
	 *
	 * @var string
	 */
	protected $key = null;

	/**
	 * SFTP client passphrase
	 *
	 * @var string
	 */
	protected $passphrase = null;

	/**
	 * SFTP server directory
	 *
	 * @var string
	 */
	protected $directory = '/';

	/**
	 * SFTP server port
	 *
	 * @var integer
	 */
	protected $port = 22;

	/**
	 * SFTP library provider
	 *
	 * @var object
	 */
	private $provider = null;

	/**
	 * Constructs the object
	 *
	 * @param string  $hostname       SFTP server hostname
	 * @param string  $username       SFTP client username
	 * @param string  $password       SFTP client password
	 * @param string  $authentication SFTP client authentication
	 * @param string  $key            SFTP client private key
	 * @param string  $passphrase     SFTP client passphrase
	 * @param string  $directory      SFTP server directory
	 * @param integer $port           SFTP server port
	 */
	public function __construct( $hostname, $username, $password, $authentication = 'password', $key = null, $passphrase = null, $directory = '/', $port = 22 ) {
		if ( ! class_exists( 'Net_SSH2' ) ) {
			throw new Ai1wmfe_Error_Exception( __( 'SFTP Extension requires PHPSecLib library. <a href="https://help.servmask.com/knowledgebase/phpseclib-missing-in-php-installation/" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
		}

		$this->hostname       = $hostname;
		$this->username       = $username;
		$this->password       = $password;
		$this->authentication = $authentication;
		$this->key            = $key;
		$this->passphrase     = $passphrase;
		$this->directory      = $directory;
		$this->port           = $port;
	}

	/**
	 * Create folder
	 *
	 * @param  string  $folder_path Folder path
	 * @return boolean
	 */
	public function create_folder( $folder_path ) {
		$folder_path = $this->sanitize_path( $folder_path );

		try {
			if ( @$this->get_connection()->nlist( $folder_path ) === false ) {
				@$this->get_connection()->mkdir( $folder_path );
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
			if ( strpos( $item['filename'], '.' ) !== 0 ) {
				$objects[] = array(
					'name'  => isset( $item['filename'] ) ? basename( $item['filename'] ) : null,
					'path'  => isset( $item['filename'] ) ? $this->sanitize_path( sprintf( '/%s/%s', $folder_path, $item['filename'] ) ) : null,
					'date'  => isset( $item['mtime'] ) ? $item['mtime'] : null,
					'bytes' => isset( $item['size'] ) ? $item['size'] : null,
					'type'  => isset( $item['type'] ) && $item['type'] === NET_SFTP_TYPE_DIRECTORY ? 'folder' : 'file',
				);
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
			if ( ( $items = @$this->get_connection()->rawlist( $folder_path ) ) === false ) {
				throw new Ai1wmfe_List_Exception( __( 'SFTP list has failed', AI1WMFE_PLUGIN_NAME ) );
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
			if ( @$this->get_connection()->put( $remote_file_path, $local_file_path, NET_SFTP_LOCAL_FILE ) === false ) {
				throw new Ai1wmfe_Upload_Exception( __( 'SFTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
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
					if ( @$this->get_connection()->put( $remote_file_path, $file_chunk_stream, NET_SFTP_LOCAL_FILE, $file_range_start ) === false ) {
						throw new Ai1wmfe_Upload_Exception( __( 'SFTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
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
			if ( @$this->get_connection()->get( $remote_file_path, $local_file_path ) === false ) {
				throw new Ai1wmfe_Download_Exception( __( 'SFTP download has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return true;
	}

	/**
	 * Get file content
	 *
	 * @param  string  $remote_file_path Remote file path
	 * @return string
	 */
	public function get_file_content( $remote_file_path ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		try {
			$file_content = @$this->get_connection()->get( $remote_file_path );

			if ( $file_content === false ) {
				throw new Ai1wmfe_Download_Exception( __( 'SFTP download has failed', AI1WMFE_PLUGIN_NAME ) );
			}

			return $file_content;
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}
	}

	/**
	 * Download file chunk
	 *
	 * @param  resource $file_stream      File stream
	 * @param  string   $remote_file_path Remote file path
	 * @param  integer  $file_range_start File range start
	 * @param  integer  $file_chunk_size  File chunk size
	 * @return boolean
	 */
	public function download_file_chunk( $file_stream, $remote_file_path, $file_range_start = 0, $file_chunk_size = AI1WMFE_DEFAULT_FILE_CHUNK_SIZE ) {
		$remote_file_path = $this->sanitize_path( $remote_file_path );

		try {
			if ( @$this->get_connection()->get( $remote_file_path, $file_stream, $file_range_start, $file_chunk_size ) === false ) {
				throw new Ai1wmfe_Download_Exception( __( 'SFTP download has failed', AI1WMFE_PLUGIN_NAME ) );
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
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
			@$this->get_connection()->delete( $file_path );
		} catch ( Ai1wmfe_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Test SFTP connection
	 *
	 * @return boolean
	 */
	public function test_connection() {
		try {
			@$this->get_connection()->isConnected();
			@$this->get_connection()->isAuthenticated();
		} catch ( Ai1wmfe_Error_Exception $e ) {
			throw $e;
		}

		return true;
	}

	/**
	 * Test SFTP append file
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
				if ( @$this->get_connection()->put( $remote_file_path, $file_stream, NET_SFTP_LOCAL_FILE ) === false ) {
					throw new Ai1wmfe_Upload_Exception( __( 'SFTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
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
				if ( @$this->get_connection()->put( $remote_file_path, $file_stream, NET_SFTP_LOCAL_FILE, $file_size ) === false ) {
					throw new Ai1wmfe_Upload_Exception( __( 'SFTP upload has failed', AI1WMFE_PLUGIN_NAME ) );
				}
			} catch ( Ai1wmfe_Error_Exception $e ) {
			}

			fclose( $file_stream );
		}

		@$this->get_connection()->clearStatCache();

		try {
			if ( ( $remote_file_size = @$this->get_connection()->size( $remote_file_path ) ) ) {
				if ( $remote_file_size === ( 2 * strlen( PHP_OS ) ) ) {
					return true;
				}
			}
		} catch ( Ai1wmfe_Error_Exception $e ) {
		}

		return false;
	}

	public function get_connection() {
		if ( $this->provider === null ) {
			$this->provider = new Net_SFTP( $this->hostname, $this->port, 120 );

			// Set credentials
			if ( $this->authentication === 'key' ) {
				$rsa = new Crypt_RSA;
				$rsa->setPassword( $this->passphrase );
				$rsa->loadKey( $this->key );

				// Login to SFTP server
				if ( @$this->provider->login( $this->username, $rsa ) === false ) {
					if ( @$this->provider->isConnected() === false ) {
						throw new Ai1wmfe_Connect_Exception( __( 'Unable to connect to SFTP server. Please check your hostname and port. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}

					if ( @$this->provider->isAuthenticated() === false ) {
						throw new Ai1wmfe_Login_Exception( __( 'Unable to login to SFTP server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}
				}

				// Change SFTP directory
				if ( $this->directory ) {
					if ( @$this->provider->chdir( $this->directory ) === false ) {
						throw new Ai1wmfe_Change_Directory_Exception( __( 'Unable to change SFTP directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}
				}
			} else {
				// Login to SFTP server
				if ( @$this->provider->login( $this->username, $this->password ) === false ) {
					if ( @$this->provider->isConnected() === false ) {
						throw new Ai1wmfe_Connect_Exception( __( 'Unable to connect to SFTP server. Please check your hostname and port. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-hostname" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}

					if ( @$this->provider->isAuthenticated() === false ) {
						throw new Ai1wmfe_Login_Exception( __( 'Unable to login to SFTP server. Please check your username and password. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-credentials" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}
				}

				// Change SFTP directory
				if ( $this->directory ) {
					if ( @$this->provider->chdir( $this->directory ) === false ) {
						throw new Ai1wmfe_Change_Directory_Exception( __( 'Unable to change SFTP directory. Please ensure that you have permission on the server. <a href="https://help.servmask.com/knowledgebase/ftp-error-codes/#invalid-directory" target="_blank">Technical details</a>', AI1WMFE_PLUGIN_NAME ) );
					}
				}
			}
		}

		return $this->provider;
	}

	/**
	 * Sanitize SFTP path
	 *
	 * @param  string $path SFTP path
	 * @return string
	 */
	public function sanitize_path( $path ) {
		return ltrim( preg_replace( '/[\\\\\/]+/', '/', $path ), '/' );
	}

	/**
	 * Close SFTP connection
	 *
	 * @return void
	 */
	public function __destruct() {
		if ( $this->provider !== null ) {
			@$this->provider->disconnect();
		}
	}
}
