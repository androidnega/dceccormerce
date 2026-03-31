<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function index(): View
    {
        $riders = Rider::query()
            ->with(['user'])
            ->latest()
            ->paginate(20);

        return view('admin.riders.index', compact('riders'));
    }

    public function create(): View
    {
        return view('admin.riders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:50'],
            'vehicle_type' => ['required', 'string', 'in:bike,car'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'rider',
            ]);

            Rider::query()->create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'vehicle_type' => $validated['vehicle_type'],
                'is_available' => (bool) ($validated['is_available'] ?? false),
            ]);
        });

        return redirect()->route('dashboard.riders.index')->with('status', 'Rider account created. They can sign in with email and password.');
    }
}
