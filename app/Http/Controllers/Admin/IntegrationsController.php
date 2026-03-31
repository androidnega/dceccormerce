<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IntegrationsController extends Controller
{
    public function edit(): View
    {
        $hasSecret = SiteSetting::query()
            ->where('key', 'paystack_secret_key')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

        return view('admin.integrations.edit', [
            'paystack_public_key' => (string) (SiteSetting::get('paystack_public_key') ?: env('PAYSTACK_PUBLIC_KEY', '')),
            'paystack_enabled' => paystack_enabled_from_settings(),
            'has_paystack_secret' => $hasSecret,
            'sms_notifications_enabled' => sms_notifications_enabled(),
            'whatsapp_notifications_enabled' => whatsapp_notifications_enabled(),
            'sms_sender_env' => (string) config('sms.sender'),
            'sms_provider_env' => (string) config('sms.provider'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paystack_public_key' => ['nullable', 'string', 'max:255'],
            'paystack_secret_key' => ['nullable', 'string', 'max:500'],
            'paystack_enabled' => ['nullable', 'boolean'],
            'sms_notifications_enabled' => ['nullable', 'boolean'],
            'whatsapp_notifications_enabled' => ['nullable', 'boolean'],
        ]);

        SiteSetting::set('paystack_public_key', $validated['paystack_public_key'] ?? '');

        if (! empty($validated['paystack_secret_key'] ?? '')) {
            SiteSetting::set('paystack_secret_key', $validated['paystack_secret_key']);
        }

        SiteSetting::set('paystack_enabled', $request->boolean('paystack_enabled') ? '1' : '0');

        SiteSetting::set('sms_notifications_enabled', $request->boolean('sms_notifications_enabled') ? '1' : '0');
        SiteSetting::set('whatsapp_notifications_enabled', $request->boolean('whatsapp_notifications_enabled') ? '1' : '0');

        return redirect()
            ->route('dashboard.integrations.edit')
            ->with('status', 'Integration settings saved.');
    }
}
