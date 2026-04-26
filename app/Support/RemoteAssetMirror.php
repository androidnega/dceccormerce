<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class RemoteAssetMirror
{
    /**
     * Download a remote asset into storage/app/public/{directory}/ and return the relative path
     * (e.g. products/abc.jpg) for use in the database.
     */
    public static function mirrorToPublicDisk(string $url, string $directory, ?string $basenameWithoutExt = null): ?string
    {
        $url = trim($url);
        if ($url === '' || (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://'))) {
            return null;
        }

        try {
            $response = Http::timeout(90)
                ->connectTimeout(20)
                ->withHeaders([
                    'User-Agent' => 'DCapple/1.0 (catalog mirror; +https://dcapple.com)',
                ])
                ->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $body = $response->body();
        if ($body === '') {
            return null;
        }

        $ext = self::guessExtension($url, $response->header('Content-Type'));
        $base = $basenameWithoutExt ?? Str::uuid()->toString();
        $base = str_replace(['..', "\0"], '', $base);
        $base = (string) preg_replace('/[^a-zA-Z0-9_-]+/', '-', $base);
        $base = trim($base, '-');
        if ($base === '') {
            $base = Str::uuid()->toString();
        }

        $filename = $base.'.'.$ext;
        $relative = trim($directory, '/').'/'.$filename;
        Storage::disk('public')->put($relative, $body);

        return $relative;
    }

    /**
     * Copy a file from public/ into storage/app/public/ (relative dest path).
     */
    public static function copyPublicAssetToPublicDisk(string $publicRelativePath, string $destRelativePath): ?string
    {
        $publicRelativePath = str_replace('\\', '/', ltrim($publicRelativePath, '/'));
        $abs = public_path($publicRelativePath);
        if (! is_file($abs)) {
            return null;
        }

        $destRelativePath = str_replace('\\', '/', ltrim($destRelativePath, '/'));
        Storage::disk('public')->put($destRelativePath, File::get($abs));

        return $destRelativePath;
    }

    private static function guessExtension(string $url, ?string $contentType): string
    {
        $path = (string) parse_url($url, PHP_URL_PATH);
        $fromPath = $path !== '' ? strtolower((string) pathinfo($path, PATHINFO_EXTENSION)) : '';
        if (in_array($fromPath, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true)) {
            return $fromPath === 'jpeg' ? 'jpg' : $fromPath;
        }

        $ct = strtolower((string) $contentType);

        return match (true) {
            str_contains($ct, 'svg') => 'svg',
            str_contains($ct, 'png') => 'png',
            str_contains($ct, 'gif') => 'gif',
            str_contains($ct, 'webp') => 'webp',
            default => 'jpg',
        };
    }
}
