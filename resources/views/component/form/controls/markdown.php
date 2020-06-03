<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: ener
 * Date: 19.09.15
 * Time: 17:21
 */
$label_attributes = [
	'for' => Arr::get($attributes, 'id', $name),
	'class' => 'control-label',
];
$div_attributes = [];
if ($form_horizontal) {
	$label_attributes['class'] .= ' col-sm-2';
	$div_attributes['class'] = 'col-sm-10';
	if (!$show_label) {
		$div_attributes['class'] .= ' col-sm-offset-2';
	}
}
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label <?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div <?php echo HTML::attributes($div_attributes) ?>>
		<?php
		echo Form::textarea($name, $value, $attributes);
		if (!empty($description)) {
			echo "\n<small>" . nl2br($description) . '</small>';
		}
		?>
	</div>
</div>
<?php /*
<script type="text/javascript">
	(function () {
		$("textarea[name='<?php echo $name; ?>']").pagedownBootstrap();
		var idPreview<?php echo $name; ?> = $('textarea[name="<?php echo $name; ?>"]').attr('id').replace('input','preview');
		var idButtonRow<?php echo $name; ?> = $('textarea[name="<?php echo $name; ?>"]').attr('id').replace('input','button-row');
		var id<?php echo $name; ?> = $('textarea[name="<?php echo $name; ?>"]').attr('id').replace('wmd-input-','');
		$('#'+idButtonRow<?php echo $name; ?> + ' #wmd-button-group4-' + id<?php echo $name; ?>).after('<button class="btn btn-primary" id="preview_<?php echo $name; ?>"><i class="fa fa-search"></i></button>')

		$('#' + idPreview<?php echo $name; ?>).hide();

		$('#preview_<?php echo $name; ?>').click(function(e){
			e.preventDefault();
			$('#' + idPreview<?php echo $name; ?>).toggle();

		});
	})();
</script>
 */
