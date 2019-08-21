<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class BaseService
 * @package App\Services
 *
 * Definition of common method for service classes
 *
 */
class BaseService
{
    const SUCCESS_STATUS = 'success';
    const ERROR_STATUS = 'fail';

    public function apiResponse($data = [], $message = null, $status = self::SUCCESS_STATUS, $code = Response::HTTP_OK,  $headers = [])
    {
        if (empty($headers)) {
            $headers = [
                'Content-Type' => 'application/json'
            ];
        }
        $responseData = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($responseData, $code, $headers);
    }

    /**
     * @param $validatorErrors
     * @return array
     */
    public function responseMessage($validatorErrors)
    {
        $messages = [];
        foreach ($validatorErrors as $key => $message) {
            $messages[$key] = $message[0];
        }
        return $messages;
    }
}
