<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 15.07.14
 * Time: 4:08
 */
if ($start_from_new_line) {
	echo '<br/>';
}
?>
<div<?php if ($use_filter_block) {
	echo ' class="helper-filter-block"';
} ?>>
	<?php
	if (!empty($label) && $show_label) {
		echo Form::label($name, $label, array(
			'class' => 'control-label-filter',
		));
	} ?>
	<div<?php echo HTML::attributes($group_attributes); ?>>
		<?php echo FORM::input($name, $value, $attributes); ?>
		<label class="input-group-addon" for="<?php echo $name; ?>"><?php echo $icon; ?></label>
	</div>
	<div class="clearfix"></div>
</div>
