<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\WishlistSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Request $request, int $id): JsonResponse
    {
        $product = Product::query()->active()->find($id);
        if ($product === null) {
            return response()->json(['ok' => false, 'message' => 'Product not found.'], 404);
        }

        $inWishlist = WishlistSession::toggle($id);

        return response()->json([
            'ok' => true,
            'inWishlist' => $inWishlist,
            'count' => WishlistSession::count(),
        ]);
    }
}
