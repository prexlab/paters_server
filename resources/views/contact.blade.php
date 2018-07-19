@extends('layouts.app')

@section('title', 'お問い合わせ')

@section('head')

    <script>
        $(function(){
            $('#nav_contact').addClass('active');
        });
    </script>

@endsection

@section('content')

<?php
use App\Services\Form;
?>

    <h1>お問い合わせ</h1>

    <p><?=config('app.site_name'); ?> に関するお問い合わせはこちらからご相談ください。</p>
    <p>&nbsp;</p>

    <?php if($success === true){ ?>

        <div class="alert alert-info" style="margin-top:10px">お問い合わせありがとうございました。</div>
    <?php }else if($success === false){ ?>
        <div class="alert alert-danger" style="margin-top:10px">入力エラー：必須項目の入力を確認してください。</div>
    <?php } ?>

    <form method="post" id="input_table"  >

    {{ csrf_field() }}

        <div class="form-group">
            <label >お名前 <span class="badge">必須</span></label><div >
                <input class="form-control" type="text" id="namae" name="namae" value="<?=$request->input('namae'); ?>" >
            </div>
        </div>

        <div class="form-group">
            <label >会社名</label><div >
                <input class="form-control" type="text" id="company_name" name="company_name" value="<?=$request->input('company_name'); ?>" >
            </div>
        </div>

        

        <div class="form-group">
            <label >メールアドレス <span class="badge">必須</span></label><div >
                <input class="form-control" type="email" id="email" name="email" value="<?=$request->input('email'); ?>" >
            </div>
        </div>

        <div class="form-group">
            <label >電話番号</label><div >
                <input class="form-control" type="text" id="tel" name="tel" value="<?=$request->input('tel'); ?>" >
            </div>
        </div>

        <div class="form-group">
            <label >ご相談内容</label><div class="checkbox" >
                <?=Form::checkbox('category', 
                ['ご利用方法'=>'ご利用方法', '不具合報告'=>'不具合報告', 'その他お問い合わせ'=>'その他お問い合わせ'],
                $request->input('category'));
                ?>
            </div>
        </div>

        <div class="form-group">
            <label >ご相談内容 <span class="badge">必須</span></label><div >
                <textarea class="form-control" id="message" name="message" ><?=$request->input('message'); ?></textarea>
            </div>
        </div>

        <div align="center">
            <input name="send" type="submit" class="btn btn-primary btn-lg" value="この内容でお問い合わせする" >
        </div>
    </form>

@endsection

