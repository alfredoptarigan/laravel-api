<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class History extends Model
{
    use HasFactory;

    protected $table = 'histories';

    protected $fillable = [
        'user_id',
        'ip_address',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
