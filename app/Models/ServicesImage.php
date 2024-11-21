<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesImage extends Model
{
    use HasFactory;


    protected $table = 'services_images';

    protected $fillable = [
        'user_id','service_id', 'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function getImageAttribute($value)
    {
        if ($value == null) {
            return null;
        } else {
            return asset('/assets/images/services/' . $value);
        }
    }
}
