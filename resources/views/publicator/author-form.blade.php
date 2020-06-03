{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
@if( $form_mode == 'create' )
{!! Former::select('lng')->options(\Psytelepat\Lootbox\Util::locale_choices(), isset($model) ? $model->lng : 0)->label('Язык') !!}
@endif
{!! Former::text('name') !!}
@if( $form_mode === 'edit' )
<div class="form-group row">
    <label class="col-sm-2 col-form-label">{{ __('validation.attributes.avatar') }}</label>
    <div class="col-sm-10">
        {!! Lootbox::uploadField('publicator.author.avatar',$model) !!}
    </div>
</div>
@endif
{!! Former::textarea('description')->addClass('js-redactor') !!}
<div class="hr-line-dashed"></div>
{!! Former::text('ig_url') !!}
{!! Former::text('fb_url') !!}
{!! Former::text('vk_url') !!}
{!! Former::text('tw_url') !!}
{!! Former::text('yt_url') !!}
 @include('lootbox::admin.form-actions', [ 'route' => 'lootbox.publicator.author', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close() !!}