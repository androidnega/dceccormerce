<?php

namespace App\Support;

final class GhanaPhone
{
    /**
     * Normalize a Ghana mobile number to E.164: +233XXXXXXXXX
     */
    public static function toE164(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $s = preg_replace('/[^\d+]/', '', trim($raw)) ?? '';
        if ($s === '') {
            return null;
        }

        $digits = str_starts_with($s, '+') ? substr($s, 1) : $s;

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+233'.substr($digits, 1);
        }

        if (strlen($digits) === 9 && preg_match('/^[2-5]\d{8}$/', $digits) === 1) {
            return '+233'.$digits;
        }

        return null;
    }

    /**
     * Local display like 0241234567 (best-effort).
     */
    public static function toLocalDisplay(?string $raw): string
    {
        $e164 = self::toE164($raw);
        if ($e164 === null) {
            return trim((string) $raw);
        }

        return '0'.substr($e164, 4);
    }

    /**
     * Short privacy mask: 024XXXX567
     */
    public static function maskLocal(?string $raw): string
    {
        $local = self::toLocalDisplay($raw);
        $len = strlen($local);
        if ($len < 8) {
            return $local;
        }

        return substr($local, 0, 3).str_repeat('X', max(3, $len - 6)).substr($local, -3);
    }
}
