<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 py-2">
        <ol class="breadcrumb">
        	<li class="breadcrumb-item"><a href="{{ route('lootbox.dashboard') }}">{{ __('lootbox::common.dashboard') }}</a></li>
@foreach( $breadcrumbs as $breadcrumb )
            <li class="breadcrumb-item"><a href="{{ $breadcrumb['route'] }}">{{ $breadcrumb['name'] }}</a></li>
@endforeach
        </ol>
        <h2>{!! $title !!}</h2>
    </div>
</div>