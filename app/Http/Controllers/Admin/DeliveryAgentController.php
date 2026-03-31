<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryAgentController extends Controller
{
    public function index(): View
    {
        $agents = DeliveryAgent::query()
            ->with('rider')
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.delivery-agents.index', compact('agents'));
    }

    public function create(): View
    {
        $riders = Rider::query()->orderBy('name')->get();

        return view('admin.delivery-agents.create', compact('riders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rider,driver,third_party,pickup,manual'],
            'phone' => ['nullable', 'string', 'max:50'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:available,busy,offline'],
            'rider_id' => ['nullable', 'integer', 'exists:riders,id'],
        ]);

        DeliveryAgent::query()->create($validated);

        return redirect()->route('dashboard.delivery-agents.index')->with('status', 'Delivery agent created.');
    }

    public function edit(DeliveryAgent $delivery_agent): View
    {
        $riders = Rider::query()->orderBy('name')->get();

        return view('admin.delivery-agents.edit', ['agent' => $delivery_agent, 'riders' => $riders]);
    }

    public function update(Request $request, DeliveryAgent $delivery_agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:rider,driver,third_party,pickup,manual'],
            'phone' => ['nullable', 'string', 'max:50'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:available,busy,offline'],
            'rider_id' => ['nullable', 'integer', 'exists:riders,id'],
        ]);

        $delivery_agent->update($validated);

        return redirect()->route('dashboard.delivery-agents.index')->with('status', 'Delivery agent updated.');
    }

    public function destroy(DeliveryAgent $delivery_agent): RedirectResponse
    {
        if (Order::query()->where('delivery_agent_id', $delivery_agent->id)->exists()) {
            return redirect()->route('dashboard.delivery-agents.index')->withErrors(['status' => 'Cannot delete an agent that is linked to orders. Reassign orders first.']);
        }

        $delivery_agent->delete();

        return redirect()->route('dashboard.delivery-agents.index')->with('status', 'Delivery agent deleted.');
    }
}
