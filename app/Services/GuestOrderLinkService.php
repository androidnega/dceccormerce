<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class GuestOrderLinkService
{
    public function linkOrdersForUser(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            return;
        }

        $email = trim(strtolower((string) $user->email));
        if ($email === '') {
            return;
        }

        Order::query()
            ->whereNull('user_id')
            ->whereNotNull('customer_email')
            ->whereRaw('LOWER(TRIM(customer_email)) = ?', [$email])
            ->update(['user_id' => $user->id]);
    }
}
