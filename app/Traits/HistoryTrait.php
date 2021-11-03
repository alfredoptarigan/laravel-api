<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\History;

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

    public function successHistoryUser($msg)
    {
        return "User dengan nama " . $this->auth() . " berhasil " . $msg;
    }

    public function postHistoryAdmin(Request $request,  $msg, $id)
    {
        return History::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'description' => $this->successHistory($msg . $id)
        ]);
    }

    public function postHistoryUser($request,  $msg, $id)
    {
        return History::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'description' => $this->successHistoryUser($msg . $id)
        ]);
    }
}
