<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 06.06.12
 * Time: 12:05
 */
?>
<div class="shape-top">
	<h3><?php echo $title; ?></h3>
</div>
<?php if (!empty($description)): ?>
	<p class="well well-sm"><?php echo $description; ?></p>
<?php endif; ?>
<div class="shape-text">
	<div class="user-profile">
		<div class="user-profile-description user-forms">
			<div class="col-xs-6">
				Всего: <span id="migration_count"><?php echo $total_items_count; ?></span>, обработано:
				<span id="migration_result">0</span>
				<input type="button" class="btn btn-primary" value="<?php echo $button_caption; ?>" id="start_migration">
			</div>
			<div class="col-xs-6">
				<span class="pull-right" id="migration_timer"></span>
			</div>
			<br />
			<br />

			<div class="progress">
				<div class="progress-bar" role="progressbar" id="migration_progress" style="width: 0%;"></div>
			</div>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><?php echo __('migration.log'); ?></div>
	<div class="row-fluid list-group" id="migration_messages">
	</div>
</div>

