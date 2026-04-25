<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reference',
    'cart_payload',
    'validated_payload',
    'expected_amount_pesewas',
    'user_id',
    'processed_at',
    'order_id',
])]
class PaystackPendingCheckout extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected function casts(): array
    {
        return [
            'cart_payload' => 'array',
            'validated_payload' => 'array',
            'expected_amount_pesewas' => 'integer',
            'processed_at' => 'datetime',
        ];
    }
}
