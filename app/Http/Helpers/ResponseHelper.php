<?php

namespace App\Http\Helpers;

class ResponseHelper
{
    public function not_found() {
        return response()->json(['message' => 'not found'], 404);
    }

    public function bad_request($message = 'bad request') {
        return response()->json(['message' => $message], 400);
    }

    public function success($data = null) {
        return response()->json([
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function created($data = null) {
        return response()->json([
            'message' => 'created',
            'data' => $data
        ], 201);
    }
}