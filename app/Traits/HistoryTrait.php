<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HistoryTrait
{
    private function auth()
    {
        return Auth::user()->name;
    }
    public function successHistory($msg)
    {
        return "Admin dengan nama " . $this->auth() . " berhasil " . $msg;
    }
}
