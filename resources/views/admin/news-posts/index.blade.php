@extends('layouts.dashboard')

@section('title', 'Popular news — Admin')
@section('heading', 'Popular news')
@section('subheading', 'Stories shown in the “Popular news” block on the storefront home page.')

@section('content')
    <div class="flex flex-wrap items-center justify-end gap-4">
        <a href="{{ route('dashboard.news-posts.create') }}" class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50">Add post</a>
    </div>

    <p class="mt-4 max-w-3xl text-sm text-zinc-600">Use a <strong class="font-medium text-zinc-800">wide image</strong> (16:10 works well). Upload replaces the image path; you can also keep files in <code class="rounded bg-zinc-100 px-1 text-xs">public/images/</code> and paste the path.</p>

    @if ($posts->isEmpty())
        <p class="mt-10 rounded-xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-sm text-neutral-600">
            No posts yet. <a href="{{ route('dashboard.news-posts.create') }}" class="font-medium text-neutral-900 underline">Add your first story</a>.
        </p>
    @else
        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($posts as $post)
                <article class="flex flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                    <div class="aspect-[16/10] bg-neutral-100">
                        <img src="{{ $post->resolveImageUrl() }}" alt="" class="h-full w-full object-cover">
                    </div>
                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-[#2563eb]">{{ $post->category }}</p>
                        <h2 class="mt-1 text-sm font-semibold leading-snug text-neutral-900">{{ $post->headline }}</h2>
                        <p class="mt-1 text-xs text-neutral-500">{{ $post->published_at->format('M j, Y') }}</p>
                        <p class="mt-2 text-xs text-neutral-500">{{ $post->is_active ? 'Visible on store' : 'Hidden' }} · Order {{ $post->sort_order }}</p>
                        <div class="mt-4 flex flex-wrap gap-3 border-t border-neutral-100 pt-3">
                            <a href="{{ route('dashboard.news-posts.edit', $post) }}" class="text-sm font-medium text-[#0057b8] underline decoration-[#cce0f7] underline-offset-2 hover:text-[#00479a]">Edit</a>
                            <form action="{{ route('dashboard.news-posts.destroy', $post) }}" method="post" class="inline" onsubmit="return confirm('Delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 underline hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <p class="mt-10 text-sm text-neutral-500">
        <a href="{{ route('dashboard.index') }}" class="font-medium text-neutral-900 underline">Admin overview</a>
        ·
        <a href="{{ route('home') }}#popular-news" class="underline hover:text-neutral-900">View on storefront</a>
    </p>
@endsection
