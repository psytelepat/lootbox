<div class="cntBlk video" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk videoBlk">
    <span class="line code">
      <label>{{ trans('lootbox::content-block.form.video_code') }}</label><br>
      <textarea class="code-fld" name="code">{!! $block->code !!}</textarea>
    </span>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>