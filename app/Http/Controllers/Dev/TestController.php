<?php

namespace App\Http\Controllers\Dev;

use App\Entities\FactoryOrderData;
use App\Entities\RepairItemCategory;
use App\Services\OrderDetailIdOnCsv;
use App\Http\Controllers\Controller;
use App\UseCases\FactoryOrder\GenerateFactoryOrderShirtCsvMakeRemake;
use App\UseCases\FactoryOrder\GenerateFactoryOrderShirtQualityCsvMake;
use App\UseCases\FactoryOrder\GenerateFactoryOrderShirtCsvMakeRemakeChoya;
use App\UseCases\FactoryOrder\GenerateFactoryOrderShirtCsvMakeRemakeOritec;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{

    private function upload(){

        return '
<form method="post" action="/api/upload" enctype="multipart/form-data">        
<input type="file" name="image"><input type="submit" value="submit">
</form>
        
';

    }

    function main($method)
    {

        echo $this->$method();
    }

}