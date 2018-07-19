<html>
<head>
    <title>@hasSection('title')
        @yield('title')|{{ config('app.site_name_long') }}
        @else
        {{ config('app.site_name_long') }}
        @endif
        </title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css?{{ uniqid() }}" rel="stylesheet">
    <link href="/assets/css/nikukyu.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>

    @section('head')
    @show

</head>
<body>

<a name="top" id="top"></a>

@include('layouts.header')

<div class="container">
    @yield('content')
</div>

@include('layouts.footer')

</body>
</html>