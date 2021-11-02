<?php

namespace App\Utils;

class APIResponse
{
    public static function SuccessResponse($msg, $code = 200)
    {
        return response()->json($msg, $code);
    }

    public static function ErrorResponse($msg, $code = 400)
    {
        return response()->json($msg, $code);
    }
}
