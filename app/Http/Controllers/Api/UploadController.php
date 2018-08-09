<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UploadRequest;

class UploadController extends ApiController
{

    public function __construct()
    {
    }



    public function upload(UploadRequest $request)
    {
        //php artisan storage:link
        //http://localhost:9970/storage/users/B5SldJmwmUuaeyRjjkWelvUBoWCQ5CU2cxdERf3B.jpeg
        $filename = $request->file('image')->store('public/users');

        file_put_contents('upload.txt', print_r([$request->file('image'), $_POST, $_FILES], 1));

        $ret = ['status'=>'ok', 'file'=>$filename];

        return $this->responseJsonSuccess($ret);
    }


}
