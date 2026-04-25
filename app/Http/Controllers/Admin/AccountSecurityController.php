<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountSecurityController extends Controller
{
    public function edit(): View
    {
        return view('admin.security.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->password = $validated['password'];
        $request->user()->save();

        return redirect()
            ->route('dashboard.security.edit')
            ->with('status', 'Your password has been updated.');
    }
}
