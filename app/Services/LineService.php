<?php

namespace App\Services;

use App\Models\Email;
use App\Mail\Simple;
use Mail;

class LineService
{

    protected $accessToken;
    protected $emailModel;

    function __construct(Email $emailModel)
    {
        $this->emailModel = $emailModel;
        $this->accessToken = env('LINE_ACCESS_TOKEN');
    }

    function main($jsonString){

        $json = $this->jsonDecode($jsonString);

        \Log::debug($json);

        $mode = $this->checkMode($json['message']);

        $messageData = $this->$mode($json['userId'],  $json['message']);

        $this->reply($json['replyToken'], $messageData);

    }

    const EMAIL_REGEXP = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    const TOKEN_REGEXP = '/^[0-9]{5}$/';


    protected function jsonDecode($jsonString){

        \Log::debug($jsonString);

        $jsonObj = json_decode($jsonString);

        return [
            'userId'=> $jsonObj->events[0]->source->userId,
            'message' => $jsonObj->events[0]->message->text,
            'replyToken' => $jsonObj->events[0]->replyToken
        ];
    }


     function checkMode($messageText){

        if(preg_match(self::EMAIL_REGEXP, $messageText)){
            $ret = 'registerEmail';
        }else if(preg_match(self::TOKEN_REGEXP, $messageText)){
            $ret = 'checkToken';
        }else{
            $ret = 'other';
        }

        return $ret;
    }

    static function getTransEmail($token){

        return str_replace('(.{32})', $token, config('app.trans_token_regexp'));
    }

    function registerEmail($user_id, $email){

        $obj = $this->emailModel->whereEmail($email)->first() ?: (new Email);

        $obj->user_id = $user_id;
        $obj->email = $email;
        $obj->activate = 0;
        $obj->token = md5($email);
        $obj->save();

         $transEmail = self::getTransEmail($obj->token);

        $body = "
ご利用ありがとうございます。

送信先メールアドレスは
{$transEmail}
です。

";
        Mail::send(new Simple([
            'from' => 'noreply@'.config('app.email_domain'),
            'from_jp' => config('app.site_name'),
            'to' => $email,
            'subject' => 'パスコードを送信します',
            'body'=>  $body
        ]));

        Mail::send(new Simple([
            'from' => $email,
            'from_jp' => '',
            'to' => config('app.admin_email'),
            'subject' => config('app.site_name').' 利用あり',
            'body'=> $body
        ]));

        return [
            'type' => 'text',
            'text' => $email . 'に送信用urlをお送りしました'
        ];

    }

    /*
    private function checkToken($user_id, $token){

        $obj = $this->emailModel
            ->whereUserId($user_id)
            ->whereToken($token)->first();

        if($obj){

            $obj->activate = 1;
            $obj->save();

            return [
                'type' => 'text',
                'text' =>  'パスコードは承認されました'
            ];
        }else{

            return [
                'type' => 'text',
                'text' =>  'パスコードは一致しません'
            ];
        }

    }
    */

    private function other($user_id, $text){

        return [
            'type' => 'text',
            'text' => $text . 'ってなんですか？'
        ];
    }

    function receiveEmail($email){

        \Log::debug(json_encode($email));

        $obj = $this->emailModel
            ->whereToken($email['token'])
            ->whereActivate(1)
            ->first();

        if($obj){

            $this->push($obj->user_id, ['type'=>'text', 'text' => "【{$email['subject']}】\n{$email['text']}"]);

            return [$obj->toArray(), $email];
        }

        return false;
    }

    function push($user_id, $messageData)
    {
        $response = [
            "to" => $user_id,
            "messages" => [$messageData]
        ];

        \Log::debug(json_encode($response));

        $ch = curl_init('https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charser=UTF-8',
            'Authorization: Bearer ' . $this->accessToken
        ));
        $result = curl_exec($ch);
        \Log::debug($result);
        curl_close($ch);

        return $result;
    }

    private function reply($replyToken, $messageData)
    {
        $response = [
            'replyToken' => $replyToken,
            'messages' => [$messageData]
        ];

        \Log::debug(json_encode($response));

        $ch = curl_init('https://api.line.me/v2/bot/message/reply');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charser=UTF-8',
            'Authorization: Bearer ' . $this->accessToken
        ));
        $result = curl_exec($ch);
        \Log::debug($result);
        curl_close($ch);

        return $result;
    }

}


/*

// 送られてきたメッセージの中身からレスポンスのタイプを選択
if ($message->{"text"} == '確認') {
    // 確認ダイアログタイプ
    $messageData = [
        'type' => 'template',
        'altText' => '確認ダイアログ',
        'template' => [
            'type' => 'confirm',
            'text' => '元気ですかー？',
            'actions' => [
                [
                    'type' => 'message',
                    'label' => '元気です',
                    'text' => '元気です'
                ],
                [
                    'type' => 'message',
                    'label' => 'まあまあです',
                    'text' => 'まあまあです'
                ],
            ]
        ]
    ];
} elseif ($message->{"text"} == 'ボタン') {
    // ボタンタイプ
    $messageData = [
        'type' => 'template',
        'altText' => 'ボタン',
        'template' => [
            'type' => 'buttons',
            'title' => 'タイトルです',
            'text' => '選択してね',
            'actions' => [
                [
                    'type' => 'postback',
                    'label' => 'webhookにpost送信',
                    'data' => 'value'
                ],
                [
                    'type' => 'uri',
                    'label' => 'googleへ移動',
                    'uri' => 'https://google.com'
                ]
            ]
        ]
    ];
} elseif ($message->{"text"} == 'カルーセル') {
    // カルーセルタイプ
    $messageData = [
        'type' => 'template',
        'altText' => 'カルーセル',
        'template' => [
            'type' => 'carousel',
            'columns' => [
                [
                    'title' => 'カルーセル1',
                    'text' => 'カルーセル1です',
                    'actions' => [
                        [
                            'type' => 'postback',
                            'label' => 'webhookにpost送信',
                            'data' => 'value'
                        ],
                        [
                            'type' => 'uri',
                            'label' => '美容の口コミ広場を見る',
                            'uri' => 'http://clinic.e-kuchikomi.info/'
                        ]
                    ]
                ],
                [
                    'title' => 'カルーセル2',
                    'text' => 'カルーセル2です',
                    'actions' => [
                        [
                            'type' => 'postback',
                            'label' => 'webhookにpost送信',
                            'data' => 'value'
                        ],
                        [
                            'type' => 'uri',
                            'label' => '女美会を見る',
                            'uri' => 'https://jobikai.com/'
                        ]
                    ]
                ],
            ]
        ]
    ];
} else {
    // それ以外は送られてきたテキストをオウム返し
    $messageData = [
        'type' => 'text',
        'text' => $message->{"text"}
    ];
}


*/



