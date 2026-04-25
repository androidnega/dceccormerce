<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function refundPolicy(): View
    {
        return view('legal.refund-policy', [
            'storeEmail' => (string) config('store.email'),
        ]);
    }
}
