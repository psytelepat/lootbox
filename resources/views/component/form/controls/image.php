<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.07.14
 * Time: 19:43
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
		<table>
			<tbody>
			<?php foreach ($image_types as $image_type): ?>
				<tr>
					<td class="table-col-middle"><?php
						if ($zoom):
							echo '<a href="' . Helper_Image::get_filename($value, $image_type, $default_image) . '" class="zoom">';
						endif;
						if ($image):
							echo Helper_Image::get_image_by_src($image, $image_type, null, $image_attributes, true);
						else:
							echo Helper_Image::get_image($value, $image_type, null, $image_attributes, $default_image);
						endif;
						if ($zoom):
							echo '</a>';
						endif;
						?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<br />

		<span><input<?php echo HTML::attributes($attributes); ?>/></span>
		<?php if ($use_title): ?>
			<br />
			<?php echo FORM::input($title_name, $title_value, $title_attributes); ?>
		<?php endif; ?>
		<?php if ($is_allow_remove && !empty($value)): ?>
			<br />
			<label class="btn btn-default">
				<?php echo Form::checkbox($name_remove, NULL, false); ?>
				<?php echo __('image.remove'); ?>
			</label>
		<?php endif; ?>
	</div>
</div>
<div class="clearfix"></div>

