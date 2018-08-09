<?php

namespace App\Http\Controllers\Api;


use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends ApiController
{

    public function __construct()
    {
    }

    public function index()
    {

        $users = User::all();
        foreach ($users as $user) {
            $user->address;
            $user->blood;
            $user->imagePaths = $user->image_paths;
            $user->thumbnailPaths = $user->thumbnail_paths;
        }

        return $this->responseJsonSuccess($users);
    }

    public function show($id)
    {

        $user = User::find($id);
        $user->address;
        $user->annualIncome;
        $user->drinking;
        $user->smoking;
        $user->figure;
        $user->haveChildren;
        $user->job;
        $user->holiday;
        $user->requireUntilMeet;

        $user->imagePaths = $user->image_paths;
        $user->thumbnailPaths = $user->thumbnail_paths;

        return $this->responseJsonSuccess($user);
    }


    public function save(Request $request)
    {
        $request = $request->all();

        $user = User::find($request['id']);

        $cols = ['name', 'age', 'tweet', 'comment', 'dream', 'address_id', 'annual_income_id', 'job_id', 'height', 'figure_id', 'smoking_id', 'drinking_id', 'request_until_meet_id', 'blood_id', 'have_child_id', 'holiday_id', 'birth_place_id', 'hobby', 'educational_background_id', 'school_name', 'job_name'];

        foreach ($cols as $col) {
            $user->$col = array_get($request['user'], $col);
        }
        $user->save();

        return $this->responseJsonSuccess($user);
    }


    public function seeder()
    {

        $seed = config('seeder.users');

        foreach ($seed as $entity => $user) {

            $obj = new User();
            $obj->id = $user['id'];
            $obj->name = $user['name'];
            $obj->images = [$user['image']];
            $obj->age = $user['age'];
            $obj->address_id = rand(1, 20);
            $obj->tweet = $user['tweet'];
            $obj->save();
        }
    }


}
