<div class="cntBlk photo {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
  <div class="cntBlkImg">
@if( $image = $block->randomImage(2) )
  <img src="{{ $image->url() }}" data-width="{{ $image->w }}" data-height="{{ $image->h }}" alt="{{ $image->description }}" title="{{ $image->title }}" />
@else
    <i>{{ trans('lootbox::content-block.empty-type-2') }}</i>
@endif
  </div>
  @if( isset( $cnt ) && $cnt )<div class="sign">{!! $cnt !!}</div>@endif
</div>