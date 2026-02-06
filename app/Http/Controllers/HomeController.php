<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Donation;
use App\Models\KategoriDonasi;
use App\Models\ProgramDonasi;
use App\Models\Slider;
use Cache;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Cache kategori selama 24 jam
        $kategori = Cache::remember("kategori_donasi", 86400, function () {
            return KategoriDonasi::orderBy("name")->get();
        });
        // Cache slider selama 1 jam
        $sliders = Cache::remember("sliders_home", 3600, function () {
            return Slider::orderBy("urutan", "asc")->get();
        });
        // Featured programs bisa di-cache 10 menit
        $featuredPrograms = Cache::remember("featured_programs", 600, function () {
            return ProgramDonasi::with(["kategori", "penggalang"])
                ->where("featured", 1)
                ->where("status", "active")
                ->orderBy("id", "desc")
                ->take(3)
                ->get();
        });
        $otherPrograms = Cache::remember("other_programs", 3600, function () {
            return ProgramDonasi::with(["kategori", "penggalang"])
                ->where("featured", 0)
                ->where("status", "active")
                ->orderBy("id", "desc")
                ->take(6)
                ->get();
        });
        $berita = Cache::remember("berita_latest_4", 3600, function () {
            return Berita::orderBy("tanggal", "desc")->take(4)->get();
        });

        $prayers = Cache::remember('latest_prayers', 3600, function () {
            return Donation::where('status', 'success')
                ->whereNotNull('note')
                ->where('note', '!=', '')
                ->where('note', '!=', 'null')
                ->latest()
                ->take(10)
                ->get();
        });

        return view("pages.home", compact("featuredPrograms", "otherPrograms", "kategori", "sliders", "berita", "prayers"));
    }

    public function programs()
    {
        $kategories = KategoriDonasi::select("id", "name", "slug")->get();

        $programs = ProgramDonasi::select(
            "id",
            "slug",
            "title",
            "short_description",
            "gambar",
            "target_amount",
            "collected_amount",
            "end_date",
            "status",
            "verified",
            "kategori_id",
        )
            ->with(["kategori:id,name,slug"]) // Juga specify untuk eager load
            ->where("status", "active")
            ->latest()
            ->get();
        return view("pages.program-donasi.programs", compact("kategories", "programs"));
    }

    public function program($slug)
    {
        $program = ProgramDonasi::where("slug", $slug)->firstOrFail();

        // 📊 Increment view count (unique per session)
        $sessionKey = "viewed_program_{$program->id}";
        if (!session()->has($sessionKey)) {
            $program->increment('view_count');
            session([$sessionKey => true]);
        }

        // 1. Ambil daftar donasi sukses (maksimal 20 atau lebih, disarankan dibatasi)
        // Diurutkan berdasarkan yang terbaru (created_at atau status_change_at)
        $donations = Donation::where("program_donasi_id", $program->id)
            ->where("status", "success")
            ->latest()
            ->limit(20) // Batasi jumlah yang ditampilkan di tab Donatur
            ->get();

        $donorsCount = Cache::remember("donors_count_{$program->id}", 300, function () use ($program) {
            return $program->donations()->where("status", "success")->count();
        });
        $relatedPrograms = ProgramDonasi::where("id", "!=", $program->id)->inRandomOrder()->take(3)->get();

        if ($relatedPrograms->count() < 3) {
            $relatedPrograms = ProgramDonasi::where("id", "!=", $program->id)->get();
        }

        return view("pages.program-donasi.single-program", compact("program", "relatedPrograms", "donations", "donorsCount"));
    }

    public function search(Request $request)
    {
        $keyword = $request->input("q");

        $programs = ProgramDonasi::search($keyword)->paginate(10);

        return view("pages.program-donasi.search", compact("programs", "keyword"));
    }

    public function berita()
    {
        $beritas = Berita::latest()->paginate(7);
        return view("pages.berita.index", compact("beritas"));
    }

    public function showBerita($slug)
    {
        $berita = Berita::where("slug", $slug)->firstOrFail();

        // Ambil 3 berita terkait/terbaru lainnya secara acak (kecuali berita saat ini)
        $relatedBeritas = Berita::where("id", "!=", $berita->id)->inRandomOrder()->limit(3)->get();

        // Opsional: Hitung view/baca berita (jika ada kolom views di tabel Berita)
        // $berita->increment('views');

        return view("pages.berita.berita", compact("berita", "relatedBeritas"));
    }
}
