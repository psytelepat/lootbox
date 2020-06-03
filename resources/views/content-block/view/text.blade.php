<div class="cntBlk text {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
@if( strlen(strip_tags($block->content)) )
  {!! $block->content !!}
@else
    <i>{{ trans('lootbox::content-block.empty-type-1') }}</i>
@endif
</div>