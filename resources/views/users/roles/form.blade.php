{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
{!! Former::text('name') !!}
{!! Former::text('display_name') !!}
@if( isset($model) )
<div class="hr-line-dashed"></div>
@php
$has_permissions = ( isset($model) && $model ) ? $model->permissions->pluck('id')->toArray() : [];
@endphp
@foreach( \Psytelepat\Lootbox\User\Permission::all() as $permission )
<div class="form-group row">
    <div class="col-sm-12">
        <div class="i-checks"><label> <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"{{ in_array($permission->id, $has_permissions) ? ' checked' : '' }} /> <i></i> <a href="{{ route('lootbox.permissions.edit', [ 'id' => $permission->id ]) }}" title="{{ $permission->key }}">{{ $permission->key }}</a> </label></div>
    </div>
</div>
@endforeach
@endif
@include('lootbox::admin.form-actions', [ 'route' => 'lootbox.roles', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close(); !!}