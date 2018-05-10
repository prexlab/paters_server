<?php
$down_payment = null;
$loan_payment = null;
$rate_of_drop = null;
$admin_repair_payment = null;
$rate_of_land = null;
$interest_rate = null;
$payment_term = null;
$commission_rate = null;
$other_cost_of_buy = null;
?>
<html>
<head>
    <title>@yield('title')|{{ config('app.site_name_long') }}</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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