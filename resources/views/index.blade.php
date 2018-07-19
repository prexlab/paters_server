@extends('layouts.app')

@section('head')

<style>

    #melco-face{
        width: 200px;
    }

    .profile{
        text-align: center;
    }

    .screen-shot{
        width: 100%;
        max-width: 500px;
    }

    .friend{
        margin: 2rem;
    }

    .friend p{
        margin: 0;
        font-weight: bold;
    }



</style>

@endsection

@section('content')

<div class="profile">
    <h2 class="profile-user-name">
        Mail 2 LINE = Melco
    </h2>
        <img src="/assets/img/melco-face.png" class="img-circle" id="melco-face" >
    <div class="friend">
        <a href="https://line.me/R/ti/p/%40xfn8428u">
            <img src="/assets/img/qr.png" >
        </a>
        <p >
            <a href="https://line.me/R/ti/p/%40xfn8428u">
                {{ config('app.site_name') }}とお友達になる
            </a>
        </p>
    </div>


</div>


<p>LINEとメールを連携するLINE BOTです。指定のメールアドレスにメール送信すると、{{ config('app.site_name') }}がLINEで通知してくれます。</p>
<p>外部システム連携などに活用ください。</p>

<div>
    <img src="/assets/img/screenshot.png" class="screen-shot" >
</div>

@endsection

