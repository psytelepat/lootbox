<!DOCTYPE html>
<html lang="{{ Lang::getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title }}</title>
    {!! $styles !!}
    {!! $scripts_in_header !!}
</head>
<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <h2>{{ config('site-settings.site_title') }}</h2>
            <form class="m-t" role="form" action="{{ route('lootbox.login') }}" method="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <input name="email" type="email" class="form-control" placeholder="Username" value="" required="">
                </div>
                <div class="form-group">
                    <input name="password" type="password" class="form-control" placeholder="Password" value="" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">Войти</button>
            </form>
        </div>
    </div>
{!! $scripts_in_footer !!}
</body>
</html>
