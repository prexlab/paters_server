<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

class ApiController extends Controller
{
    /**
     * 正常Jsonレスポンス
     *
     * @access public
     * @param mixed $body
     * @param int $code
     * @return Response
     */
    public function responseJsonSuccess($body, int $code = 200)
    {
        return $this->__responseJson($this->__responseSuccess($body, $code), $code);
    }

    /**
     * 例外Jsonレスポンス
     *
     * @access public
     * @param mixed $body
     * @param int $code
     * @return Response
     */
    public function responseJsonFailed($body, int $code = 400)
    {
        return $this->__responseJson($this->__responseFailed($body, $code), $code);
    }

    /**
     * 正常JsonレスポンスBody生成
     *
     * @access private
     * @param mixed $body
     * @param int $code
     * @return array
     */
    private function __responseSuccess($body, int $code = 200): array
    {
        return [
            'result' => true,
            'code' => $code,
            'body' => $body,
        ];
    }

    /**
     * 例外JsonレスポンスBody生成
     *
     * @access private
     * @param mixed $body
     * @param int $code
     * @return array
     */
    private function __responseFailed($body, int $code = 400): array
    {
        return [
            'result' => false,
            'code' => $code,
            'body' => $body,
        ];
    }

    /**
     * Jsonレスポンス
     *
     * @access private
     * @param array $body
     * @param int $code
     * @return Response
     */
    private function __responseJson(array $body, int $code)
    {
        return Response::json($body, $code);
    }
}
