<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'sub_service_id',
        'status'
    ];


    //Relationship with the Appointment model.
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }


     //Relationship with the SubService model.
    public function subService()
    {
        return $this->belongsTo(SubService::class);
    }
}
