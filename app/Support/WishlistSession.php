<?php

namespace App\Support;

class WishlistSession
{
    private const SESSION_KEY = 'wishlist';

    /**
     * @return list<int>
     */
    public static function ids(): array
    {
        $raw = session()->get(self::SESSION_KEY, []);

        if (! is_array($raw)) {
            return [];
        }

        return array_values(array_unique(array_map(static fn ($id) => (int) $id, $raw)));
    }

    public static function has(int $productId): bool
    {
        return in_array($productId, self::ids(), true);
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    /**
     * Toggle product in wishlist. Returns true if now in wishlist, false if removed.
     */
    public static function toggle(int $productId): bool
    {
        $ids = self::ids();
        $key = array_search($productId, $ids, true);
        if ($key !== false) {
            unset($ids[$key]);
            $ids = array_values($ids);
            session()->put(self::SESSION_KEY, $ids);

            return false;
        }
        $ids[] = $productId;
        session()->put(self::SESSION_KEY, $ids);

        return true;
    }
}
