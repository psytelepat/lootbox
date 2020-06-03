<div class="cntBlk quote {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
@if( strlen(strip_tags($block->content)) )
  {!! $block->content !!}
  <div class="quote-person">
	  @if( $image = $block->quoteImage() )
	  <div class="quote-image" style="background-image: url({{ $image->url() }})"></div>
	  @endif
	  <div class="person">
	  		<div class="title">{{ $block->title }}</div>
	  		<div class="description">{{ $block->description }}</div>
	  </div>
  </div>
@else
    <i>{{ trans('lootbox::content-block.empty-type-5') }}</i>
@endif
</div>