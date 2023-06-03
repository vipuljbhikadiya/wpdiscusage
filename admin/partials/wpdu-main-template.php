<?php

/**
 * WPDU Main Page
 *
 * @link       https://wordpress.org
 * @since      1.0.0
 *
 * @package    Wp_Disk_Usage
 * @subpackage Wp_Disk_Usage/admin/partials
 */
?>
<header class="wpdu-header">
	<h2 class="header-title"><?php _e( 'Disc Usage', 'wp-disk-usage' ); ?></h2>
</header>

<div class="wpdu-body">
	<div class="grid grid-2">
		<div class="wpdu-box">
			<h3 class="box-title"><?php _e( 'Controls section', 'wp-disk-usage' ); ?></h3>

			<div class="generate-statics-block">
				<button type="button" class="button button-primary button-hero generate-statistics"><?php _e( 'Generate Statistics', 'wp-disk-usage' ); ?></button>
			</div>
			
		</div>

		<div class="wpdu-box">
			<h3 class="box-title"><?php _e( 'Disc Space Usage', 'wp-disk-usage' ); ?></h3>
			<div class="grid wpdu-file-list-wrapper <?php echo ( ! $result ) ? 'hide' : '' ?>">
				<div class="wpdu-directories">
				</div>
				<div class="wpdu-files">
				</div>
			</div>
			<?php if ( $result ) {
				?>
				<div class="grid wpdu-file-list-wrapper">
					<ul class="wpdu-directories">
					</ul>
					<div class="wpdu-files">
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="generate-statics-block no-data"><?php _e( 'Please click <strong>Generate Statics</strong> button in control area to view disc space usage.', 'wp-disk-usage' ); ?></div>
				<?php
				}
			?>
		</div>
	</div>
</div>