<div class="cntBlk gallery {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
@if( ( $count = count($images = $block->images(2))) )
<div class="slider js-slider">
  <div class="stage" data-resize-mode="16x9" data-clickable="1">
@foreach( $images as $image )
    <div class="slide fxRollX" data-src="{{ $image->url() }}"></div>
@endforeach
  </div>
@if( $count > 1 )
  <div class="arr arrl js-prev"><svg><use xlink:href="#slider-arrl"></svg></div>
  <div class="arr arrr js-next"><svg><use xlink:href="#slider-arrr"></svg></div>
@endif
</div>
@else
    <i>{{ trans('lootbox::content-block.empty-type-3') }}</i>
@endif
</div>