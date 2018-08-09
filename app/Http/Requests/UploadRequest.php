<?php

namespace App\Http\Requests;

class UploadRequest extends ApiRequest{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => [
                'required',// 必須
                'file',// アップロードされたファイルであること
                'image',// 画像ファイルであること
                'mimes:jpeg,png',// MIMEタイプを指定
                'dimensions:min_width=120,min_height=120,max_width=10000,max_height=10000',
            ],
        ];
    }

    public function messages(){

        return [
          'dimensions' => '不正なサイズです。120x120以上 10000x10000以内にしてください',
        ];
    }
}
