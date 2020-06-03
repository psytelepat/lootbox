<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 15.01.15
 * Time: 22:03
 */
?>
<script type="text/javascript">
	<?php echo "$('#{$name}').on('shown.bs.modal', function () {
		$('#{$name}').focus();
	});"; ?>
</script>
<div<?php echo $attributes; ?>>
	<div<?php echo $modal_attributes; ?>>
		<div class="modal-content">
			<div class="modal-header">
				<?php if ($show_close): ?>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
				<?php endif; ?>
				<?php if ($show_label): ?>
					<h4 class="modal-title"><?php echo $label; ?></h4>
				<?php endif; ?>
			</div>
			<div class="modal-body">
				<?php echo $content; ?>
			</div>
			<?php if ($show_buttons): ?>
				<div class="modal-footer">
					<?php foreach ($buttons as $button): ?>
						<?php echo $button; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div><!-- /.modal -->