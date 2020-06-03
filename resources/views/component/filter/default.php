<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 07.08.12
 * Time: 14:05
 */
?>
<div class="row">
	<div class="col-sm-2">
		<p id="filter-header" class="admin-row-header pull-left"<?php if (!$always_visible && !$filter_is_visible && empty($filter_conditions)) echo ' style="display:none;"'; ?>><?php echo __('common.search.title'); ?></p>
	</div>
	<div class="col-sm-<?php echo ($always_visible) ? '10' : '8'; ?>">
		<div class="admin-filter-conditions pull-left">
<?php
	if (!empty($filter_conditions)):
		$_conditions_line = array();
		foreach ($filter_conditions as $_key => $_value):
			$_conditions_line[] = $_key . $key_value_divider . '<span style="color: black">' . $_value . '</span>';
		endforeach;
		$_conditions_line = implode($filter_conditions_divider, $_conditions_line);
		echo '<span style="color: silver">' . $_conditions_line . '</span>';
	endif;
?>
		</div>
	</div>
<?php if (!$always_visible): ?>
	<div class="col-sm-2">
		<div class="admin-filter-slide pull-right">
			<span class="btn btn-info"><?php echo ($filter_is_visible) ? __('admin.filter.hide') : __('admin.filter.show'); ?></span>
		</div>
	</div>
<?php endif; ?>
</div>
<div class="clearfix"></div>

<div class="shape-text admin-filter-block"<?php if (!$always_visible && !$filter_is_visible) echo ' style="display:none;"'; ?>>
<?php
	echo Form::open($form_action, $attributes);
	echo $filter_body;
	echo $hidden_body;
?>
	<div class="helper-filter-block helper-filter-block-oh">
		<button type="submit" class="btn btn-primary"><i class="icon-search"></i> <?php echo __('common.search_button'); ?></button>
		<a class="btn btn-default filter-cancel-btn" href="<?php echo $cancel_uri; ?>"><?php echo __('common.search_button_cancel'); ?></a>
	</div>
	<div class="clearfix"></div>
	<?php echo Form::close(); ?>
</div>
<?php if (!$always_visible): ?>
<hr class="admin-filter-line"<?php if ($filter_is_visible) echo ' style="display:none;"'; ?>>
<?php endif; ?>
