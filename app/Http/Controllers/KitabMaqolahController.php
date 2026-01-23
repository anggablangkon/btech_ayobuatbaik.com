<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KitabChapter;
use App\Models\KitabMaqolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KitabMaqolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chapters = KitabChapter::with('kitab')->orderBy('kitab_id')->orderBy("nomor_bab")->get();
        $search = $request->search;

        if (!$request->has('chapter') && !$search) {
            // Grouped view: Default landing overview
            $chaptersWithMaqolahs = KitabChapter::with(['maqolahs' => function($q) {
                $q->orderBy('nomor_maqolah', 'asc');
            }])
            ->orderBy('nomor_bab', 'asc')
            ->paginate($request->get("perPage", 5)); // Paginate chapters for better performance if many

            return view("pages.admin.kitab-maqolah.index", [
                "chapters" => $chapters,
                "chaptersWithMaqolahs" => $chaptersWithMaqolahs,
                "isGrouped" => true,
                "selectedChapter" => null
            ]);
        }

        // Standard Flat View (Filtered or Searched)
        $query = KitabMaqolah::with("chapter");
        $selectedChapterId = $request->chapter;

        if ($selectedChapterId) {
            $query->where("chapter_id", $selectedChapterId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("judul", "like", "%" . $search . "%")
                  ->orWhere("konten", "like", "%" . $search . "%");
            });
        }

        // Sort: Default to Chapter then Maqolah if not specified
        $sortField = $request->get("sort", "chapter_nomor");
        $sortDirection = $request->get("direction", "asc");

        if ($sortField === "chapter_nomor" || $sortField === "chapter") {
            $query->join("kitab_chapters", "kitab_maqolah.chapter_id", "=", "kitab_chapters.id")
                  ->orderBy("kitab_chapters.nomor_bab", $sortDirection)
                  ->orderBy("kitab_maqolah.nomor_maqolah", "asc")
                  ->select("kitab_maqolah.*");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $maqolahs = $query->paginate($request->get("perPage", 10));
        $selectedChapter = ($selectedChapterId && $selectedChapterId !== "") ? KitabChapter::find($selectedChapterId) : null;

        return view("pages.admin.kitab-maqolah.index", [
            "maqolahs" => $maqolahs,
            "chapters" => $chapters,
            "selectedChapter" => $selectedChapter,
            "isGrouped" => false
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $chapters = KitabChapter::with('kitab')->orderBy('kitab_id')->orderBy("nomor_bab")->get();
        $selectedChapterId = $request->chapter;

        // Get next nomor_maqolah for selected chapter
        $nextNomor = 1;
        if ($selectedChapterId) {
            $lastMaqolah = KitabMaqolah::where("chapter_id", $selectedChapterId)->orderBy("nomor_maqolah", "desc")->first();
            $nextNomor = $lastMaqolah ? $lastMaqolah->nomor_maqolah + 1 : 1;
        }

        return view("pages.admin.kitab-maqolah.create", compact("chapters", "selectedChapterId", "nextNomor"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "chapter_id" => "required|exists:kitab_chapters,id",
            "nomor_maqolah" => "required|integer",
            "judul" => "required|string|max:255",
            "konten" => "required|string",
        ]);

        $validated["urutan"] = $validated["nomor_maqolah"];

        $maqolah = KitabMaqolah::create($validated);

        // Hapus cache kitab
        Cache::forget('kitab_chapters_list');
        Cache::forget('kitab_total_maqolah');
        Cache::forget("kitab_chapter_{$maqolah->chapter->slug}");

        return redirect()
            ->route("admin.kitab_maqolah.index", ["chapter" => $validated["chapter_id"]])
            ->with("success", "Maqolah berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(KitabMaqolah $kitabMaqolah)
    {
        return redirect()->route("home.kitab.maqolah", [
            "chapterSlug" => $kitabMaqolah->chapter->slug,
            "id" => $kitabMaqolah->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KitabMaqolah $kitabMaqolah)
    {
        $chapters = KitabChapter::with('kitab')->orderBy('kitab_id')->orderBy("nomor_bab")->get();

        return view("pages.admin.kitab-maqolah.edit", compact("kitabMaqolah", "chapters"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KitabMaqolah $kitabMaqolah)
    {
        $validated = $request->validate([
            "chapter_id" => "required|exists:kitab_chapters,id",
            "nomor_maqolah" => "required|integer",
            "judul" => "required|string|max:255",
            "konten" => "required|string",
        ]);

        $validated["urutan"] = $validated["nomor_maqolah"];

        $oldChapterSlug = $kitabMaqolah->chapter->slug;
        $kitabMaqolah->update($validated);

        // Hapus cache kitab
        Cache::forget('kitab_chapters_list');
        Cache::forget('kitab_total_maqolah');
        Cache::forget("kitab_chapter_{$oldChapterSlug}");
        if ($oldChapterSlug !== $kitabMaqolah->chapter->slug) {
            Cache::forget("kitab_chapter_{$kitabMaqolah->chapter->slug}");
        }
        Cache::forget("kitab_maqolah_{$kitabMaqolah->id}");

        return redirect()
            ->route("admin.kitab_maqolah.index", ["chapter" => $validated["chapter_id"]])
            ->with("success", "Maqolah berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KitabMaqolah $kitabMaqolah)
    {
        $chapterId = $kitabMaqolah->chapter_id;
        $chapterSlug = $kitabMaqolah->chapter->slug;
        $maqolahId = $kitabMaqolah->id;
        $kitabMaqolah->delete();

        // Hapus cache kitab
        Cache::forget('kitab_chapters_list');
        Cache::forget('kitab_total_maqolah');
        Cache::forget("kitab_chapter_{$chapterSlug}");
        Cache::forget("kitab_maqolah_{$maqolahId}");

        return redirect()
            ->route("admin.kitab_maqolah.index", ["chapter" => $chapterId])
            ->with("success", "Maqolah berhasil dihapus.");
    }
}
