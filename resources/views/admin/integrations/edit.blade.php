@extends('layouts.dashboard')

@section('title', 'Integrations — '.config('app.name'))
@section('heading', 'Integrations')
@section('subheading', 'Connect payment and SMS APIs. Keys in the form override .env when saved.')

@section('content')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7">
            <form action="{{ route('dashboard.integrations.update') }}" method="post" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 bg-gradient-to-r from-zinc-50 to-white px-5 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Payment API</p>
                        <h2 class="mt-1 text-base font-semibold text-zinc-900">Paystack</h2>
                        <p class="mt-1 text-sm text-zinc-500">Public and secret keys from your <a href="https://dashboard.paystack.com" target="_blank" rel="noopener noreferrer" class="font-medium text-zinc-800 underline decoration-zinc-300 underline-offset-2 hover:text-zinc-600">Paystack dashboard</a>. Values here override <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">.env</code> when set.</p>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3">
                            <input type="hidden" name="paystack_enabled" value="0">
                            <input type="checkbox" name="paystack_enabled" value="1" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900" @checked($paystack_enabled)>
                            <span class="text-sm font-medium text-zinc-900">Enable Paystack checkout</span>
                        </label>

                        <div>
                            <label for="paystack_public_key" class="block text-sm font-medium text-zinc-700">Public key</label>
                            <input type="text" name="paystack_public_key" id="paystack_public_key" value="{{ old('paystack_public_key', $paystack_public_key) }}" autocomplete="off" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="pk_live_… or pk_test_…">
                        </div>

                        <div>
                            <label for="paystack_secret_key" class="block text-sm font-medium text-zinc-700">Secret key</label>
                            <input type="password" name="paystack_secret_key" id="paystack_secret_key" value="" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="{{ $has_paystack_secret ? 'Leave blank to keep current secret' : 'sk_live_… or sk_test_…' }}">
                            @if ($has_paystack_secret)
                                <p class="mt-1.5 text-xs text-zinc-500">A secret is saved. Enter a new value only if you want to replace it.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 bg-gradient-to-r from-amber-50/80 to-white px-5 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-amber-800/80">SMS API</p>
                        <h2 class="mt-1 text-base font-semibold text-zinc-900">Arkesel</h2>
                        <p class="mt-1 text-sm text-zinc-500">Arkesel is the default SMS gateway (Ghana). Configure the HTTP API key and sender ID here, or use <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">SMS_API_KEY</code> and <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">SMS_PROVIDER</code> in <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">.env</code>. Saved values take precedence over the file when present.</p>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        <div>
                            <label for="sms_provider" class="block text-sm font-medium text-zinc-700">SMS channel</label>
                            <select name="sms_provider" id="sms_provider" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                <option value="log" @selected(old('sms_provider', $sms_provider) === 'log')>Log only (no sends — development)</option>
                                <option value="arkesel" @selected(old('sms_provider', $sms_provider) === 'arkesel')>Arkesel (HTTP API)</option>
                                <option value="hubtel" @selected(old('sms_provider', $sms_provider) === 'hubtel')>Hubtel (use Hubtel client id/secret in .env)</option>
                            </select>
                        </div>

                        <div>
                            <label for="sms_api_key" class="block text-sm font-medium text-zinc-700">Arkesel API key</label>
                            <input type="password" name="sms_api_key" id="sms_api_key" value="" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="{{ $has_sms_api_key ? 'Leave blank to keep current key' : 'Paste your Arkesel API key' }}">
                            @if ($has_sms_api_key)
                                <p class="mt-1.5 text-xs text-zinc-500">A key is stored. Leave the field empty to keep it, or enter a new key to replace it.</p>
                            @else
                                <p class="mt-1.5 text-xs text-zinc-500">If this is empty, the app uses <code class="rounded bg-zinc-100 px-1 text-[0.7rem] text-zinc-700">SMS_API_KEY</code> from the environment when Arkesel is selected.</p>
                            @endif
                        </div>

                        <div>
                            <label for="sms_sender" class="block text-sm font-medium text-zinc-700">Sender ID (from)</label>
                            <input type="text" name="sms_sender" id="sms_sender" value="{{ old('sms_sender', $sms_sender) }}" maxlength="20" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="Registered name on your account (e.g. {{ $sms_sender_effective }})">
                            <p class="mt-1.5 text-xs text-zinc-500">Effective sender right now: <span class="font-mono text-zinc-800">{{ $sms_sender_effective }}</span>. Clear the field and save to fall back to the environment default.</p>
                        </div>

                        <div>
                            <label for="sms_arkesel_url" class="block text-sm font-medium text-zinc-700">Arkesel API URL (optional)</label>
                            <input type="url" name="sms_arkesel_url" id="sms_arkesel_url" value="{{ old('sms_arkesel_url', $sms_arkesel_url) }}" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="https://sms.arkesel.com/sms/api">
                            <p class="mt-1.5 text-xs text-zinc-500">Default in use: <span class="break-all font-mono text-zinc-800">{{ $sms_arkesel_url_effective }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 px-5 py-4">
                        <h2 class="text-base font-semibold text-zinc-900">Customer notifications</h2>
                        <p class="mt-1 text-sm text-zinc-500">Control whether order updates are sent (Ghana numbers as +233). Toggles are for cost and rollout control; WhatsApp uses Twilio if configured in <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">.env</code>.</p>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3">
                            <input type="hidden" name="sms_notifications_enabled" value="0">
                            <input type="checkbox" name="sms_notifications_enabled" value="1" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900" @checked($sms_notifications_enabled)>
                            <span class="text-sm font-medium text-zinc-900">Send SMS notifications</span>
                        </label>

                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3">
                            <input type="hidden" name="whatsapp_notifications_enabled" value="0">
                            <input type="checkbox" name="whatsapp_notifications_enabled" value="1" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900" @checked($whatsapp_notifications_enabled)>
                            <span class="text-sm font-medium text-zinc-900">Send WhatsApp notifications (Twilio)</span>
                        </label>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">Save integration settings</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="xl:col-span-5">
            <div class="grid gap-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-zinc-900">Status</h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">Paystack</dt>
                            <dd class="font-medium text-zinc-900">{{ $paystack_enabled ? 'Enabled' : 'Disabled' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">Paystack keys</dt>
                            <dd class="font-medium text-zinc-900">{{ paystack_ready() ? 'Ready' : 'Incomplete' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">SMS channel</dt>
                            <dd class="font-mono text-xs font-medium text-zinc-900">{{ old('sms_provider', $sms_provider) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">Arkesel (when selected)</dt>
                            <dd class="font-medium text-zinc-900">{{ old('sms_provider', $sms_provider) === 'arkesel' ? (arkesel_sms_ready() ? 'Ready' : 'Missing key or sender') : '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">SMS</dt>
                            <dd class="font-medium text-zinc-900">{{ $sms_notifications_enabled ? 'On' : 'Off' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-zinc-500">WhatsApp</dt>
                            <dd class="font-medium text-zinc-900">{{ $whatsapp_notifications_enabled ? 'On' : 'Off' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50/80 p-5">
                    <h3 class="text-sm font-semibold text-zinc-700">Environment defaults</h3>
                    <p class="mt-2 text-sm text-zinc-500">When a field in this form is left blank (or not saved), Laravel reads <span class="font-mono">SMS_PROVIDER</span>, <span class="font-mono">SMS_API_KEY</span>, <span class="font-mono">SMS_SENDER</span> from the server environment. Saving the SMS channel here always stores the chosen provider in the database and overrides the file for that value.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
