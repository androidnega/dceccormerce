@extends('layouts.dashboard')

@section('title', 'Edit category — Admin')
@section('heading', 'Edit category')
@section('subheading', 'Slug updates when you change the name.')

@section('content')
    <form action="{{ route('dashboard.categories.update', $category) }}" method="post" class="mt-0 max-w-md space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save</button>
            <a href="{{ route('dashboard.categories.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
