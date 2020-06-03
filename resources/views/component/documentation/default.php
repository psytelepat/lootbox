<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 30.07.12
 * Time: 18:04
 */
?>
<div class="row">
	<div class="col-sm-<?php echo (!empty($menu)) ? '9' : '12'; ?>">
		<div class="bs-docs-section">
			<?php echo implode("\n", $content); ?>
		</div>
	</div>

	<?php if (!empty($menu)): ?>
		<div class="col-sm-3 docs-sidebar">
			<?php echo $menu; ?>
		</div>
	<?php endif; ?>
</div>
<div class="clearfix"></div>
