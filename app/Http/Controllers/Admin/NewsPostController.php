<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsPostController extends Controller
{
    public function index(): View
    {
        $posts = NewsPost::query()->ordered()->get();

        return view('admin.news-posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.news-posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:64'],
            'headline' => ['required', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:512'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $path = '';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news-images', 'public');
        } elseif (isset($validated['image_path']) && trim($validated['image_path']) !== '') {
            $path = trim($validated['image_path']);
        }
        if ($path === '') {
            return redirect()->back()->withErrors(['image' => 'Upload an image or enter a path under public (e.g. images/example.webp).'])->withInput();
        }

        NewsPost::query()->create([
            'category' => $validated['category'],
            'headline' => $validated['headline'],
            'published_at' => $validated['published_at'],
            'image_path' => $path,
            'link_url' => $validated['link_url'] ?? '/shop#store-search',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.news-posts.index')
            ->with('status', 'News post created.');
    }

    public function edit(NewsPost $news_post): View
    {
        return view('admin.news-posts.edit', ['newsPost' => $news_post]);
    }

    public function update(Request $request, NewsPost $news_post): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:64'],
            'headline' => ['required', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'link_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:512'],
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $path = $news_post->image_path;
        if ($request->hasFile('image')) {
            if ($path !== '' && str_starts_with($path, 'news-images/')) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('image')->store('news-images', 'public');
        } elseif (isset($validated['image_path']) && trim((string) $validated['image_path']) !== '') {
            $path = trim($validated['image_path']);
        }

        $news_post->update([
            'category' => $validated['category'],
            'headline' => $validated['headline'],
            'published_at' => $validated['published_at'],
            'image_path' => $path,
            'link_url' => $validated['link_url'] ?? '/shop#store-search',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('dashboard.news-posts.index')
            ->with('status', 'News post updated.');
    }

    public function destroy(NewsPost $news_post): RedirectResponse
    {
        $path = $news_post->image_path;
        if ($path !== '' && str_starts_with($path, 'news-images/')) {
            Storage::disk('public')->delete($path);
        }
        $news_post->delete();

        return redirect()->route('dashboard.news-posts.index')
            ->with('status', 'News post deleted.');
    }
}
