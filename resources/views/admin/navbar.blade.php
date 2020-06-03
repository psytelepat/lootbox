<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ $user_name }}&nbsp;<b class="caret"></b></strong>
                            </span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="{{ route('lootbox.profile') }}">{{ trans('lootbox::common.profile') }}</a></li>
                        <li><a href="{{ route('lootbox.logout') }}">{{ trans('lootbox::common.logout') }}</a></li>
                    </ul>
                </div>
                <div class="logo-element">FC</div>
            </li>
@foreach( $menu as $item )
            <li class="{{ $item['active'] ? 'active' : '' }}">
                <a href="{{ array_get($item,'url','#') }}"><i class="{{ array_get($item,'icon') }}"></i><span class="nav-label">{{ $item['title'] }}</span> @if($item['has_items'])<span class="fa arrow"></span>@endif</a>
@if( $item['has_items'] )
                <ul class="nav nav-second-level">
@foreach( $item['items'] as $item2 )
                    <li class="{{ $item2['active'] ? 'active' : '' }}">
                        <a href="{{ array_get($item2,'url','#') }}"><i class="{{ array_get($item2,'icon') }}"></i><span class="nav-label">{{ $item2['title'] }}</span>@if($item2['has_items'])<span class="fa arrow"></span>@endif
                            {!! array_key_exists('after',$item2) && is_callable($item2['after']) ? call_user_func($item2['after']) : '' !!}
                        </a>
@if( $item2['has_items'] )
                        <ul class="nav nav-third-level">
@foreach( $item2['items'] as $item3 )
                            <li class="{{ $item3['active'] ? 'active' : '' }}"><a href="{{ array_get($item3,'url','#') }}"><i class="{{ array_get($item3,'icon') }}"></i><span class="nav-label">{{ $item3['title'] }}</span></a></li>
@endforeach
                        </ul>
@endif
                    </li>
@endforeach
                </ul>
@endif
            </li>
@endforeach
        </ul>
    </div>
</nav>