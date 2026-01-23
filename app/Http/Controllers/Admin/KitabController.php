<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kitab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KitabController extends Controller
{
    /**
     * Display a listing of kitabs.
     */
    public function index()
    {
        $kitabs = Kitab::withCount('chapters')->orderBy('urutan', 'asc')->orderBy('name', 'asc')->get();
        return view('pages.admin.kitab.index', compact('kitabs'));
    }

    /**
     * Show the form for creating a new kitab.
     */
    public function create()
    {
        return view('pages.admin.kitab.create');
    }

    /**
     * Store a newly created kitab in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('kitab-covers', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Kitab::create($validated);

        // Clear cache
        $this->clearKitabCache();

        return redirect()->route('admin.kitab.index')->with('success', 'Kitab berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified kitab.
     */
    public function edit(Kitab $kitab)
    {
        return view('pages.admin.kitab.edit', compact('kitab'));
    }

    /**
     * Update the specified kitab in storage.
     */
    public function update(Request $request, Kitab $kitab)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'urutan' => 'integer',
        ]);

        // Update slug if name changed
        if ($kitab->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($kitab->cover_image) {
                Storage::disk('public')->delete($kitab->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('kitab-covers', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $kitab->update($validated);

        // Clear cache
        $this->clearKitabCache();

        return redirect()->route('admin.kitab.index')->with('success', 'Kitab berhasil diperbarui.');
    }

    /**
     * Remove the specified kitab from storage.
     */
    public function destroy(Kitab $kitab)
    {
        // Delete cover image
        if ($kitab->cover_image) {
            Storage::disk('public')->delete($kitab->cover_image);
        }

        $kitab->delete();

        // Clear cache
        $this->clearKitabCache();

        return redirect()->route('admin.kitab.index')->with('success', 'Kitab berhasil dihapus.');
    }

    /**
     * Clear kitab related cache.
     */
    private function clearKitabCache()
    {
        Cache::forget('kitabs_list');
        Cache::forget('kitabs_list_v2');
        // Clear individual kitab caches
        $kitabs = Kitab::all();
        foreach ($kitabs as $kitab) {
            Cache::forget("kitab_{$kitab->slug}");
            Cache::forget("kitab_{$kitab->slug}_chapters");
            Cache::forget("kitab_{$kitab->slug}_total_maqolah");
            Cache::forget("kitab_{$kitab->slug}_latest_update");
        }
    }
}
