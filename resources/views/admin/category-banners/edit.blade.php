@extends('layouts.dashboard')

@section('title', 'Edit category card — Admin')
@section('heading', 'Edit Shop by category card')
@section('subheading', 'Change the image, text, or video for this card.')

@section('content')
    <form action="{{ route('dashboard.category-banners.update', $banner) }}" method="post" enctype="multipart/form-data" class="max-w-2xl space-y-5">
        @csrf
        @method('PUT')
        @include('admin.category-banners._form', ['banner' => $banner, 'types' => $types])
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-md bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">Save</button>
            <a href="{{ route('dashboard.category-banners.index') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection

