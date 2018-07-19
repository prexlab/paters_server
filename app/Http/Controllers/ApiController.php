<?php

namespace App\Http\Controllers;

use App\Services\LineService;
use App\Services\MailDecoderService;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $message;
    protected $replyToken;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LineService $line)
    {
        $this->line = $line;
    }


    public function callBack(){

        $jsonString = file_get_contents('php://input');

        return $this->line->main($jsonString);
        
    }

    public function receiveEmail($file_name){


        error_reporting(E_ALL & ~ E_DEPRECATED & ~ E_USER_DEPRECATED & ~ E_NOTICE);

        $path = storage_path('emails/' . $file_name);

        if (is_file($path)){

            $mime = new MailDecoderService;

            $stdin = file_get_contents($path);

            $email = $mime->myMimeDecode($stdin);

            return (string)$this->line->receiveEmail($email);

        }


    }

    public function receiveEmailTest($file_name){

        error_reporting(E_ALL & ~ E_DEPRECATED & ~ E_USER_DEPRECATED & ~ E_NOTICE);

        $path = storage_path('emails/' . $file_name);

        if (is_file($path)){

            $mime = new MailDecoderService;

            $stdin = file_get_contents($path);

            $email = $mime->myMimeDecode($stdin);

            dump($email);
        }
    }

    public function receiveEmailTestAll(){

        $files = glob(storage_path('emails/*.txt'));
        foreach($files as $f){
            echo basename($f);
            $this->receiveEmailTest(basename($f));
        }

    }

}
