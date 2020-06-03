<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 py-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lootbox.dashboard') }}">{{ __('lootbox::common.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lootbox.profile') }}">{{ __('lootbox::common.profile') }}</a></li>
        </ol>
        <h2>{{ isset($model) ? $model->name : 'New user' }}</h2>
    </div>
</div>


<div class="row wrapper wrapper-content white-bg">
    <div class="col-lg-12">
        {!! Former::open('/admin/profile')->method('POST')->data_form_mode('edit') !!}
        <form method="POST" action="/admin/profile" data-form-mode="edit">
            @csrf
            {!! Former::text('email')->readonly(true) !!}
            {!! Former::text('name')->label('Имя') !!}
            <div class="hr-line-dashed"></div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Аватар</label>
                <div class="col-sm-10">
                    {!! Lootbox::uploadField('user.avatar', $model) !!}
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            {!! Former::password('current_password')->label('Текущий пароль') !!}
            {!! Former::password('new_password')->label('Новый пароль') !!}
            {!! Former::password('confirm_new_password')->label('Подтверджение нового пароля') !!}
            <div class="hr-line-dashed"></div>
            <div class="form-group row">
                <div class="col-sm-4 col-sm-offset-2">
                    <a class="btn btn-white btn-sm" href="{{ route('lootbox.dashboard') }}">Cancel</a>
                    <button class="btn btn-primary btn-sm" type="submit">Сохранить</button>
                </div>
            </div>
        {!! Former::close() !!}
    </div>
</div>