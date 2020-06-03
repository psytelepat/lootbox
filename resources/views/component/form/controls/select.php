<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 26.07.14
 * Time: 10:32
 */
$label_attributes = [
	'for' => Arr::get($attributes, 'id', $name),
	'class' => 'control-label',
];
$div_attributes = [];
if ($form_horizontal) {
	$label_attributes['class'] .= ' col-sm-2';
	$div_attributes['class'] = 'col-sm-10';
	if (!$show_label) {
		$div_attributes['class'] .= ' col-sm-offset-2';
	}
}
$show_input_group = (!empty($before_input) || !empty($after_input));
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php echo ($show_input_group) ? '<div class="input-group">' : ''; ?>
		<?php echo $before_input; ?>
		<?php echo Form::select($name, $options, $value, $attributes); ?>
		<?php echo $after_input; ?>
		<?php echo ($show_input_group) ? '</div>' : ''; ?>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>