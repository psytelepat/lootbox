<div class="cntBlk gallery" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk photoBlk">
    <span class="line fileUpload">
@if( $block )
{!! ContentBlock::uploadField('content-block.gallery',$block) !!}
@endif
    </span>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>