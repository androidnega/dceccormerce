@extends('layouts.dashboard')

@section('title', 'Edit homepage section — Admin')
@section('heading', 'Edit homepage section')
@section('subheading', $section->type)

@section('content')
    <form action="{{ route('dashboard.homepage-sections.update', $section) }}" method="post" enctype="multipart/form-data" class="max-w-2xl space-y-5">
        @csrf
        @method('PUT')
        @include('admin.homepage-sections._form', ['section' => $section])
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-[#0057b8] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#00479a]">Update section</button>
            <a href="{{ route('dashboard.homepage-sections.index') }}" class="rounded-lg border border-neutral-200 px-5 py-2.5 text-sm font-medium text-neutral-800 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection
