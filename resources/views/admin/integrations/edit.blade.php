@extends('layouts.dashboard')

@section('title', 'Integrations — '.config('app.name'))
@section('heading', 'Integrations')
@section('subheading', 'Connect payment and external services. Keys are stored securely.')

@section('content')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7">
            <form action="{{ route('dashboard.integrations.update') }}" method="post" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 px-5 py-4">
                        <h2 class="text-base font-semibold text-zinc-900">Paystack</h2>
                        <p class="mt-1 text-sm text-zinc-500">Public and secret keys from your Paystack dashboard. Values here override <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">.env</code> when set.</p>
                    </div>
                    <div class="space-y-5 px-5 py-5">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3">
                            <input type="hidden" name="paystack_enabled" value="0">
                            <input type="checkbox" name="paystack_enabled" value="1" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900" @checked($paystack_enabled)>
                            <span class="text-sm font-medium text-zinc-900">Enable Paystack</span>
                        </label>

                        <div>
                            <label for="paystack_public_key" class="block text-sm font-medium text-zinc-700">Public key</label>
                            <input type="text" name="paystack_public_key" id="paystack_public_key" value="{{ old('paystack_public_key', $paystack_public_key) }}" autocomplete="off" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="pk_live_… or pk_test_…">
                        </div>

                        <div>
                            <label for="paystack_secret_key" class="block text-sm font-medium text-zinc-700">Secret key</label>
                            <input type="password" name="paystack_secret_key" id="paystack_secret_key" value="" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900" placeholder="{{ $has_paystack_secret ? 'Leave blank to keep current secret' : 'sk_live_… or sk_test_…' }}">
                            @if ($has_paystack_secret)
                                <p class="mt-1.5 text-xs text-zinc-500">A secret is saved. Enter a new value only if you want to replace it.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
                    <div class="border-b border-zinc-100 px-5 py-4">
                        <h2 class="text-base font-semibold text-zinc-900">SMS &amp; WhatsApp</h2>
                        <p class="mt-1 text-sm text-zinc-500">Order updates for customers (Ghana numbers as +233). Put API keys in <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs text-zinc-700">.env</code>; toggles control sending (cost control).</p>
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

                        <p class="text-xs text-zinc-500">Env: <span class="font-mono text-zinc-700">SMS_PROVIDER</span>={{ $sms_provider_env }}, <span class="font-mono text-zinc-700">SMS_SENDER</span>={{ $sms_sender_env }}. Use <span class="font-mono">SMS_PROVIDER=log</span> to only log messages locally.</p>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-800">Save integrations</button>
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
                            <dt class="text-zinc-500">Keys</dt>
                            <dd class="font-medium text-zinc-900">{{ paystack_ready() ? 'Ready' : 'Incomplete' }}</dd>
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
                    <h3 class="text-sm font-semibold text-zinc-700">More providers</h3>
                    <p class="mt-2 text-sm text-zinc-500">Additional gateways (for example Stripe or mobile money aggregators) can be added here as separate cards with the same pattern: enable toggle, environment keys, and webhooks.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
