@extends('layouts.dashboard')

@section('title', 'Edit promo — Admin')
@section('heading', 'Edit promo')

@section('content')
    <form action="{{ route('dashboard.promos.update', $promo) }}" method="post" enctype="multipart/form-data" class="mt-6 max-w-xl space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Title <span class="text-red-600">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', $promo->title) }}" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-slate-700">Type <span class="text-red-600">*</span></label>
            <select name="type" id="type" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (\App\Models\Promo::TYPES as $t)
                    <option value="{{ $t }}" @selected(old('type', $promo->type) === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="value" class="block text-sm font-medium text-slate-700">Value</label>
            <input type="text" name="value" id="value" value="{{ old('value', $promo->value) }}" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="media_kind" class="block text-sm font-medium text-slate-700">Homepage media</label>
            <select name="media_kind" id="media_kind" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                @foreach (\App\Models\Promo::MEDIA_KINDS as $k)
                    <option value="{{ $k }}" @selected(old('media_kind', $promo->media_kind ?? 'none') === $k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        @if ($promo->media_upload_path)
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-xs font-medium text-slate-600">Current upload</p>
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($promo->media_upload_path) }}" alt="" class="mt-2 max-h-40 rounded border border-slate-200 object-contain">
                <label class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="remove_promo_image" value="1" class="rounded border-slate-300">
                    Remove uploaded image
                </label>
            </div>
        @endif
        <div>
            <label for="promo_image" class="block text-sm font-medium text-slate-700">Replace / add image upload</label>
            <input type="file" name="promo_image" id="promo_image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white">
        </div>
        <div>
            <label for="media_external_url" class="block text-sm font-medium text-slate-700">Image or video URL</label>
            <input type="url" name="media_external_url" id="media_external_url" value="{{ old('media_external_url', $promo->media_external_url) }}" placeholder="https://…" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-medium text-slate-700">Sort order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $promo->sort_order) }}" min="0" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div>
            <label for="homepage_slot" class="block text-sm font-medium text-slate-700">Homepage section <span class="text-red-600">*</span></label>
            <select name="homepage_slot" id="homepage_slot" required class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
                <option value="{{ \App\Models\Promo::SLOT_SECONDARY }}" @selected(old('homepage_slot', $promo->homepage_slot ?? \App\Models\Promo::SLOT_PRIMARY) === \App\Models\Promo::SLOT_SECONDARY)>Upper strip (Spotlight)</option>
                <option value="{{ \App\Models\Promo::SLOT_PRIMARY }}" @selected(old('homepage_slot', $promo->homepage_slot ?? \App\Models\Promo::SLOT_PRIMARY) === \App\Models\Promo::SLOT_PRIMARY)>Lower grid (More offers)</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" @checked(old('is_active', $promo->is_active))>
            <label for="is_active" class="text-sm text-slate-700">Active</label>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save</button>
            <a href="{{ route('dashboard.promos.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
        </div>
    </form>
@endsection
