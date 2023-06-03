<?php

/**
 * Setting Page
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/admin/partials
 */
?>
<header class="wpdu-header">
	<h2 class="header-title"><?php _e( 'Settings', 'wp-disk-usage' ); ?></h2>
</header>

<div class="wpdu-body">
	<div class="wpdu-box">
		<h3 class="box-title"><?php _e( 'Settings', 'wp-disk-usage' ); ?></h3>
		<form class="form-setting" method="post" action="options.php">
            <?php settings_fields('wpdu_settings'); ?>
            <div class="setting--wrapper">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="worker_time"><?php _e( 'Worker Time', 'wp-disk-usage'); ?></label>
							</th>
							<td>
								<input type="text" name="worker_time" class="regular-text" id="worker_time" value="<?php echo get_option( 'worker_time' ); ?>" placeholder="<?php _e( 'Worker Time', 'wp-disk-usage'); ?>" required>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

            <div>
                <input type="submit" name="wpdu_submit" id="btn-submit" class="button button-primary" value="Save Changes">
            </div>
        </form>
	</div>
</div>