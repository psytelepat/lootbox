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
<body>
<div id="wrapper">
    {!! $navbar !!}
    <div id="page-wrapper" class="gray-bg">
        {!! $top_navbar !!}
        {!! $header !!}
        {!! $content !!}
        <br><br><br>
        {!! $footer !!}
    </div>
</div>
{!! $modals !!}
{!! $scripts_in_footer !!}
</body>
</html>
