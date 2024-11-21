<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingService extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'service_id',
        'appointment_id',
        'rating',
        'comment'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Relationship with Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
