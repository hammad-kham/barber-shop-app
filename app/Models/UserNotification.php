<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'send_to',
        'message',
        'title',
        'type',
        'redirect',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
