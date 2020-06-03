{!! Former::open($form_action)->method('post')->data_form_mode($form_mode) !!}
@csrf
{!! Former::text('email') !!}
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Payload</label>
    <div class="col-sm-10">
        <code>
            {!! isset($model) ? nl2br(json_encode(json_decode($model->payload),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) : null !!}
        </code>
    </div>
</div>
{!! Former::close() !!}
