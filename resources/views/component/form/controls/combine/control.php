<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 06.06.17
 * Time: 16:34
 */
?>
<div class="combine-control-group">
	<?php echo $control; ?>
	<div class="pull-right">
		<a href="#" class="btn btn-default combine-up" title="<?php echo __('common.up'); ?>"><i class="fa fa-arrow-up"></i></a>
		<a href="#" class="btn btn-default combine-down" title="<?php echo __('common.down'); ?>"><i class="fa fa-arrow-down"></i></a>
		<a href="#" class="btn btn-danger combine-remove" title="<?php echo __('common.delete'); ?>"><i class="fa fa-remove"></i></a>
	</div>
	<div class="clearfix"></div>
	<?php echo Form::hidden($hidden_name, $hidden_value, $hidden_attributes); ?>
</div>
<?php echo $selector; ?>
