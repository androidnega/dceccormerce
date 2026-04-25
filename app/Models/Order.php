<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'user_id',
    'total_amount',
    'promo_discount_amount',
    'status',
    'payment_status',
    'order_number',
    'delivery_status',
    'delivery_method',
    'delivery_zone',
    'delivery_price',
    'delivery_agent_id',
    'payment_method',
    'notes',
    'rider_id',
    'failed_previous_status',
    'delivery_option',
    'paystack_reference',
    'refund_status',
    'paystack_refund_id',
    'refunded_at',
])]
class Order extends Model
{
    /** @var list<string> */
    public const STATUSES = ['pending', 'confirmed', 'prepared', 'assigned', 'on_the_way', 'delivered', 'failed'];

    /** @var list<string> */
    public const DELIVERY_STATUSES = ['pending', 'confirmed', 'prepared', 'assigned', 'on_the_way', 'delivered', 'failed'];

    /** @var list<string> */
    public const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];

    /** @var list<string> */
    public const REFUND_STATUSES = ['none', 'processing', 'completed', 'failed'];

    /** @var list<string> */
    public const PAYMENT_METHODS = ['cod', 'momo'];

    /**
     * @return array<string, list<string>>
     */
    public static function allowedTransitions(): array
    {
        return [
            'pending' => ['confirmed'],
            'confirmed' => ['assigned', 'failed'],
            'assigned' => ['on_the_way', 'failed'],
            'on_the_way' => ['delivered', 'failed'],
            'delivered' => [],
            'failed' => [],
        ];
    }

    public function canTransitionTo(string $target): bool
    {
        $method = (string) ($this->delivery_method ?? '');
        if ($method === '' || $method === 'rider') {
            // Back-compat for existing orders: infer method from the legacy delivery_option.
            $opt = (string) ($this->delivery_option ?? '');
            $method = $opt === 'pickup' ? 'pickup' : 'rider';
        }

        $deliveryStatus = (string) ($this->delivery_status ?? 'pending');

        if (in_array($method, ['pickup', 'manual'], true)) {
            $map = [
                'pending' => ['confirmed'],
                'confirmed' => ['prepared'],
                'prepared' => ['delivered', 'failed'],
                'assigned' => [],
                'on_the_way' => [],
                'delivered' => [],
                'failed' => [],
            ];

            return in_array($target, $map[$deliveryStatus] ?? [], true);
        }

        $map = [
            'pending' => ['confirmed'],
            'confirmed' => ['prepared'],
            'prepared' => ['assigned', 'failed'],
            'assigned' => ['on_the_way', 'failed'],
            'on_the_way' => ['delivered', 'failed'],
            'delivered' => [],
            'failed' => [],
        ];

        return in_array($target, $map[$deliveryStatus] ?? [], true);
    }

    public static function generateNextOrderNumber(?int $year = null): string
    {
        $year = $year ?? (int) now()->format('Y');
        $prefix = 'DCA-'.$year.'-';

        $lastForYear = DB::table('orders')
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('order_number');

        $lastSequence = 0;
        if (is_string($lastForYear) && str_starts_with($lastForYear, $prefix)) {
            $lastSequence = (int) substr($lastForYear, -4);
        }

        return sprintf('%s%04d', $prefix, $lastSequence + 1);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->order_number) {
                $order->order_number = self::generateNextOrderNumber();
            }

            if (! $order->delivery_status) {
                $order->delivery_status = 'pending';
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasOne<OrderAddress, $this>
     */
    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    /**
     * @return BelongsTo<Rider, $this>
     */
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    /**
     * @return BelongsTo<DeliveryAgent, $this>
     */
    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'promo_discount_amount' => 'decimal:2',
            'delivery_price' => 'decimal:2',
            'refunded_at' => 'datetime',
        ];
    }
}
