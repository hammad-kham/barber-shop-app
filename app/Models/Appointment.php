<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'amount',
        'date',
        'time',
        'status',
        'reason',
        'note',
    ];


    //Relationship with the User model.
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    //Relationship with the Service model.
    public function service()
    {
        return $this->belongsTo(Service::class);
    }


    //Relationship with AppointmentDetail.
    public function AppointmentDetails()
    {
        return $this->hasMany(AppointmentDetail::class);
    }
}
