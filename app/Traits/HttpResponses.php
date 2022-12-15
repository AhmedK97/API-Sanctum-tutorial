<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success(array $data, string $message = null, $code = 200)
    {
        return response()->json(
            [
                'status' => 'Request was successful',
                'message' => $message,
                'data' => $data,
            ],
            $code
        );
    }


    protected function error($data, string $message = null, int $code)
    {
        return response()->json(
            [
                'status' => 'Error has occurred...',
                'message' => $message,
                'data' => $data,
            ],
            $code
        );
    }
}
