<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: telepat
 * Date: 5/10/17
 * Time: 7:10 PM
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
	<?php if ($show_label): ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php endif; ?>
	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php if ($value): ?>
			<?php echo __('file.loaded') . ': <a href="' . URL::site($path . $value) . '" target="_blank" rel="nofollow">' . $value . '</a> &nbsp;&nbsp;'; ?>
			<br /><br />
		<?php endif; ?>

		<span><input<?php echo HTML::attributes($attributes); ?>/></span>
		<br />
		<?php if ($is_allow_remove && !empty($value)): ?>
			<label class="btn btn-default">
				<?php echo Form::checkbox($name_remove, NULL, false); ?>
				<?php echo __('file.remove'); ?>
			</label>
		<?php endif; ?>

		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
<div class="clearfix"></div>

