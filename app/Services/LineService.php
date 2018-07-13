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

        $message = $this->getEchoBack($json['message']);
        $this->push($json['userId'], ['type'=>'text', 'text'=>$message]);

        sleep(rand(1, 3));

        $email = $this->getTransEmailFromUserId($json['userId']);
        $message = $this->getInvitation($email);
        $this->push($json['userId'], ['type'=>'text', 'text'=>$message]);
    }

    const EMAIL_REGEXP = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    const TOKEN_REGEXP = '/^[0-9]{5}$/';

    const MD5_TOKEN_SALT = 'pagyQK2XMWDqpH6rP0q0z1U0drtjifsB';


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

    static function getToken($user_id){
        return md5($user_id);
    }

    function getTransEmailFromUserId($user_id)
    {

        $obj = $this->emailModel->whereUserId($user_id)->first() ?: (new Email);

        $obj->user_id = $user_id;
        $obj->activate = 0;
        $obj->token = self::getToken($user_id);
        $obj->save();

        return  self::getTransEmail($obj->token);
    }

    function getInvitation($transEmail){

        return <<<EOD
さて、秘密のメアド
$transEmail
を授けるにゃ。何か送ってみてにゃ。
EOD;
    }

    function getEchoBack($text){

        return array_random([
            "{$text}ってなんにゃ？",
            "{$text}。。知らないにゃ。。",
            "www",
            "その話はあとにゃ",
            "{$text}って言いたかったんにゃね",
            "いい話にゃねぇ。。",
            "。。。",
            "それいま言う必要あるにゃか？",
            "melcoは {$text} を覚えた",
            "{$text}！{$text}！{$text}！",
            "Internal Server Error. Undefined cat nya."
        ]);
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




