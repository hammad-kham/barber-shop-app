<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGallery extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'image'];

    public function getImageAttribute($value)
    {
        if ($value == null) {
            return null;
        } else {
            return asset('/assets/images/user/' . $value);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
