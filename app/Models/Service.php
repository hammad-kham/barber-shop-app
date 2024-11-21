<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'location_type', 'shop_name', 'country', 'city', 'building', 'zipcode',
        'time_open_close', 'book_before', 'phone_no','bio',
    ];

    public function subServices()
    {
        return $this->hasMany(SubService::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function services_images()
    {
        return $this->hasMany(ServicesImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(RatingService::class);
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
