<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.10.14
 * Time: 13:10
 */
$id = Arr::get($attributes, 'id', $name);
$label_attributes = [
	'for' => $id,
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
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<div class="input-group date">
			<?php echo FORM::input($name, $value, $attributes); ?>
			<label class="input-group-addon" for="<?php echo $id; ?>"><?php echo $icon; ?></label>
		</div>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
