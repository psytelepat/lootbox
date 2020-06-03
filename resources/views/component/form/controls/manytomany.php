<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 01.09.12
 * Time: 15:49
 */
$label_attributes = [
	'class' => 'control-label',
];
$div_attributes['class'] = 'col-sm-6';
$div_group_attributes = [];
$div_description_attributes = [];
if ($form_horizontal) {
	$label_attributes['class'] .= ' col-sm-2';
	$div_group_attributes['class'] = 'col-sm-offset-2';
	$div_description_attributes['class'] = 'col-sm-10 col-sm-offset-2 col-xs-12';
} else {
	$div_group_attributes['class'] = 'row';
}
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label <?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div <?php echo HTML::attributes($div_group_attributes); ?>>
		<div <?php echo HTML::attributes($div_attributes); ?>>
			<div class="pull-left"><h5><?php echo __('form.many-to-many.unlinked'); ?></h5></div>
			<div class="pull-right">
				<button class="btn btn-default two-lists-button-right" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string(__('form.many-to-many.add_selected')); ?>>
					<i class="glyphicon glyphicon-arrow-right"></i></button>
			</div>
			<div class="clearfix"></div>
			<?php echo Form::select($name, $available_options, null, $attributes_available_list); ?>
		</div>
		<div <?php echo HTML::attributes($div_attributes); ?>>
			<div class="pull-left">
				<button class="btn btn-default two-lists-button-left" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string(__('form.many-to-many.remove_selected'), 'left'); ?>>
					<i class="glyphicon glyphicon-arrow-left"></i></button>
			</div>
			<div class="pull-right"><h5><?php echo __('form.many-to-many.linked'); ?></h5></div>
			<div class="clearfix"></div>
			<?php echo Form::select($name, $selected_options, null, $attributes_selected_list); ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php if (!empty($description)) { ?>
		<div <?php echo HTML::attributes($div_description_attributes); ?>>
			<small><?php echo nl2br($description); ?></small>
		</div>
	<?php } ?>
</div>

<div id="<?php echo $name . '_values'; ?>">
	<?php echo $selected_values; ?>
</div>