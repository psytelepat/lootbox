<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 14.11.14
 * Time: 22:27
 */
?>
<?php if ($form_horizontal) { ?>
	<div class="form-group">
		<?php if ($show_label) { ?>
			<label class="col-sm-2 control-label"><?php echo $label; ?></label>
		<?php } ?>

		<div class="col-sm-10<?php if (!$show_label) {
			echo ' col-sm-offset-2';
		} ?>">
			<img src="<?php echo $value; ?>" <?php echo HTML::attributes($attributes); ?> />
		</div>
	</div>
<?php } else { ?>
	<div class="form-group">
		<?php if ($show_label) { ?>
			<label class="control-label"><?php echo $label; ?></label>
		<?php } ?>

		<div>
			<img src="<?php echo $value; ?>" <?php echo HTML::attributes($attributes); ?> />
		</div>
	</div>
<?php } ?>