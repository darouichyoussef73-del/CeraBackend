<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'category',
        'stock',
        'status',
        // store only the relative image path (e.g. products/abcd.jpg)
        'image',
    ];
    
}
