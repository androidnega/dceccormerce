@extends('layouts.dashboard')

@section('title', 'On sale spotlight — Admin')
@section('heading', 'On sale spotlight')
@section('subheading', 'Edit the 3 cards shown in the homepage “On sale” section.')

@section('content')
    <form
        action="{{ route('dashboard.sale-spotlight.update') }}"
        method="post"
        enctype="multipart/form-data"
        class="admin-form max-w-5xl space-y-6"
    >
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-[#cce0f7] bg-[#e6f0fb] p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                <div class="max-w-2xl">
                    <p class="text-sm leading-relaxed text-slate-800">
                        Each slot is shown as one “Sale!” card on the storefront homepage. Pick a product and (optionally) upload an image override.
                    </p>
                    <p class="mt-1 text-xs text-slate-600">
                        If a slot is inactive, it won’t be shown.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @php $positions = [0, 1, 2]; @endphp
            @foreach ($positions as $pos)
                @php
                    $card = $cards[$pos] ?? null;
                    $oldProductId = old("slots.$pos.product_id", $card?->product_id);
                    $oldIsActive = old("slots.$pos.is_active", $card?->is_active ?? false);
                @endphp

                <div class="rounded-xl border border-neutral-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-semibold text-neutral-900">Card {{ $pos + 1 }}</div>
                            <div class="mt-0.5 text-xs text-neutral-500">Displayed as a “Sale!” product.</div>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-neutral-700">
                            <input type="hidden" name="slots[{{ $pos }}][is_active]" value="0">
                            <input
                                type="checkbox"
                                name="slots[{{ $pos }}][is_active]"
                                value="1"
                                class="rounded border-neutral-300 text-[#0057b8]"
                                @checked((string) $oldIsActive === '1' || $oldIsActive === true)
                            >
                            Active
                        </label>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-neutral-700">Product</label>
                        <select
                            name="slots[{{ $pos }}][product_id]"
                            class="mt-1 w-full rounded-md border border-neutral-300 px-3 py-2 text-neutral-900 shadow-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500"
                        >
                            <option value="">— None —</option>
                            @foreach ($products as $p)
                                <option
                                    value="{{ $p->id }}"
                                    @selected((string) $oldProductId === (string) $p->id)
                                >
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-neutral-700">Image override (optional)</label>

                        @if ($card !== null && $card->image_path)
                            <div class="mt-2 rounded-lg border border-neutral-200 bg-neutral-50 p-3">
                                <div class="text-xs font-medium text-neutral-500">Current image</div>
                                <img
                                    src="{{ $card->imageUrl() }}"
                                    alt=""
                                    class="mt-2 max-h-24 w-full rounded object-contain"
                                >
                                <label class="mt-2 flex items-center gap-2 text-xs text-neutral-700">
                                    <input
                                        type="checkbox"
                                        name="slots[{{ $pos }}][remove_image]"
                                        value="1"
                                        class="rounded border-neutral-300 text-neutral-900"
                                    >
                                    Remove override
                                </label>
                            </div>
                        @endif

                        <input
                            type="file"
                            name="slots[{{ $pos }}][image]"
                            accept="image/*"
                            class="mt-2 block w-full text-sm text-neutral-600 file:mr-4 file:rounded-md file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-700"
                        >

                        <p class="mt-1 text-xs text-neutral-500">
                            Leave empty to show the product’s default image.
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                Save
            </button>
            <a href="{{ route('dashboard.index') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">
                Cancel
            </a>
        </div>
    </form>
@endsection

