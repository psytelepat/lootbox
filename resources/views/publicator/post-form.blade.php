{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
@if( $form_mode == 'create' )
{!! Former::select('lng')->options(\Psytelepat\Lootbox\Util::locale_choices(), isset($model) ? $model->lng : 0)->label('Язык') !!}
@endif
{!! Former::text('title') !!}
{!! Former::text('slug')->prepend(route('lootbox.publicator.index').'/')->addGroupClass('slug-input') !!}
@if( $form_mode == 'edit' )
<div class="form-group row">
    <label class="col-sm-2 col-form-label"></label>
    <div class="col-sm-10">
        Адрес на сайте: <a href="{{ \App\Http\Controllers\MainController::publicator_post_url($model) }}">{{ \App\Http\Controllers\MainController::publicator_post_url($model) }}</a>
    </div>
</div>
@endif
@if( config('publicator.with_categories') )
{!! ( isset($categories) && $categories->count() ) ? Former::choice('category_id')->fromQuery($categories,'title','id', isset($model) ? $model->category_id : 0)->label('Категория') : null !!}
@endif
<div class="form-group row js-datepicker">
    <label class="col-sm-2 col-form-label">Дата</label>
    <div class="col-sm-3">
        <div class="input-group date">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="created_at_date" value="{{ ( isset($model) && $model ) ? $model->created_at->format('d.m.Y') : date('d.m.Y') }}">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="input-group clockpicker js-clockpicker" data-autoclose="true">
            <span class="input-group-addon"><span class="fa fa-clock"></span></span>
            <input type="text" name="created_at_time" class="form-control" value="{{ ( isset($model) && $model ) ? $model->created_at->format('H:i') : date('H:i') }}">
        </div>
    </div>
</div>
@if( $form_mode === 'edit' )
@if( config('publicator.with_tags') )
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Теги</label>
    <div class="col-sm-10"><input type="text" name="tags" class="tagsinput form-control" data-role="tagsinput" data-typeahead-source="{{ isset($tags_typeahead_source) ? $tags_typeahead_source : '' }}" value="{{ old('tags', ( isset($model) ? implode(',',$model->tags->pluck('name')->toArray()) : '')) }}"></div>
</div>
@endif
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Опубликовано</label>
    <div class="col-sm-10">
        <div class="i-checks"><label><input type="checkbox" name="is_published" value="1" {{ old('is_published', ( isset($model) ? $model->is_published : null )) ? ' checked' : '' }}/><i></i><span style="vertical-align: middle;"> &nbsp; Отображать публикацию на сайте</span></label></div>
    </div>
</div>
@if( config('publicator.with_authors') )
{!! ( isset($authors) && $authors->count() ) ? Former::choice('author_id')->choices([ '0' => '-- Без автора --'])->fromQuery($authors,'name','id',isset($model) ? $model->author_id : 0)->label('Автор') : null !!}
@endif
{!! Former::textarea('description')->addClass('js-redactor')->label('Краткое описание') !!}
{!! config('publicator.with_post_content_field') ? Former::textarea('content')->addClass('js-redactor')->label('Расширенное описание') : null !!}
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Обложка</label>
    <div class="col-sm-10">
        {!! Lootbox::uploadField('publicator.post.cover',$model) !!}
    </div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Контент</label>
    <div class="col-sm-10">
        {!! $model->contentBlockEditor() !!}
    </div>
</div>
<div class="hr-line-dashed"></div>
{!! Former::text('seo_title') !!}
{!! Former::text('seo_description') !!}
{!! Former::text('seo_keywords') !!}
<div class="form-group row">
    <label class="col-sm-2 col-form-label">OpenGraph Image</label>
    <div class="col-sm-10">
        {!! Lootbox::uploadField('publicator.post.seo',$model) !!}
    </div>
</div>
@endif
@include('lootbox::admin.form-actions', [ 'route' => 'lootbox.publicator.post', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close() !!}