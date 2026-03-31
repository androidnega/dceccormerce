<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeliveryRuleController extends Controller
{
    public function index(): View
    {
        $rules = DeliveryRule::query()
            ->orderBy('zone')
            ->orderBy('method')
            ->orderBy('option')
            ->paginate(25);

        return view('admin.delivery-rules.index', compact('rules'));
    }

    public function create(): View
    {
        return view('admin.delivery-rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zone' => [
                'required',
                'string',
                'max:64',
                Rule::unique('delivery_rules', 'zone')->where(function ($query) use ($request): void {
                    $query->where('method', (string) $request->input('method'))
                        ->where('option', (string) $request->input('option'));
                }),
            ],
            'method' => ['required', 'string', 'in:rider,driver,third_party,pickup,manual'],
            'option' => ['required', 'string', 'in:standard,express,pickup'],
            'price' => ['required', 'numeric', 'min:0'],
            'estimated_time' => ['nullable', 'string', 'max:64'],
            'active' => ['nullable', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        DeliveryRule::query()->create($validated);

        return redirect()->route('dashboard.delivery-rules.index')->with('status', 'Delivery rule created.');
    }

    public function edit(DeliveryRule $delivery_rule): View
    {
        return view('admin.delivery-rules.edit', ['rule' => $delivery_rule]);
    }

    public function update(Request $request, DeliveryRule $delivery_rule): RedirectResponse
    {
        $validated = $request->validate([
            'zone' => [
                'required',
                'string',
                'max:64',
                Rule::unique('delivery_rules', 'zone')
                    ->where(function ($query) use ($request): void {
                        $query->where('method', (string) $request->input('method'))
                            ->where('option', (string) $request->input('option'));
                    })
                    ->ignore($delivery_rule->id),
            ],
            'method' => ['required', 'string', 'in:rider,driver,third_party,pickup,manual'],
            'option' => ['required', 'string', 'in:standard,express,pickup'],
            'price' => ['required', 'numeric', 'min:0'],
            'estimated_time' => ['nullable', 'string', 'max:64'],
            'active' => ['nullable', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        $delivery_rule->update($validated);

        return redirect()->route('dashboard.delivery-rules.index')->with('status', 'Delivery rule updated.');
    }

    public function destroy(DeliveryRule $delivery_rule): RedirectResponse
    {
        $delivery_rule->delete();

        return redirect()->route('dashboard.delivery-rules.index')->with('status', 'Delivery rule deleted.');
    }
}
