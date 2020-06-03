{!! Former::open($form_action)->method('GET')->data_form_mode('view') !!}
@csrf
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Форма</label>
    <div class="col-sm-10">{{ __('applicated-form.id.'.$model->form_id) }}</div>
</div>
@php
$data = json_decode($model->payload);
@endphp
@foreach( $data as $key => $val )
<div class="form-group row">
    <label class="col-sm-2 col-form-label">{{ __('validation.attributes.'.$key) }}</label>
    <div class="col-sm-10">{{ $val }}</div>
</div>
@endforeach
{!! Former::close() !!}
