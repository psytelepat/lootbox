<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.01.17
 * Time: 20:58
 */
?>
<div <?php echo HTML::attributes($attributes); ?>>
	<?php foreach ($controls as $control_id => $control) { ?>
		<?php if ($control instanceof Force_Form_Control): ?>
			<a href="#" class="btn btn-info combine-insert" data-type="<?php echo $control->get_type(); ?>"><?php echo $control->get_icon() . ' ' . $control->get_label(); ?></a>
		<?php endif; ?>
	<?php } ?>
</div>
