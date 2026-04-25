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
        $hasPaystackSecret = SiteSetting::query()
            ->where('key', 'paystack_secret_key')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

        $hasSmsApiKey = SiteSetting::query()
            ->where('key', 'sms_api_key')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

        $smsSenderStored = (string) (SiteSetting::get('sms_sender') ?? '');
        $arkeselUrlStored = (string) (SiteSetting::get('sms_arkesel_url') ?? '');

        return view('admin.integrations.edit', [
            'paystack_public_key' => (string) (SiteSetting::get('paystack_public_key') ?: env('PAYSTACK_PUBLIC_KEY', '')),
            'paystack_enabled' => paystack_enabled_from_settings(),
            'has_paystack_secret' => $hasPaystackSecret,
            'sms_provider' => sms_provider_resolved(),
            'sms_sender' => $smsSenderStored,
            'sms_arkesel_url' => $arkeselUrlStored,
            'has_sms_api_key' => $hasSmsApiKey,
            'sms_notifications_enabled' => sms_notifications_enabled(),
            'whatsapp_notifications_enabled' => whatsapp_notifications_enabled(),
            'sms_sender_effective' => sms_sender_resolved(),
            'sms_arkesel_url_effective' => sms_arkesel_url_resolved(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'paystack_public_key' => ['nullable', 'string', 'max:255'],
            'paystack_secret_key' => ['nullable', 'string', 'max:500'],
            'paystack_enabled' => ['nullable', 'boolean'],
            'sms_provider' => ['required', 'in:log,arkesel,hubtel'],
            'sms_api_key' => ['nullable', 'string', 'max:500'],
            'sms_sender' => ['nullable', 'string', 'max:20'],
            'sms_arkesel_url' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || $value === '') {
                        return;
                    }
                    if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                        $fail('The Arkesel API URL must be a valid URL.');
                    }
                },
            ],
            'sms_notifications_enabled' => ['nullable', 'boolean'],
            'whatsapp_notifications_enabled' => ['nullable', 'boolean'],
        ]);

        SiteSetting::set('paystack_public_key', $validated['paystack_public_key'] ?? '');

        if (! empty($validated['paystack_secret_key'] ?? '')) {
            SiteSetting::set('paystack_secret_key', $validated['paystack_secret_key']);
        }

        SiteSetting::set('paystack_enabled', $request->boolean('paystack_enabled') ? '1' : '0');

        SiteSetting::set('sms_provider', $validated['sms_provider'] ?? 'log');
        if (! empty($validated['sms_api_key'] ?? '')) {
            SiteSetting::set('sms_api_key', $validated['sms_api_key']);
        }
        $sender = trim($validated['sms_sender'] ?? '');
        SiteSetting::set('sms_sender', $sender);

        $arkeselUrl = trim($validated['sms_arkesel_url'] ?? '');
        if ($arkeselUrl === '') {
            SiteSetting::set('sms_arkesel_url', null);
        } else {
            SiteSetting::set('sms_arkesel_url', $arkeselUrl);
        }

        SiteSetting::set('sms_notifications_enabled', $request->boolean('sms_notifications_enabled') ? '1' : '0');
        SiteSetting::set('whatsapp_notifications_enabled', $request->boolean('whatsapp_notifications_enabled') ? '1' : '0');

        return redirect()
            ->route('dashboard.integrations.edit')
            ->with('status', 'Integration settings saved.');
    }
}
