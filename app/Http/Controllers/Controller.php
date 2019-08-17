<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SUCCESS_STATUS = 'success';
    const ERROR_STATUS = 'fail';

    public function apiResponse($data = [], $message = null, $code = self::SUCCESS_STATUS, $headers = [])
    {
        if (empty($headers)) {
            $headers = [
                'Content-Type' => 'application/json'
            ];
        }
        $items = array_key_exists('items', $data) ? $data['items'] : $data;
        $paging = array_key_exists('paging', $data) ? $data['paging'] : [];

        $responseData = [
            'code' => $code,
            'message' => $message,
            'data' => $items
        ];

        if (!empty($paging)) {
            $responseData = array_merge($responseData, $paging);
        }
        return response()->json($responseData, Response::HTTP_OK, $headers);
    }
}
