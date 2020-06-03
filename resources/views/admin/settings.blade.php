<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 py-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lootbox.dashboard') }}">{{ __('lootbox::common.dashboard') }}</a></li>
        </ol>
        <h2>{{ __('lootbox::common.settings') }}</h2>
    </div>
</div>

<div class="row wrapper wrapper-content white-bg">
    <div class="col-lg-12">
        {!! Former::open('/admin/settings')->method('post')->enctype('multipart/form-data')->data_form_mode('edit') !!}
        @csrf
        @foreach( $list as $section_tid => $section)
        <div class="form-group row">
            <div class="col-12">
                <h3>{{ $section['title'] }}</h3>
            </div>
        </div>
        @if( ($fields = array_get($section,'fields')) &&  is_array($fields) && !empty($fields) )
        @foreach( $fields as $field_tid => $field )
        @switch( array_get($field,'type','default') )
            @case('checkbox')
            {!! Former::checkbox($field_tid)->label(array_get($field,'title',$field_tid)) !!}
            @break
            @case('textarea')
            {!! Former::textarea($field_tid)->label(array_get($field,'title',$field_tid)) !!}
            @break
            @case('number')
            {!! Former::number($field_tid)->label(array_get($field,'title',$field_tid)) !!}
            @break
            @case('file')
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">{{ array_get($field,'title',$field_tid) }}</label>
                <div class="col-sm-10">
@if( isset($model) && array_key_exists($field_tid,$model) && $model[$field_tid] )
                    Загружен файл: <a href="{{ Storage::url(array_get($field,'path') . array_get($model,$field_tid)) }}">{{ array_get($model,$field_tid) }}</a>
                    &nbsp;&nbsp;<label><input type="checkbox" name="delete_{{ $field_tid }}" value="1" /> удалить</label>
                    <hr>
@endif
                    <input type="file" name="{{ $field_tid }}" />
                </div>
            </div>
            @break
            @default
            {!! Former::text($field_tid)->label(array_get($field,'title',$field_tid)) !!}
            @break
        @endswitch
        @endforeach
        <div class="hr-line-dashed"></div>
        @endif
        @endforeach
        <div class="form-group row">
            <div class="col-sm-4 col-sm-offset-2">
                <button class="btn btn-primary btn-sm" type="submit">Сохранить</button>
            </div>
        </div>
        {!! Former::close() !!}
    </div>
</div>