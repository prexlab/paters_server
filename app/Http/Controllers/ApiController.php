<?php

namespace App\Http\Controllers;

use App\Services\LineService;

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

    public function index()
    {
        return 1;
    }


    public function callBack(){

        $jsonString = file_get_contents('php://input');

        return $this->line->main($jsonString);
        
    }

    public function receiveEmail(){

        $mime = new MailDecoderService;
        $stdin = file_get_contents('php://input');
        $email = $mime->myMimeDecode($stdin);

        return $this->line->receiveEmail($email);
    }

}
