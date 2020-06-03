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
	}
	if ($multiple_reset_button) { ?>
	<div class="btn-group"><?php }
		echo Form::select($name, $values, $value, $attributes);
		if ($multiple_reset_button) { ?>
		<button data-target="<?php echo $name; ?>" class="btn btn-default"><?php echo __('common.reset'); ?></button>
	</div>
<?php } ?>
	<div class="clearfix"></div>
</div>
