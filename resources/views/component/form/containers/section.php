<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.07.14
 * Time: 15:16
 */
?>
<section <?php echo HTML::attributes($attributes); ?>>
	<?php if ($show_label): ?>
		<h4 class="fc-section-header"><?php echo $label; ?></h4>
	<?php endif; ?>
	<?php echo $container_body; ?>
</section>