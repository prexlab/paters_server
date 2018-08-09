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

    function smoking(){
        return $this->belongsTo(Smoking::class);
    }

    /*
    function userImages(){
        $this->hasMany(UserImage::class);
    }
    */


}
