<div class="cntBlk text" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk quoteBlk">
    <span class="line cnt">
      <textarea class="cnt-fld" name="content">{!! $block->content !!}</textarea>
    </span>
    <span class="line input">
    	<input type="text" class="title-fld" name="title" value="{{ $block->title }}">
    </span>
    <span class="line input">
    	<input type="text" class="description-fld" name="description" value="{{ $block->description }}">
    </span>
    <span class="line fileUpload">
@if( $block )
{!! ContentBlock::uploadField('content-block.quote',$block) !!}
@endif
    </span>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>