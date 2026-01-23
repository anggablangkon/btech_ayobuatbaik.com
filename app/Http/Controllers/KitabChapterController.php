<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kitab;
use App\Models\KitabChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class KitabChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KitabChapter::with('kitab')->withCount("maqolahs");

        // Search
        if ($request->search) {
            $query->where("judul_bab", "like", "%" . $request->search . "%");
        }

        // Filter by kitab
        if ($request->kitab_id) {
            $query->where('kitab_id', $request->kitab_id);
        }

        // Sort
        $sortField = $request->get("sort", "nomor_bab");
        $sortDirection = $request->get("direction", "asc");
        $query->orderBy($sortField, $sortDirection);

        $chapters = $query->paginate($request->get("perPage", 10));
        $kitabs = Kitab::orderBy('name')->get();

        return view("pages.admin.kitab-chapter.index", compact("chapters", "kitabs"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedKitabId = $request->get('kitab_id');
        
        $query = KitabChapter::query();
        if ($selectedKitabId) {
            $query->where('kitab_id', $selectedKitabId);
        }
        
        $lastChapter = $query->orderBy("nomor_bab", "desc")->first();
        $nextNomor = $lastChapter ? $lastChapter->nomor_bab + 1 : 1;
        $kitabs = Kitab::orderBy('name')->get();

        return view("pages.admin.kitab-chapter.create", compact("nextNomor", "kitabs", "selectedKitabId"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "kitab_id" => "required|exists:kitabs,id",
            "nomor_bab" => "required|integer",
            "judul_bab" => "nullable|string|max:255",
            "deskripsi" => "nullable|string",
        ]);

        $validated["judul_bab"] = $request->judul_bab ?? "";
        $validated["slug"] = Str::slug("bab-" . $validated["nomor_bab"] . ($request->judul_bab ? "-" . $request->judul_bab : ""));
        $validated["urutan"] = $validated["nomor_bab"];

        KitabChapter::create($validated);

        // Hapus cache kitab
        $this->clearChapterCache($validated['kitab_id']);

        return redirect()->route("admin.kitab_chapter.index")->with("success", "Bab berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(KitabChapter $kitabChapter)
    {
        return redirect()->route("admin.kitab_maqolah.index", ["chapter" => $kitabChapter->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KitabChapter $kitabChapter)
    {
        $kitabs = Kitab::orderBy('name')->get();
        return view("pages.admin.kitab-chapter.edit", compact("kitabChapter", "kitabs"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KitabChapter $kitabChapter)
    {
        $validated = $request->validate([
            "kitab_id" => "required|exists:kitabs,id",
            "nomor_bab" => "required|integer",
            "judul_bab" => "nullable|string|max:255",
            "deskripsi" => "nullable|string",
        ]);

        $validated["judul_bab"] = $request->judul_bab ?? "";
        $validated["slug"] = Str::slug("bab-" . $validated["nomor_bab"] . ($request->judul_bab ? "-" . $request->judul_bab : ""));
        $validated["urutan"] = $validated["nomor_bab"];

        $oldSlug = $kitabChapter->slug;
        $kitabChapter->update($validated);

        // Hapus cache kitab
        $this->clearChapterCache($validated['kitab_id']);
        Cache::forget("kitab_chapter_{$oldSlug}");
        if ($oldSlug !== $kitabChapter->slug) {
            Cache::forget("kitab_chapter_{$kitabChapter->slug}");
        }

        return redirect()->route("admin.kitab_chapter.index")->with("success", "Bab berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KitabChapter $kitabChapter)
    {
        $kitabId = $kitabChapter->kitab_id;
        $chapterSlug = $kitabChapter->slug;
        $kitabChapter->delete();

        // Hapus cache kitab
        $this->clearChapterCache($kitabId);
        Cache::forget("kitab_chapter_{$chapterSlug}");

        return redirect()->route("admin.kitab_chapter.index")->with("success", "Bab berhasil dihapus.");
    }

    /**
     * Clear chapter related cache.
     */
    private function clearChapterCache($kitabId)
    {
        $kitab = Kitab::find($kitabId);
        if ($kitab) {
            Cache::forget("kitab_{$kitab->slug}");
            Cache::forget("kitab_{$kitab->slug}_chapters");
            Cache::forget("kitab_{$kitab->slug}_total_maqolah");
            Cache::forget("kitab_{$kitab->slug}_latest_update");
        }
        Cache::forget('kitabs_list');
    }
}
