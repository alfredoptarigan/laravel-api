<?php

namespace App\Traits;

trait APITraits
{

    public function response($msg, $data)
    {
        $response = [
            'msg' => $msg,
            'data' => $data,
        ];

        return $response;
    }
}
