<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 28.05.18
 * Time: 13:41
 */
?>
<ul <?php echo HTML::attributes($attributes); ?>>
	<?php foreach ($controls as $control): if ($control instanceof Force_Tab): ?>
		<li <?php echo $control->render_attributes(); ?>><a <?php echo $control->render_link_attributes(); ?>><?php echo $control->get_label(); ?></a></li>
	<?php endif; endforeach; ?>
</ul>