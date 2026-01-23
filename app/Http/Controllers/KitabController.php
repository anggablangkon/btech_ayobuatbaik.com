<?php

namespace App\Http\Controllers;

use App\Models\Kitab;
use App\Models\KitabChapter;
use App\Models\KitabMaqolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class KitabController extends Controller
{
    /**
     * Menampilkan daftar semua kitab
     */
    public function index()
    {
        $kitabs = Cache::remember("kitabs_list_v2", 43200, function () {
            return Kitab::active()
                ->withCount('chapters')
                ->orderBy('urutan', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        });

        return view("pages.kitab.index", compact("kitabs"));
    }

    /**
     * Menampilkan daftar bab dalam satu kitab
     */
    public function show($kitabSlug)
    {
        $kitab = Cache::remember("kitab_{$kitabSlug}", 43200, function () use ($kitabSlug) {
            return Kitab::where('slug', $kitabSlug)
                ->where('is_active', true)
                ->firstOrFail();
        });

        $chapters = Cache::remember("kitab_{$kitabSlug}_chapters", 43200, function () use ($kitab) {
            return KitabChapter::where('kitab_id', $kitab->id)
                ->with(['maqolahs' => function ($query) {
                    $query->select('id', 'chapter_id', 'nomor_maqolah', 'judul', 'urutan')->orderBy('urutan');
                }])
                ->withCount("maqolahs")
                ->orderBy("urutan")
                ->get();
        });

        $totalMaqolah = Cache::remember("kitab_{$kitabSlug}_total_maqolah", 43200, function () use ($kitab) {
            return KitabMaqolah::whereHas('chapter', function ($q) use ($kitab) {
                $q->where('kitab_id', $kitab->id);
            })->count();
        });

        // Get latest update timestamp for offline sync detection
        $latestUpdate = Cache::remember("kitab_{$kitabSlug}_latest_update", 43200, function () use ($kitab) {
            $latest = KitabMaqolah::whereHas('chapter', function ($q) use ($kitab) {
                $q->where('kitab_id', $kitab->id);
            })->max('updated_at');
            return $latest ? strtotime($latest) : 0;
        });

        return view("pages.kitab.show", compact("kitab", "chapters", "totalMaqolah", "latestUpdate"));
    }

    /**
     * Menampilkan maqolah dalam satu bab
     */
    public function showChapter($kitabSlug, $chapterSlug)
    {
        $kitab = Kitab::where('slug', $kitabSlug)->where('is_active', true)->firstOrFail();

        $chapter = Cache::remember("kitab_chapter_{$chapterSlug}", 43200, function () use ($kitab, $chapterSlug) {
            return KitabChapter::where("kitab_id", $kitab->id)
                ->where("slug", $chapterSlug)
                ->with([
                    "maqolahs" => function ($query) {
                        $query->orderBy("urutan");
                    },
                ])
                ->firstOrFail();
        });

        return view("pages.kitab.chapter", compact("kitab", "chapter"));
    }

    /**
     * Menampilkan detail maqolah tunggal
     */
    public function showMaqolah($kitabSlug, $chapterSlug, $id)
    {
        $kitab = Kitab::where('slug', $kitabSlug)->where('is_active', true)->firstOrFail();

        $cacheKey = "kitab_maqolah_{$id}";
        $data = Cache::remember($cacheKey, 43200, function () use ($kitab, $chapterSlug, $id) {
            $chapter = KitabChapter::where("kitab_id", $kitab->id)
                ->where("slug", $chapterSlug)
                ->firstOrFail();
            $maqolah = KitabMaqolah::where("chapter_id", $chapter->id)->where("id", $id)->firstOrFail();

            // Get previous and next maqolah
            $previous = KitabMaqolah::where("chapter_id", $chapter->id)->where("urutan", "<", $maqolah->urutan)->orderBy("urutan", "desc")->first();
            $next = KitabMaqolah::where("chapter_id", $chapter->id)->where("urutan", ">", $maqolah->urutan)->orderBy("urutan", "asc")->first();

            return compact("chapter", "maqolah", "previous", "next");
        });

        $data['kitab'] = $kitab;

        return view("pages.kitab.maqolah", $data);
    }

    /**
     * API: Mengembalikan semua URL Kitab untuk offline caching
     */
    public function getAllUrls(Request $request)
    {
        $kitabSlug = $request->query('kitab');

        if (!$kitabSlug) {
            return response()->json(['error' => 'kitab parameter required'], 400);
        }

        $kitab = Kitab::where('slug', $kitabSlug)->where('is_active', true)->firstOrFail();

        $urls = ['/kitab', '/kitab/' . $kitab->slug];

        $chapters = KitabChapter::where('kitab_id', $kitab->id)
            ->with('maqolahs:id,chapter_id,nomor_maqolah,updated_at')
            ->orderBy('urutan')
            ->get();

        // Track latest update time
        $latestUpdate = null;

        foreach ($chapters as $chapter) {
            // Chapter URL
            $urls[] = '/kitab/' . $kitab->slug . '/' . $chapter->slug;

            // Maqolah URLs
            foreach ($chapter->maqolahs as $maqolah) {
                $urls[] = '/kitab/' . $kitab->slug . '/' . $chapter->slug . '/maqolah/' . $maqolah->id;

                // Track latest updated_at
                if (!$latestUpdate || $maqolah->updated_at > $latestUpdate) {
                    $latestUpdate = $maqolah->updated_at;
                }
            }
        }

        return response()->json([
            'kitab' => $kitab->name,
            'total' => count($urls),
            'urls' => $urls,
            'latest_update' => $latestUpdate ? $latestUpdate->timestamp : null
        ]);
    }
}
