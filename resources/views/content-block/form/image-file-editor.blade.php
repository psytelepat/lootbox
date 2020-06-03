<div class="modal-content">
	<div class="modal-header">
        <h5 class="modal-title">Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
    	{!! Former::open()->addClass('js-form')->action($form_path)->onsubmit('return false;')->method('POST') !!}
			{!! Former::textarea('title')->addClass('js-title')->value($file->title) !!}
			{!! Former::textarea('description')->addClass('js-description')->value($file->description) !!}
			{!! Former::textarea('alt')->addClass('js-alt')->value($file->alt) !!}
			{!! Former::textarea('href')->addClass('js-href')->value($file->href) !!}
		{!! Former::close() !!}
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('lootbox::common.cancel') }}</button>
        <button type="button" class="btn btn-primary js-submit">{{ __('lootbox::common.save') }}</button>
	</div>
</div>