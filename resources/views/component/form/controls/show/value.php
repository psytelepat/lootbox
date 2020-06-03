<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 17.11.14
 * Time: 18:25
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
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<div<?php echo HTML::attributes($attributes); ?>>
			<?php if (!empty($value)) { ?>
				<?php echo nl2br($value); ?>
			<?php } else { ?>
				<?php echo $value; ?>
			<?php } ?>
		</div>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
