<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerController extends Controller
{
    public function dashboard(): View
    {
        return view('customer.dashboard');
    }
}
