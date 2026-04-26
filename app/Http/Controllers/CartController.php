<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\CartSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return Collection<int, Product>
     */
    private function recommendedForCart(array $lines)
    {
        $ids = array_map('intval', array_keys($lines));
        $query = Product::query()->active()->with(['category', 'images']);

        if ($ids !== []) {
            $query->whereNotIn('id', $ids);
        }

        return $query->inRandomOrder()->limit(4)->get();
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: float}
     */
    private function linesAndTotal(): array
    {
        $lines = [];
        $total = 0.0;
        $cleaned = CartSession::reconcile();

        foreach ($cleaned as $productId => $line) {
            $price = (float) $line['price'];
            $qty = (int) $line['quantity'];
            $subtotal = $price * $qty;
            $lines[$productId] = array_merge($line, [
                'subtotal' => $subtotal,
            ]);
            $total += $subtotal;
        }

        return [$lines, $total];
    }

    private function cartJsonPayload(): array
    {
        [$lines, $total] = $this->linesAndTotal();
        $cartCount = collect($lines)->sum(fn ($line) => (int) ($line['quantity'] ?? 0));
        $recommendedProducts = $this->recommendedForCart($lines);

        return [
            'cartCount' => $cartCount,
            'cartTotal' => $total,
            'cartTotalFormatted' => format_ghs($total),
            'drawerHtml' => view('cart.partials.drawer-body', [
                'lines' => $lines,
                'total' => $total,
                'recommendedProducts' => $recommendedProducts,
            ])->render(),
        ];
    }

    public function index(): View
    {
        [$lines, $total] = $this->linesAndTotal();

        return view('cart.index', [
            'lines' => $lines,
            'total' => $total,
            'recommendedProducts' => $this->recommendedForCart($lines),
        ]);
    }

    public function drawer(Request $request): View|JsonResponse
    {
        [$lines, $total] = $this->linesAndTotal();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                ...$this->cartJsonPayload(),
            ]);
        }

        return view('cart.partials.drawer-body', [
            'lines' => $lines,
            'total' => $total,
            'recommendedProducts' => $this->recommendedForCart($lines),
        ]);
    }

    public function add(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'redirect' => ['nullable', 'string', 'in:cart,checkout'],
        ]);

        $redirectTo = $validated['redirect'] ?? 'cart';

        if ($redirectTo === 'checkout' && ! $request->boolean('terms')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Please agree to the terms and conditions before continuing.',
                ], 422);
            }

            return back()->withErrors(['cart' => 'Please agree to the terms and conditions before continuing.']);
        }

        $product = Product::query()->with('images')->find($id);

        if (! $product) {
            abort(404);
        }

        if (! $product->is_active) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'This product is not available.',
                ], 422);
            }

            return back()->withErrors(['cart' => 'This product is not available.']);
        }

        if ($product->stock < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'This product is out of stock.',
                ], 422);
            }

            return back()->withErrors(['cart' => 'This product is out of stock.']);
        }

        $addQty = max(1, (int) ($validated['quantity'] ?? 1));

        $cart = session()->get('cart', []);
        $currentQty = isset($cart[$id]) ? (int) $cart[$id]['quantity'] : 0;

        $maxCanAdd = $product->stock - $currentQty;

        if ($maxCanAdd < 1) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'You cannot add more than available stock.',
                ], 422);
            }

            return back()->withErrors(['cart' => 'You cannot add more than available stock.']);
        }

        $addQty = min($addQty, $maxCanAdd);
        $newQty = $currentQty + $addQty;

        $cart[$id] = CartSession::lineItemFromProduct($product, $newQty);
        session()->put('cart', $cart);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Added to cart.',
                ...$this->cartJsonPayload(),
            ]);
        }

        if ($redirectTo === 'checkout') {
            return redirect()->route('checkout.index')->with('status', 'Added to cart. Continue to complete your order.');
        }

        return redirect()->route('cart.index')->with('status', 'Added to cart.');
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => 'Item not in cart.'], 422);
            }

            return redirect()->route('cart.index')->withErrors(['cart' => 'That item is not in your cart.']);
        }

        $product = Product::query()->with('images')->find($id);

        if (! $product) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Removed.',
                    ...$this->cartJsonPayload(),
                ]);
            }

            return redirect()->route('cart.index')->withErrors(['cart' => 'Product was removed from your cart because it no longer exists.']);
        }

        if (! $product->is_active) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Removed.',
                    ...$this->cartJsonPayload(),
                ]);
            }

            return redirect()->route('cart.index')->withErrors(['cart' => 'This product is no longer available and was removed from your cart.']);
        }

        $qty = min(max(1, $validated['quantity']), $product->stock);

        $cart[$id] = CartSession::lineItemFromProduct($product, $qty);
        session()->put('cart', $cart);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Updated.',
                ...$this->cartJsonPayload(),
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Cart updated.');
    }

    public function remove(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Removed.',
                ...$this->cartJsonPayload(),
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('cart.index')->with('status', 'Cart cleared.');
    }
}
