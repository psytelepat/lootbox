<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
$group_class = ($multiple) ? 'checkbox' : 'radio';
if ($horizontal) {
	$group_class .= '-inline';
}
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($form_horizontal) { ?>
		<?php if ($show_label) { ?>
			<label class="col-sm-2 control-label"><?php echo $label; ?></label>
		<?php } ?>
		<div class="col-sm-10">
			<?php foreach ($options as $_key => $_value) { ?>
				<div class='<?php echo $group_class; ?>'>
					<?php
					$checked = is_array($value) ? array_key_exists($_key, $value) : ($value == $_key);
					?>
					<label>
						<?php if ($multiple) {
							echo Form::checkbox($name . '[]', $_key, $checked, $attributes);
						} else {
							echo Form::radio($name, $_key, $checked, $attributes);
						} ?>&nbsp;<?php echo $_value; ?>
					</label>
				</div>
				<?php if (!$horizontal) { ?>
					<div class="clearfix"></div>
				<?php } ?>
			<?php } ?>
			<?php if (!empty($description)) { ?>
				<small><?php echo nl2br($description); ?></small>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php if ($show_label) { ?>
			<label class="control-label"><?php echo $label; ?></label>
		<?php } ?>
		<div>
			<?php foreach ($options as $_key => $_value) { ?>
				<div class='<?php echo $group_class; ?>'>
					<?php
					$checked = is_array($value) ? array_key_exists($_key, $value) : ($value == $_key);
					?>
					<label>
						<?php if ($multiple) { ?>
							<?php echo Form::checkbox($name . '[]', $_key, $checked, $attributes); ?>
						<?php } else { ?>
							<?php echo Form::radio($name, $_key, $checked, $attributes); ?>
						<?php } ?>
						<?php echo $_value; ?>
					</label>
				</div>
			<?php } ?>
			<?php if (!empty($description)) { ?>
				<small><?php echo nl2br($description); ?></small>
			<?php } ?>
		</div>
	<?php } ?>
</div>
