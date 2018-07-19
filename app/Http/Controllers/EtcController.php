<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\Simple;
use Mail;

class EtcController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    function contact(Request $request){

        $success = null;

        if($request->input('send')){

            if($request->input('namae') 
            && $request->input('email') 
            && $request->input('message') 
            ){
                $req = $request->all();

                $category = implode(',', $request->input('category'));

                $body = "
お名前：{$req['namae']}
会社名：{$req['company_name']}
メール：{$req['email']}
電話番号：{$req['tel']}
ご相談内容：{$category}
{$req['message']}
";

$body_head = "
{$req['namae']} 様         
 
お問い合わせありがとうございます。
担当者より１両日中にご連絡いたします。
            
";
                Mail::send(new Simple([
                    'from' => 'noreply@'.config('app.domain'),
                    'from_jp' => config('app.site_name'),
                    'to' => $req['email'],
                    'subject' => 'ご相談ありがとうごいます',
                    'body'=> $body_head . $body
                ]));

                Mail::send(new Simple([
                    'from' => $req['email'],
                    'from_jp' => '',
                    'to' => config('app.admin_email'),
                    'subject' => config('app.site_name').' ご相談あり',
                    'body'=> $body
                ]));

                $success = true;

            }else{
                $success = false;
            }
        }

        return view('contact', compact('success', 'request'));
    }

    function privacy(){
        return view('privacy');
    }

    function index(){
        return view('index');
    }

}
