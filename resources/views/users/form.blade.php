{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
{!! Former::choice('role_id')->fromQuery(\Psytelepat\Lootbox\User\Role::all(),'display_name','id')->label('Role') !!}
{!! Former::text('email') !!}
{!! Former::text('name') !!}
{!! Former::password('password')->value('') !!}
{!! Former::password('password_confirm')->value('') !!}
@include('lootbox::admin.form-actions', [ 'route' => 'lootbox.users', 'form_mode' => $form_mode, 'model' => $model ?? null ])
{!! Former::close() !!}