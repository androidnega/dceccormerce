@extends('layout')

@section('title', 'Return & Refund policy — ' . config('app.name'))
@section('main_class', 'w-full min-w-0 flex-1 bg-slate-50/80')

@section('content')
    <div class="store-box max-w-3xl py-10 sm:py-14">
        <p class="text-xs font-medium uppercase tracking-[0.12em] text-slate-500">Legal</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Return &amp; refund policy</h1>
        <p class="mt-2 text-sm text-slate-600">Last updated: {{ now()->format('F j, Y') }}. This policy follows common e-commerce practice. If anything here conflicts with an agreement you have with us in writing, the written agreement applies.</p>

        <div class="prose prose-slate mt-8 max-w-none text-sm leading-relaxed text-slate-700">
            <h2 class="text-base font-semibold text-slate-900">1. Our commitment</h2>
            <p>We want you to shop with confidence. If your order is wrong, damaged, or we cannot get it to you as agreed, we will make it right with a repair, replacement, or refund, depending on what is reasonable in your situation.</p>

            <h2 class="mt-8 text-base font-semibold text-slate-900">2. When you can return or get a refund</h2>
            <ul class="list-disc space-y-2 pl-5">
                <li><strong>Defective or not as described:</strong> Contact us as soon as you notice a fault or a mismatch with what you ordered. We may ask for a short description or photos to help us fix the issue quickly.</li>
                <li><strong>Non-delivery / failed delivery:</strong> If the order cannot be completed through no fault of yours (for example dispatch or delivery failure on our side), you are entitled to a full refund of what you paid for the order, or a replacement if you prefer and stock allows.</li>
                <li><strong>Change of mind:</strong> Where the law or a specific promotion does not require otherwise, discretionary returns for “change of mind” may be limited to unopened, unused products in original condition within a short window (often 7–14 days from delivery). Contact <a href="mailto:{{ $storeEmail }}" class="font-medium text-[#0057b8] underline">{{ $storeEmail }}</a> before sending anything back.</li>
            </ul>

            <h2 class="mt-8 text-base font-semibold text-slate-900">3. How refunds work (including Paystack / Mobile Money)</h2>
            <p><strong>Cash on delivery (COD):</strong> You pay when the order is delivered. If the order is cancelled or fails before you pay, nothing is due. If you already paid in cash and a full refund is approved, we will return that amount in a way that matches how you paid, where practical.</p>
            <p><strong>Prepaid (Paystack / Mobile Money / card):</strong> When a refund is due, we issue it to the same payment path where possible. Paystack processes the money movement; timing depends on your bank or mobile money provider, often a few business days after we trigger the refund.</p>
            <p><strong>Automatic refunds:</strong> For orders paid with Paystack, if the delivery is marked <strong>failed</strong> in our system (we could not complete the delivery in line with our process), we automatically request a <strong>full refund</strong> of that order with Paystack. You do not need to do anything. If a refund does not show up, email us with your <strong>order number</strong> and we will follow up, including the Paystack transaction or refund reference when available.</p>
            <p class="text-slate-600">Failed online payments: if a payment is declined or not completed, you are not charged. Any duplicate or incorrect charge is reconciled on request to <a href="mailto:{{ $storeEmail }}" class="font-medium text-[#0057b8] underline">{{ $storeEmail }}</a>.</p>

            <h2 class="mt-8 text-base font-semibold text-slate-900">4. Exclusions</h2>
            <p>Some items (for example personalised, opened software, or clearance products) may be marked as non-returnable at purchase. We will show that clearly where it applies. Abuse of returns (for example repeat misuse) may be refused in line with fair use.</p>

            <h2 class="mt-8 text-base font-semibold text-slate-900">5. Contact</h2>
            <p>Questions about this policy or a specific order: <a href="mailto:{{ $storeEmail }}" class="font-medium text-[#0057b8] underline">{{ $storeEmail }}</a></p>
        </div>
    </div>
@endsection
