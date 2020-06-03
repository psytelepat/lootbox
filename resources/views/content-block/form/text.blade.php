<div class="cntBlk text" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk textBlk">
    <span class="line cnt">
      <textarea class="cnt-fld" name="content">{!! $block->content !!}</textarea>
    </span>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>