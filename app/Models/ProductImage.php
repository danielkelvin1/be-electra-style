<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'image_url',
        'product_id'
    ];

    protected $hidden = [
        'product_id'
    ];
}
