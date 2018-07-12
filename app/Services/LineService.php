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

        sleep(rand(0, 2));

        $messageData = $this->echoBack($json['message']);
        $this->reply($json['replyToken'], $messageData);

        sleep(rand(0, 2));

        $messageData = $this->registerAndInvitation($json['userId']);
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


    static function getTransEmail($token){

        return sprintf('line+%s@%s', $token, config('app.email_domain'));
    }

    function registerAndInvitation($user_id){

        $obj = $this->emailModel->whereUserId($user_id)->first() ?: (new Email);

        $obj->user_id = $user_id;
        $obj->activate = 0;
        $obj->token = md5($user_id);
        $obj->save();

        $transEmail = self::getTransEmail($obj->token);

        return [
            'type' => 'text',
            'text' => <<<EOD
$transEmail
にメールを送るにゃ。
EOD
        ];

    }

    private function echoBack($text){

        return [
            'type' => 'text',
            'text' => array_random(["{$text}ってなんにゃ？", "{$text}。。知らないにゃ。。", "www", "その話はあとにゃ"])
        ];
    }

    function receiveEmail($email){

        \Log::debug(json_encode($email));

        $obj = $this->emailModel
            ->whereToken($email['token'])
            ->first();

        if($obj){

            //受診時にアクティベイト。利用ごとに更新される
            $obj->activate = 1;
            $obj->save();

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



