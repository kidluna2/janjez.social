<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price',
        'min_quantity', 'max_quantity', 'provider_service_id',
        'provider_name', 'status', 'order_count'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'order_count' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'KES ' . number_format($this->price, 2);
    }
}
