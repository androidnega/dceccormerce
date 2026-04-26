<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRule extends Model
{
    protected $fillable = [
        'zone',
        'method',
        'option',
        'price',
        'estimated_time',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }
}
