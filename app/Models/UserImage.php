<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;


class UserImage extends Model
{
    protected $guarded =  ['id'];

    const THUMBNAIL_FLAG = 1;

    const THUMBNAIL_DIR = 'th/';
    const BASE_DIR = 'users/';
    const STORE_PATH = 'public/' . self::BASE_DIR;
    const STORAGE_PATH = 'app/public/' . self::BASE_DIR;
    const STORAGE_URL = '/storage/' . self::BASE_DIR;

    static function getAbsolutePath($basename, $thumbnailFlag = false){
        $dir = $thumbnailFlag ? self::THUMBNAIL_DIR : '';
        return storage_path(self::STORAGE_PATH . $dir . $basename);
    }

    static function getUrl($basename, $thumbnailFlag = false){
        $dir = $thumbnailFlag ? self::THUMBNAIL_DIR : '';
        return self::STORAGE_URL . $dir . $basename;
    }

    static function makeThumbnail($basename){

        $path = UserImage::getAbsolutePath($basename);
        $image = Image::make($path);
        $image->resize(900, null)->save($path);

        $image = Image::make(UserImage::getAbsolutePath($basename));
        $image->fit(200, 200)->save(UserImage::getAbsolutePath($basename, true));
    }
}
