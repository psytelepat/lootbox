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
	<div class="clearfix"></div>
	<?php echo Form::input($name, $value, $attributes); ?>
</div>
