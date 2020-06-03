{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
{!! Former::text('group') !!}
{!! Former::text('key') !!}
@if( isset($model) )  
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Используется в ролях</label>
    <div class="col-sm-10">
    @foreach( $model->roles as $role )
    <a href="{{ route('lootbox.roles.edit', [ 'id' => $role->id ]) }}" class="btn btn-success" title="{{ $role->name }}">{{ $role->display_name }}</a>
    @endforeach
    </div>
</div>
@endif
@include('lootbox::admin.form-actions', [ 'route' => 'lootbox.permissions', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close() !!}