<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UploadRequest;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Support\Facades\Log;

class UploadController extends ApiController
{

    public function __construct()
    {
    }



    public function upload(UploadRequest $request)
    {

        try{
            //php artisan storage:link
            // storage/app/public/users/3vULtKZWjtwpqL6ekZSnYT0lm9TDoShGGX5qjxxa.jpeg
            // http://localhost:9970/storage/users/B5SldJmwmUuaeyRjjkWelvUBoWCQ5CU2cxdERf3B.jpeg
            $filename = $request->file('image')->store(UserImage::STORE_PATH);
            $filename = basename($filename);

            $user = User::find(1);
            $user->images = [$filename];
            $user->save();

            $user->thumbnailPaths = $user->thumbnail_paths;

            UserImage::makeThumbnail($filename);

            Log::debug(print_r([$request->file('image'), $_POST, $_FILES, $_SERVER], 1));

            return $this->responseJsonSuccess($user->toArray());

        }catch(\Exception $e){
            return $this->responseJsonFailed($e->getMessage());
        }

    }


}
