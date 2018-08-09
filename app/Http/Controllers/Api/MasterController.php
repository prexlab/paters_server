<?php

namespace App\Http\Controllers\Api;


use App\Models\Address;
use App\Models\AnnualIncome;
use App\Models\Blood;
use App\Models\Drinking;
use App\Models\EducationalBackground;
use App\Models\Figure;
use App\Models\HaveChild;
use App\Models\Holiday;
use App\Models\Job;
use App\Models\RequestUntilMeet;
use App\Models\Smoking;


class MasterController extends ApiController
{

    public function __construct()
    {
    }

    public function index()
    {
        $ret = [
            'addresses' => Address::select('id', 'name')->get(),
            'annual_incomes' => AnnualIncome::select('id', 'name')->get(),
            'bloods' => Blood::select('id', 'name')->get(),
            'drinkings' => Drinking::select('id', 'name')->get(),
            'educational_backgrounds' => EducationalBackground::select('id', 'name')->get(),
            'figures' => Figure::select('id', 'name')->get(),
            'have_children' => HaveChild::select('id', 'name')->get(),
            'holidays' => Holiday::select('id', 'name')->get(),
            'jobs' => Job::select('id', 'name')->get(),
            'request_until_meets' => RequestUntilMeet::select('id', 'name')->get(),
            'smokings' => Smoking::select('id', 'name')->get(),
            'heights' => []
        ];

        foreach (config('seeder.master.heights') as $id => $name) {
            $ret['heights'][] = compact('id', 'name');
        }

        return $this->responseJsonSuccess($ret);
    }

    public function seeder()
    {
        exit;

        $seed = config('seeder');

        foreach ($seed as $entity => $record) {

            $Class = 'App\Models\\' . studly_case(str_singular($entity));

            if ($Class::all()->count() != 0) {
                continue;
            }

            foreach ($record as $id => $name) {


                $obj = new $Class();
                $obj->name = $name;
                $obj->save();
            }
        }
    }


}
