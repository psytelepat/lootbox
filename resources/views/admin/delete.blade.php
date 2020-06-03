{!! $pageHeading ?? null !!}

<div class="row wrapper wrapper-content white-bg">
    <div class="col-lg-12">
		{!! Former::open( url()->current() )->method('post') !!}
		@csrf
        <div class="form-group row">
            <div class="col-sm-4 col-sm-offset-2">
                <a class="btn btn-white btn-sm" href="{{ url()->previous() }}">{{ __('lootbox::common.cancel') }}</a>
                <button class="btn btn-danger btn-sm" type="submit">{{ __('lootbox::common.delete') }}</button>
            </div>
        </div>
		{!! Former::close() !!}
	</div>
</div>