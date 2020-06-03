<div class="modal-content">
	<div class="modal-header">
        <h5 class="modal-title">Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
    	<div class="cropper js-cropper" data-src="{{ $file->url() }}" data-target-width="{{ $targetWidth }}" data-target-height="{{ $targetHeight }}" data-target-aspect="{{ $targetAspect }}"></div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lootbox::common.cancel') }}</button>
        <button type="button" class="btn btn-primary js-submit">{{ __('lootbox::common.save') }}</button>
	</div>
</div>