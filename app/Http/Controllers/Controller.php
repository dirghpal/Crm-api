<?php

namespace App\Http\Controllers;

abstract class Controller
{
   protected $response = [
        'msg' => '',
        'data' => null,
    ];

    protected function handleApiRequest(callable $callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            $this->response['msg'] = $e->getMessage();
            return response()->json($this->response, 400);
        }
    }
}
