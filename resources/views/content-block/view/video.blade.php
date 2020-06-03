<div class="cntBlk video {{ $data->css }}" grp="{{ $block->grp }}" pos="{{ $block->pos }}" mode="{{ $block->mode }}" state="0">
@if( strlen($block->code) )
  {!! $block->parseVideoCode() !!}
@else
    <i>{{ trans('lootbox::content-block.empty-type-4') }}</i>
@endif
  @if( isset( $cnt ) && $cnt )<div class="sign">{!! $cnt !!}</div>@endif
</div>