<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adress extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        "province_id",
        "city_id",
        "complete_address",
        'user_id'
    ];

    protected $hidden = [
        'user_id'
    ];
}
