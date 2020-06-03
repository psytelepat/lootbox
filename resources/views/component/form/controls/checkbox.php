<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */

?>
<?php if ($form_horizontal): ?>
	<div<?php echo $group_attributes; ?>>
		<label>
			<?php echo Form::checkbox($name, NULL, $value, $attributes); ?>
			<?php if ($show_label): ?>
				<?php echo $label; ?>
			<?php endif; ?>
		</label>
	</div>
	<div class="clearfix"></div>
	<?php if (!empty($description)): ?>
		<div class="form-group">
			<small class="col-sm-10 col-sm-offset-2"><?php echo nl2br($description); ?></small>
		</div>
	<?php endif; ?>
<?php else: ?>
	<div<?php echo $group_attributes; ?>>
		<label>
			<?php echo Form::checkbox($name, NULL, $value, $attributes); ?>
			<?php if ($show_label): ?>
				<?php echo $label; ?>
			<?php endif; ?>
		</label>
	</div>
	<?php if (!empty($description)): ?>
		<div class="form-group">
			<small><?php echo nl2br($description); ?></small>
		</div>
	<?php endif; ?>
<?php endif; ?>
