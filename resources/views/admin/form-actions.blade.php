<div class="hr-line-dashed"></div>
<div class="form-group row">
    <div class="col-sm-4">
        <button class="btn btn-primary" name="action_save" type="submit"><i class="fa fa-{{ ( $form_mode == 'create' ) ? 'plus-circle' : 'save' }}"></i> {{ $form_mode == 'create' ? __('lootbox::common.create_and_continue') : __('lootbox::common.save') }}</button>
        @if( $form_mode === 'edit' )
        <button class="btn btn-success" name="action_update" type="submit"><i class="fa fa-pencil-alt"></i> {{ __('lootbox::common.save_and_continue') }}</button>
        @endif
    </div>
    <div class="col-sm-4" style="text-align: center;">
        <a class="btn btn-white" href="{{ route($route.'.index') }}"><i class="fa fa-list"></i>  {{ __('lootbox::common.back_to_list') }}</a>
    </div>
    @if( isset($model) )
    <div class="col-sm-4" style="text-align: right;">
        <a class="btn btn-danger" href="{{ route($route.'.delete',$model) }}"><i class="fa fa-trash"></i>  {{ __('lootbox::common.delete') }}</a>
    </div>
    @endif
</div>