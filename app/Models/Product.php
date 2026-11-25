<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'farmer_id',
        'name',
        'slug',
        'unit',
        'base_price',
        'selling_price',
        'daily_quota',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}