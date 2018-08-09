<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class User extends Model
{
    protected $guarded =  ['id'];

    protected $casts = [
        'images' => 'array',
    ];

    function address(){
        return $this->belongsTo(Address::class);
    }

    function birthPlace(){
        return $this->belongsTo(Address::class, 'birth_place_id');
    }



    function annualIncome(){
        return $this->belongsTo(AnnualIncome::class);
    }

    function blood(){
        return $this->belongsTo(Blood::class);
    }

    function drinking(){
        return $this->belongsTo(Drinking::class);
    }

    function educationalBackground(){
        return $this->belongsTo(EducationalBackground::class);
    }

    function figure(){
        return $this->belongsTo(Figure::class);
    }

    function haveChild(){
        return $this->belongsTo(HaveChild::class);
    }

    function holiday(){
        return $this->belongsTo(Holiday::class);
    }

    function sendMessages(){
        return $this->hasMany(Message::class);
    }

    function receiveMessages(){
        return $this->hasMany(Message::class);
    }

    function requestUntilMeet(){
        return $this->belongsTo(RequestUntilMeet::class);
    }

    function job(){
        return $this->belongsTo(Job::class);
    }

    function smoking(){
        return $this->belongsTo(Smoking::class);
    }

    function getImagePathsAttribute(){
        $ret = [];
        foreach($this->images as $image){
            $ret[] = UserImage::getUrl($image);
        }
        return $ret;
    }

    function getThumbnailPathsAttribute(){
        $ret = [];
        foreach($this->images as $image){
            $ret[] = UserImage::getUrl($image, true);
        }
        return $ret;
    }


    /*
    function userImages(){
        $this->hasMany(UserImage::class);
    }
    */


}
