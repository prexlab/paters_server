<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Contracts\Validation\Validator;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->getMessageBag()->toArray();
        $apiController = new ApiController();
        throw new ValidationException($validator, $apiController->responseJsonFailed($errors));
    }
}