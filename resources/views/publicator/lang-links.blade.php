@if( isset($item) && $item )
<div class="lang-links">
@foreach( $item->langLinks() as $locale => $link )
<a href="{{ $link['url'] }}" class="lang-link fa fa-{{ $link['icon'] }}">&nbsp;{{ $link['text'] }}</a>
@endforeach
</div>
@endif
