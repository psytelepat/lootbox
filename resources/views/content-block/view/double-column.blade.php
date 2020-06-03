@php
$content = json_decode($block->content,true);
@endphp
<div class="cntBlk double-column {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
	<div class="row">
		<div class="col"><b>До</b><br><br>{!! array_get($content,'content1') !!}</div>
		<div class="col"><b>После</b><br><br>{!! array_get($content,'content2') !!}</div>
	</div>
</div>