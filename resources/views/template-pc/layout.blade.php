<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="google-site-verification" content="qcHXYlFmIacWcnkz0OotUEizHy0Lo6aVY5pKxfYgQl4" />
    <link rel="icon" href="/favicon.ico" type="image/gif" sizes="16x16">
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')"/>
    @include('template-pc.includes.style')
    @include('template-pc.includes.metageo')
</head>
<body>
    @include('template-pc.blocks.header')
    @include('template-pc.blocks.content')
    @include('template-pc.blocks.footer')

</body>
</html>