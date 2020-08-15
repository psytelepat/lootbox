<div class="cntBlk video" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk videoBlk">
    <div class="form-group row">
      <div class="col-12"><input class="form-control code-fld" name="code" type="text" value="{{$block->code}}" placeholder="ID Vimeo видео"></div>
    </div>
    <span class="line fileUpload">
@if( $block )
{!! ContentBlock::uploadField('content-block.video',$block) !!}
@endif
    </span>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>