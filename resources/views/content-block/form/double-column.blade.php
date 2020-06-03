@php
$content = json_decode($block->content,true);
@endphp
<div class="cntBlk double-column" grp="{{ $block->grp }}" mode="{{ $block->mode }}" state="{{ $data->state }}">
  <span class="cntBlk doucleColumnBlk">
  	<div class="row">
  		<div class="col">
		  	<b>До</b><br><br>
		    <span class="line cnt">
		      <textarea class="cnt-fld1" name="content1">{!! array_get($content,'content1') !!}</textarea>
		    </span>
		</div>
		<div class="col">
		    <b>После</b><br><br>
		    <span class="line cnt">
		      <textarea class="cnt-fld2" name="content2">{!! array_get($content,'content2') !!}</textarea>
		    </span>
		</div>
    </div>
  </span>
  <div class="btn btn-success btn-sm saveBlock" title="{{ trans('lootbox::content-block.form.save') }}"><i class="fa fa-save"></i> {{ trans('lootbox::content-block.form.save') }}</div>
</div>