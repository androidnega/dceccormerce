@extends('layouts.dashboard')

@section('title', 'Add homepage section — Admin')
@section('heading', 'Add homepage section')
@section('subheading', 'Appears on the store home page below the hero carousel.')

@section('content')
    <form action="{{ route('dashboard.homepage-sections.store') }}" method="post" enctype="multipart/form-data" class="max-w-2xl space-y-5">
        @csrf
        @include('admin.homepage-sections._form', ['section' => null])
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-[#0057b8] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#00479a]">Save section</button>
            <a href="{{ route('dashboard.homepage-sections.index') }}" class="rounded-lg border border-neutral-200 px-5 py-2.5 text-sm font-medium text-neutral-800 hover:bg-neutral-50">Cancel</a>
        </div>
    </form>
@endsection
