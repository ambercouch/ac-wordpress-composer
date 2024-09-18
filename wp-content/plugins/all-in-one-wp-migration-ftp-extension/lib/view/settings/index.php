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
?>

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'FTP Settings', AI1WMFE_PLUGIN_NAME ); ?></h1>
				<br />
				<br />

				<?php if ( Ai1wm_Message::has( 'success' ) ) : ?>
					<div class="ai1wm-message ai1wm-success-message">
						<p><?php echo Ai1wm_Message::get( 'success' ); ?></p>
					</div>
				<?php elseif ( Ai1wm_Message::has( 'error' ) ) : ?>
					<div class="ai1wm-message ai1wm-error-message">
						<p><?php echo Ai1wm_Message::get( 'error' ); ?></p>
					</div>
				<?php endif; ?>

				<div id="ai1wmfe-ftp-details">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmfe_ftp_connection' ) ); ?>" enctype="multipart/form-data">
						<div class="ai1wm-field">
							<?php _e( 'Type', AI1WMFE_PLUGIN_NAME ); ?>
							<br />
							<div style="margin: 6px 0 8px 0;">
								<label for="ai1wmfe-ftp-type-ftp">
									<input type="radio" id="ai1wmfe-ftp-type-ftp" name="ai1wmfe_ftp_type" class="ai1wmfe-settings-type" value="ftp" <?php echo $type === 'ftp' ? 'checked="checked"' : null; ?> />
									<?php _e( 'FTP', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
								<label for="ai1wmfe-ftp-type-ftps">
									<input type="radio" id="ai1wmfe-ftp-type-ftps" name="ai1wmfe_ftp_type" class="ai1wmfe-settings-type" value="ftps" <?php echo $type === 'ftps' ? 'checked="checked"' : null; ?> />
									<?php _e( 'FTPS', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
								<label for="ai1wmfe-ftp-type-sftp">
									<input type="radio" id="ai1wmfe-ftp-type-sftp" name="ai1wmfe_ftp_type" class="ai1wmfe-settings-type" value="sftp" <?php echo $type === 'sftp' ? 'checked="checked"' : null; ?> />
									<?php _e( 'SFTP', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
							</div>
						</div>

						<div class="ai1wm-field">
							<label for="ai1wmfe-ftp-hostname">
								<?php _e( 'Hostname', AI1WMFE_PLUGIN_NAME ); ?>
								<br />
								<input type="text" placeholder="<?php _e( 'Enter Hostname', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-hostname" name="ai1wmfe_ftp_hostname" class="ai1wmfe-settings-hostname" value="<?php echo esc_attr( $hostname ); ?>" />
							</label>
						</div>

						<div id="ai1wmfe-ftp-authentication-details" class="<?php echo $type === 'sftp' ? null : 'ai1wmfe-hide'; ?>">
							<div class="ai1wm-field">
								<?php _e( 'Authentication type', AI1WMFE_PLUGIN_NAME ); ?>
								<br />
								<div style="margin: 6px 0 8px 0;">
									<label for="ai1wmfe-ftp-authentication-password">
										<input type="radio" id="ai1wmfe-ftp-authentication-password" name="ai1wmfe_ftp_authentication" class="ai1wmfe-settings-authentication" value="password" <?php echo $authentication === 'password' ? 'checked="checked"' : null; ?> />
										<?php _e( 'Password', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
									<label for="ai1wmfe-ftp-authentication-key">
										<input type="radio" id="ai1wmfe-ftp-authentication-key" name="ai1wmfe_ftp_authentication" class="ai1wmfe-settings-authentication" value="key" <?php echo $authentication === 'key' ? 'checked="checked"' : null; ?> />
										<?php _e( 'Private key', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
								</div>
							</div>
						</div>

						<div class="ai1wm-field">
							<label for="ai1wmfe-ftp-username">
								<?php _e( 'Username', AI1WMFE_PLUGIN_NAME ); ?>
								<br />
								<input type="text" placeholder="<?php _e( 'Enter Username', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-username" name="ai1wmfe_ftp_username" class="ai1wmfe-settings-username" value="<?php echo esc_attr( $username ); ?>" />
							</label>
						</div>

						<div id="ai1wmfe-ftp-authentication-password-details" class="<?php echo $authentication === 'password' ? null : 'ai1wmfe-hide'; ?>">
							<div class="ai1wm-field">
								<label for="ai1wmfe-ftp-password">
									<?php _e( 'Password', AI1WMFE_PLUGIN_NAME ); ?>
									<br />
									<input type="password" placeholder="<?php echo $password ? str_repeat( '*', strlen( $password ) ) : __( 'Enter Password', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-password" name="ai1wmfe_ftp_password" class="ai1wmfe-settings-password" autocomplete="off" />
								</label>
							</div>
						</div>

						<div id="ai1wmfe-ftp-authentication-key-details" class="<?php echo $authentication === 'key' ? null : 'ai1wmfe-hide'; ?>">
							<div class="ai1wm-field">
								<label for="ai1wmfe-ftp-key">
									<?php _e( 'Private key file', AI1WMFE_PLUGIN_NAME ); ?>
									<br />
									<input type="file" id="ai1wmfe-ftp-key" name="ai1wmfe_ftp_key" class="ai1wmfe-settings-key" />
								</label>
							</div>

							<div class="ai1wm-field">
								<label for="ai1wmfe-ftp-passphrase">
									<?php _e( 'Private key passphrase (optional)', AI1WMFE_PLUGIN_NAME ); ?>
									<br />
									<input type="password" placeholder="<?php echo $passphrase ? str_repeat( '*', strlen( $passphrase ) ) : __( 'Enter Passphrase', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-passphrase" name="ai1wmfe_ftp_passphrase" class="ai1wmfe-settings-passphrase" autocomplete="off" />
								</label>
							</div>
						</div>

						<div class="ai1wm-field">
							<label for="ai1wmfe-ftp-directory">
								<?php _e( 'Root directory', AI1WMFE_PLUGIN_NAME ); ?>
								<br />
								<input type="text" placeholder="<?php _e( 'Enter Root directory', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-directory" name="ai1wmfe_ftp_directory" class="ai1wmfe-settings-directory" value="<?php echo esc_attr( $directory ); ?>" />
							</label>
						</div>

						<div class="ai1wm-field">
							<label for="ai1wmfe-ftp-port">
								<?php _e( 'Port', AI1WMFE_PLUGIN_NAME ); ?>
								<br />
								<input type="number" min="1" max="65535" placeholder="<?php _e( 'Enter Port', AI1WMFE_PLUGIN_NAME ); ?>" id="ai1wmfe-ftp-port" name="ai1wmfe_ftp_port" class="ai1wmfe-settings-port" value="<?php echo esc_attr( $port ); ?>" />
							</label>
						</div>

						<div id="ai1wmfe-ftp-active-details" class="<?php echo $type === 'sftp' ? 'ai1wmfe-hide' : null; ?>">
							<div class="ai1wm-field" style="margin: 10px 0 0 0;">
								<label for="ai1wmfe-ftp-active">
									<input type="checkbox" id="ai1wmfe-ftp-active" name="ai1wmfe_ftp_active" <?php echo $active ? 'checked="checked"' : null; ?> />
									<?php _e( 'Active mode', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
							</div>
						</div>

						<p>
							<button type="submit" class="ai1wm-button-blue" name="ai1wmfe_ftp_link" id="ai1wmfe-ftp-link">
								<i class="ai1wm-icon-enter"></i>
								<?php _e( 'Update', AI1WMFE_PLUGIN_NAME ); ?>
							</button>
						</p>
					</form>
				</div>
			</div>

			<?php if ( $connection ) : ?>
				<div id="ai1wmfe-backups" class="ai1wm-holder">
					<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'FTP Backups', AI1WMFE_PLUGIN_NAME ); ?></h1>
					<br />
					<br />

					<?php if ( Ai1wm_Message::has( 'settings' ) ) : ?>
						<div class="ai1wm-message ai1wm-success-message">
							<p><?php echo Ai1wm_Message::get( 'settings' ); ?></p>
						</div>
					<?php endif; ?>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmfe_ftp_settings' ) ); ?>">
						<article class="ai1wmfe-article">
							<h3><?php _e( 'Configure your backup plan', AI1WMFE_PLUGIN_NAME ); ?></h3>

							<p>
								<label for="ai1wmfe-ftp-cron-timestamp">
									<?php _e( 'Backup time:', AI1WMFE_PLUGIN_NAME ); ?>
									<input type="text" name="ai1wmfe_ftp_cron_timestamp" id="ai1wmfe-ftp-cron-timestamp" value="<?php echo esc_attr( get_date_from_gmt( date( 'Y-m-d H:i:s', $ftp_cron_timestamp ), 'g:i a' ) ); ?>" autocomplete="off" />
									<code><?php echo ai1wm_get_timezone_string(); ?></code>
								</label>
							</p>

							<ul id="ai1wmfe-ftp-cron">
								<li>
									<label for="ai1wmfe-ftp-cron-hourly">
										<input type="checkbox" name="ai1wmfe_ftp_cron[]" id="ai1wmfe-ftp-cron-hourly" value="hourly" <?php echo in_array( 'hourly', $ftp_backup_schedules ) ? 'checked="checked"' : null; ?> />
										<?php _e( 'Every hour', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmfe-ftp-cron-daily">
										<input type="checkbox" name="ai1wmfe_ftp_cron[]" id="ai1wmfe-ftp-cron-daily" value="daily" <?php echo in_array( 'daily', $ftp_backup_schedules ) ? 'checked="checked"' : null; ?> />
										<?php _e( 'Every day', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmfe-ftp-cron-weekly">
										<input type="checkbox" name="ai1wmfe_ftp_cron[]" id="ai1wmfe-ftp-cron-weekly" value="weekly" <?php echo in_array( 'weekly', $ftp_backup_schedules ) ? 'checked="checked"' : null; ?> />
										<?php _e( 'Every week', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmfe-ftp-cron-monthly">
										<input type="checkbox" name="ai1wmfe_ftp_cron[]" id="ai1wmfe-ftp-cron-monthly" value="monthly" <?php echo in_array( 'monthly', $ftp_backup_schedules ) ? 'checked="checked"' : null; ?> />
										<?php _e( 'Every month', AI1WMFE_PLUGIN_NAME ); ?>
									</label>
								</li>
							</ul>

							<p>
								<?php _e( 'Last backup date:', AI1WMFE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $last_backup_date; ?>
								</strong>
							</p>

							<p>
								<?php _e( 'Next backup date:', AI1WMFE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $next_backup_date; ?>
								</strong>
							</p>
						</article>

						<article class="ai1wmfe-article">
							<h3><?php _e( 'Notification settings', AI1WMFE_PLUGIN_NAME ); ?></h3>
							<p>
								<label for="ai1wmfe-ftp-notify-toggle">
									<input type="checkbox" id="ai1wmfe-ftp-notify-toggle" name="ai1wmfe_ftp_notify_toggle" <?php echo empty( $notify_ok_toggle ) ? null : 'checked'; ?> />
									<?php _e( 'Send an email when a backup is complete', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label for="ai1wmfe-ftp-notify-error-toggle">
									<input type="checkbox" id="ai1wmfe-ftp-notify-error-toggle" name="ai1wmfe_ftp_notify_error_toggle" <?php echo empty( $notify_error_toggle ) ? null : 'checked'; ?> />
									<?php _e( 'Send an email if a backup fails', AI1WMFE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label for="ai1wmfe-ftp-notify-email">
									<?php _e( 'Email address', AI1WMFE_PLUGIN_NAME ); ?>
									<br />
									<input class="ai1wmfe-email" style="width: 15rem;" type="email" id="ai1wmfe-ftp-notify-email" name="ai1wmfe_ftp_notify_email" value="<?php echo esc_attr( $notify_email ); ?>" />
								</label>
							</p>
						</article>

						<article class="ai1wmfe-article">
							<h3><?php _e( 'Retention settings', AI1WMFE_PLUGIN_NAME ); ?></h3>
							<p>
								<div class="ai1wm-field">
									<label for="ai1wmfe-ftp-backups">
										<?php _e( 'Keep the most recent', AI1WMFE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmfe_ftp_backups" id="ai1wmfe-ftp-backups" value="<?php echo intval( $backups ); ?>" />
									</label>
									<?php _e( 'backups. <small>Default: <strong>0</strong> unlimited</small>', AI1WMFE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmfe-ftp-total">
										<?php _e( 'Limit the total size of backups to', AI1WMFE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmfe_ftp_total" id="ai1wmfe-ftp-total" value="<?php echo intval( $total ); ?>" />
									</label>
									<select style="margin-top: -2px;" name="ai1wmfe_ftp_total_unit" id="ai1wmfe-ftp-total-unit">
										<option value="MB" <?php echo strpos( $total, 'MB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'MB', AI1WMFE_PLUGIN_NAME ); ?></option>
										<option value="GB" <?php echo strpos( $total, 'GB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'GB', AI1WMFE_PLUGIN_NAME ); ?></option>
									</select>
									<?php _e( '<small>Default: <strong>0</strong> unlimited</small>', AI1WMFE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmfe-ftp-days">
										<?php _e( 'Remove backups older than ', AI1WMFE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmfe_ftp_days" id="ai1wmfe-ftp-days" value="<?php echo intval( $days ); ?>" />
									</label>
									<?php _e( 'days. <small>Default: <strong>0</strong> off</small>', AI1WMFE_PLUGIN_NAME ); ?>
								</div>
							</p>
						</article>

						<article class="ai1wmfe-article">
							<h3><?php _e( 'Transfer settings', AI1WMFE_PLUGIN_NAME ); ?></h3>
							<div class="ai1wm-field">
								<label><?php _e( 'Slow Internet (Home)', AI1WMFE_PLUGIN_NAME ); ?></label>
								<input name="ai1wmfe_ftp_file_chunk_size" min="5242880" max="20971520" step="5242880" type="range" value="<?php echo $file_chunk_size; ?>" id="ai1wmfe-ftp-file-chunk-size" />
								<label><?php _e( 'Fast Internet (Internet Servers)', AI1WMFE_PLUGIN_NAME ); ?></label>
							</div>
						</article>

						<p>
							<button type="submit" class="ai1wm-button-blue" name="ai1wmfe_ftp_update" id="ai1wmfe-ftp-update">
								<i class="ai1wm-icon-database"></i>
								<?php _e( 'Update', AI1WMFE_PLUGIN_NAME ); ?>
							</button>
						</p>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">
					<?php if ( ! AI1WM_DEBUG ) : ?>
						<?php include AI1WM_TEMPLATES_PATH . '/common/share-buttons.php'; ?>
					<?php endif; ?>

					<h2><?php _e( 'Leave Feedback', AI1WMFE_PLUGIN_NAME ); ?></h2>

					<?php include AI1WM_TEMPLATES_PATH . '/common/leave-feedback.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
