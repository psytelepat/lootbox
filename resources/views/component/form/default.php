<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 30.07.12
 * Time: 18:04
 */
?>
<div class="row">
	<div class="col-md-<?php echo (!empty($menu)) ? '9' : '12'; ?> col-sm-12">
		<?php if ($show_label && !empty($form_title)): ?>
			<h3><?php echo $form_title; ?></h3>
		<?php endif; ?>

		<?php
		echo Form::open($form_action, $attributes);
		echo $form_body;
		?>

		<?php echo $buttons_body; ?>
		<?php echo Form::close(); ?>
	</div>

	<?php if (!empty($menu)): ?>
		<div class="col-md-3 hidden-sm hidden-xs docs-sidebar">
			<?php echo $menu; ?>
		</div>
	<?php endif; ?>
</div>
<div class="clearfix"></div>
