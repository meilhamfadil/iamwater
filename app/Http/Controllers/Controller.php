<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function responseJson(
        $data = null,
        $message = 'Success',
        $code = 200,
        $httpCode = 200
    ) {
        return response(
            [
                'code' => $code,
                'message' => $message,
                'data' => $data
            ],
            $httpCode
        )->header('Content-type', 'application/json');
    }
}
