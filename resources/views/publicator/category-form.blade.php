{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
    @csrf
    @if( $form_mode == 'create' )
    {!! Former::select('lng')->options(\Psytelepat\Lootbox\Util::locale_choices(), isset($model) ? $model->lng : 0)->label('Язык') !!}
    @endif
    {!! Former::text('title') !!}
    {!! Former::text('slug')->prepend(route('lootbox.publicator.category.index').'/')->addGroupClass('slug-input') !!}
    {!! Former::checkbox('is_published')->value(1) !!}
    {!! Former::textarea('description')->addClass('js-redactor') !!}
    {!! Former::textarea('content')->addClass('js-redactor') !!}
    <div class="hr-line-dashed"></div>
    {!! Former::text('seo_title') !!}
    {!! Former::text('seo_description') !!}
    {!! Former::text('seo_keywords') !!}
     @include('lootbox::admin.form-actions', [ 'route' => 'lootbox.publicator.category', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close() !!}
