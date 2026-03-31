<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'full_name', 'phone', 'recipient_name', 'recipient_phone', 'address', 'city', 'country'])]
class OrderAddress extends Model
{
    /**
     * Person receiving the shipment (when different from the order contact).
     * Null means same as {@see $full_name} / {@see $phone}.
     */
    public function deliveryRecipientName(): string
    {
        $r = trim((string) ($this->recipient_name ?? ''));

        return $r !== '' ? $r : (string) $this->full_name;
    }

    /**
     * Phone for the person at delivery (falls back to order contact phone).
     */
    public function deliveryRecipientPhone(): string
    {
        $r = trim((string) ($this->recipient_phone ?? ''));

        return $r !== '' ? $r : (string) $this->phone;
    }

    public function recipientDiffersFromContact(): bool
    {
        $rn = trim((string) ($this->recipient_name ?? ''));
        $rp = trim((string) ($this->recipient_phone ?? ''));

        return $rn !== '' || $rp !== '';
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
