<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\ProgramDonasi;
use App\Models\User;
use Cache;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = Cache::remember("dashboard_stats", 300, function () {
            return [
                "total_programs" => ProgramDonasi::count(),
                "total_donations" => Donation::where("status", "success")->count(),
                "total_amount" => ProgramDonasi::sum("collected_amount"),
                "total_users" => User::count(),
            ];
        });

        $recent_donations_query = Donation::latest()->with("program")->take(3)->get();

        $recent_donations = $recent_donations_query->map(function ($donations) {
            return [
                "donor_name" => $donations->donor_name,
                "amount" => $donations->amount,
                "time" => $donations->created_at->diffForHumans(),
                "program" => $donations->program ? $donations->program->title : "Unknown Program",
            ];
        });

        // Ambil 3 program terbaru
        $recent_programs = ProgramDonasi::latest()
            ->take(3)
            ->get(["title", "collected_amount", "target_amount", "gambar"]);
        return view("pages.admin.dashboard", compact("stats", "recent_donations", "recent_programs"));
    }

    public function programs()
    {
        //   $programs = Program::latest()->paginate(10);
        // return view('pages.admin.programs', compact('programs'));
        return view("pages.admin.programs");
    }

    public function donasi(Request $request)
    {
        // 1. Ambil data Program Donasi (untuk dropdown filter)
        $programs = ProgramDonasi::orderBy("title", "asc")->get(["id", "title"]);

        // 2. Mendapatkan parameter dari request
        $search = $request->get("search");
        $status = $request->get("status");
        $programId = $request->get("program_id"); // Parameter BARU
        $perPage = $request->get("perPage", 15);

        $donations = Donation::with("program")
            ->orderBy("created_at", "desc")
            ->when($search, function ($query, $search) {
                // Filter berdasarkan kode donasi, nama donatur, atau email
                $query
                    ->where("donation_code", "like", "%" . $search . "%")
                    ->orWhere("donor_name", "like", "%" . $search . "%")
                    ->orWhere("donor_email", "like", "%" . $search . "%");
            })
            ->when($status, function ($query, $status) {
                if ($status === "failed") {
                    // Mengelompokkan status gagal, expired, dan unpaid
                    $query->whereIn("status", ["failed", "expire", "unpaid"]);
                } else {
                    // Filter status spesifik
                    $query->where("status", $status);
                }
            })
            // 3. Filter BERDASARKAN PROGRAM (BARU)
            ->when($programId, function ($query, $programId) {
                $query->where("program_donasi_id", $programId);
            })
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString(); // Memastikan parameter filter tetap ada di link pagination

        return view("pages.admin.donasi.index", compact("donations", "programs"));
    }

    public function pageStoreManualDonasi(Request $request)
    {
        $programs = ProgramDonasi::where("status", "active")
            ->orderBy("title", "asc")
            ->get(["id", "title"]);

        return view("pages.admin.donasi.store_manual", compact("programs"));
    }

    public function storeManualDonasi(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            "program_donasi_id" => "required|exists:program_donasi,id",
            "donor_name" => "required|string|max:255",
            "donor_phone" => "required|string|max:20",
            "donor_email" => "nullable|email|max:255",
            "amount" => "required|numeric|min:1000",
            "note" => "nullable|string",
            "donation_type" => "nullable|string",
        ]);

        // Generate kode donasi unik
        $donationCode = "MANUAL-" . strtoupper(uniqid());

        // langsung sukses
        $donation = Donation::create([
            "donation_code" => $donationCode,
            "program_donasi_id" => $validated["program_donasi_id"],
            "donor_name" => $validated["donor_name"],
            "donor_phone" => $validated["donor_phone"],
            "donor_email" => $validated["donor_email"],
            "amount" => $validated["amount"],
            "note" => $validated["note"],
            "donation_type" => $validated["donation_type"],
        ]);

        $donation->setStatusSuccess();

        // update collected_amount kita (total duit) di program donasi kite
        $program = ProgramDonasi::find($validated["program_donasi_id"]);
        $program->collected_amount += $validated["amount"];
        $program->save();

        Cache::forget("dashboard_stats");
        Cache::forget("featured_programs");
        Cache::forget("other_programs");
        Cache::forget("donors_count_{$program->id}");
        return redirect()
            ->route("admin.donasi.index")
            ->with("success", "Donasi manual berhasil ditambahkan! Total program bertambah Rp " . number_format($validated["amount"], 0, ",", "."));
    }

    public function pageEditManualDonasi($id)
    {
        $donation = Donation::find($id);
        $programs = ProgramDonasi::where("status", "active")
            ->orderBy("title", "asc")
            ->get(["id", "title"]);
        return view("pages.admin.donasi.edit_manual", compact("donation", "programs"));
    }

    public function updateManualDonasi(Request $request, $id)
    {
        $validated = $request->validate([
            "program_donasi_id" => "required|exists:program_donasi,id",
            "donor_name" => "required|string|max:255",
            "donor_phone" => "required|string|max:20",
            "donor_email" => "nullable|email|max:255",
            "amount" => "required|numeric|min:1000",
            "status" => "required|in:pending,success,failed,expire,unpaid",
            "note" => "nullable|string",
            "created_at" => "required|date",
        ]);

        $donation = Donation::findOrFail($id);

        // --- LOGIC REKALKULASI DANA PROGRAM ---

        // 1. Ambil data lama sebelum update
        $oldProgramId = $donation->program_donasi_id;
        $oldAmount = $donation->amount;
        $oldStatus = $donation->status;

        // 2. Data baru yang akan diupdate
        $newProgramId = $validated["program_donasi_id"];
        $newAmount = $validated["amount"];
        $newStatus = $validated["status"];

        // 3. Update data donasi basic dulu
        $donation->update([
            "program_donasi_id" => $newProgramId,
            "donor_name" => $validated["donor_name"],
            "donor_phone" => $validated["donor_phone"],
            "donor_email" => $validated["donor_email"],
            "amount" => $newAmount, // simpan nominal baru
            "status" => $newStatus, // simpan status baru
            "note" => $validated["note"],
            "created_at" => $validated["created_at"], // Update waktu donasi
        ]);

        // 4. Handle perubahan nominal di Program Donasi
        // Case A: Program Berubah
        if ($oldProgramId != $newProgramId) {
            // Tarik dana dari program lama (jika dulu sukses)
            if ($oldStatus == "success") {
                $oldProgram = ProgramDonasi::find($oldProgramId);
                if ($oldProgram) {
                    $oldProgram->decrement("collected_amount", $oldAmount);
                }
            }
            // Masukkan dana ke program baru (jika status baru sukses)
            if ($newStatus == "success") {
                $newProgram = ProgramDonasi::find($newProgramId);
                if ($newProgram) {
                    $newProgram->increment("collected_amount", $newAmount);
                }
            }
        }
        // Case B: Program Sama, tapi Status atau Nominal berubah
        else {
            $program = ProgramDonasi::find($newProgramId);
            if ($program) {
                // Jika dulu sukses, kurangi dulu nominal LAMA
                if ($oldStatus == "success") {
                    $program->decrement("collected_amount", $oldAmount);
                }

                // Jika sekarang sukses, tambah nominal BARU
                if ($newStatus == "success") {
                    $program->increment("collected_amount", $newAmount);
                }
            }
        }

        // Clear Cache
        Cache::forget("dashboard_stats");
        Cache::forget("featured_programs");
        Cache::forget("other_programs");
        Cache::forget("donors_count_{$newProgramId}");
        if ($oldProgramId != $newProgramId) {
            Cache::forget("donors_count_{$oldProgramId}");
        }

        return redirect()->route("admin.donasi.index")->with("success", "Donasi berhasil diperbarui! Data nominal program telah disesuaikan otomatis.");
    }

    public function exportDonasi(Request $request)
    {
        $search = $request->get("search");
        $status = $request->get("status");
        $programId = $request->get("program_id");

        return response()->streamDownload(function () use ($search, $status, $programId) {
            $handle = fopen("php://output", "w");

            // Tambahkan BOM untuk fix karakter aneh di Excel Windows
            fprintf($handle, chr(0xef) . chr(0xbb) . chr(0xbf));

            // Gunakan SEMICOLON (;) sebagai separator agar otomatis rapi di Excel Indonesia/Eropa
            $separator = ";";

            // Header CSV
            fputcsv($handle, ["No", "Kode Donasi", "Tanggal", "Nama Donatur", "Telepon", "Email", "Program", "Nominal", "Status", "Catatan"], $separator);

            // Query Data (Sama dengan filter index)
            $query = Donation::with("program")
                ->orderBy("created_at", "desc")
                ->when($search, function ($q, $search) {
                    $q->where("donation_code", "like", "%{$search}%")
                        ->orWhere("donor_name", "like", "%{$search}%")
                        ->orWhere("donor_email", "like", "%{$search}%");
                })
                ->when($status, function ($q, $status) {
                    if ($status === "failed") {
                        $q->whereIn("status", ["failed", "expire", "unpaid"]);
                    } else {
                        $q->where("status", $status);
                    }
                })
                ->when($programId, function ($q, $programId) {
                    $q->where("program_donasi_id", $programId);
                });

            // Chunking untuk performa jika data banyak
            $query->chunk(500, function ($donations) use ($handle, $separator) {
                foreach ($donations as $index => $donation) {
                    // Normalisasi HP biar kebaca teks di excel
                    $phone = preg_replace("/[^0-9]/", "", $donation->donor_phone);

                    fputcsv(
                        $handle,
                        [
                            $donation->id,
                            $donation->donation_code,
                            $donation->created_at->format("Y-m-d H:i:s"),
                            $donation->donor_name,
                            "'" . $phone, // Kasih kutip biar excel ga auto format jadi scientific
                            $donation->donor_email,
                            $donation->program ? $donation->program->title : "Program Dihapus",
                            $donation->amount,
                            $donation->status,
                            $donation->note,
                        ],
                        $separator,
                    );
                }
            });

            fclose($handle);
        }, "laporan-donasi-" . date("Y-m-d-His") . ".csv");
    }

    public function exportDonors(Request $request)
    {
        return response()->streamDownload(function () {
            $handle = fopen("php://output", "w");

            // Tambahkan BOM untuk fix karakter aneh di Excel Windows
            fprintf($handle, chr(0xef) . chr(0xbb) . chr(0xbf));

            // Gunakan SEMICOLON (;) sebagai separator agar otomatis rapi di Excel Indonesia/Eropa
            $separator = ";";

            // Header CSV
            fputcsv($handle, ["No", "Nama Donatur", "Telepon", "Email", "Total Donasi (Rp)", "Frekuensi Donasi", "Terakhir Donasi"], $separator);

            // Ambil semua donasi sukses, urutkan dari yang TERBARU
            // PENTING: Order Descending penting untuk logika pemilihan nama
            $donations = Donation::where("status", "success")->orderBy("created_at", "desc")->get();

            $uniqueDonors = [];

            foreach ($donations as $donation) {
                // Normalisasi HP
                $phone = preg_replace("/[^0-9]/", "", $donation->donor_phone);

                // Kunci Unik: HP (Prioritas) > Email
                $key = !empty($phone) ? $phone : $donation->donor_email;

                if (empty($key)) {
                    continue;
                }

                if (!isset($uniqueDonors[$key])) {
                    // Init Data Baru
                    $uniqueDonors[$key] = [
                        "final_name" => $donation->donor_name, // Default: nama dari transaksi terbaru
                        "phone" => $donation->donor_phone,
                        "email" => $donation->donor_email,
                        "total_amount" => 0,
                        "frequency" => 0,
                        "last_donation" => $donation->created_at->format("Y-m-d H:i:s"),
                        "has_real_name" => false, // Flag penanda apakah sudah ketemu nama asli
                    ];
                }

                // --- LOGIKA SMART NAME (NAMA ASLI) ---
                // Cek apakah nama transaksi ini "Hamba Allah" atau bukan (Case Insensitive)
                $isHambaAllah = preg_match("/hamba\s*allah/i", $donation->donor_name) || empty($donation->donor_name) || $donation->donor_name == "-";

                // Jika nama yang sekarang BUKAN Hamba Allah
                if (!$isHambaAllah) {
                    // Dan jika kita BELUM pernah nemu nama asli sebelumnya untuk orang ini
                    if (!$uniqueDonors[$key]["has_real_name"]) {
                        // Kita update namanya jadi nama asli ini
                        $uniqueDonors[$key]["final_name"] = $donation->donor_name;
                        $uniqueDonors[$key]["has_real_name"] = true;
                        // Note: Karena sort DESC, nama asli yang pertama ketemu pasti yang PALING BARU.
                        // Jika ada nama asli lagi di transaksi lama (di bawah), kita abaikan karena sudah marked true.
                    }
                }

                // --- LOGIKA SMART EMAIL (BACKFILL EMAIL) ---
                // Jika email di data gabungan masih kosong, TAPI di transaksi ini ada emailnya, ambil email ini!
                if (empty($uniqueDonors[$key]["email"]) && !empty($donation->donor_email)) {
                    $uniqueDonors[$key]["email"] = $donation->donor_email;
                }

                // Agregasi Angka
                $uniqueDonors[$key]["total_amount"] += $donation->amount;
                $uniqueDonors[$key]["frequency"]++;
            }

            // Tulis baris CSV
            $no = 1;
            foreach ($uniqueDonors as $donor) {
                fputcsv(
                    $handle,
                    [
                        $no++,
                        $donor["final_name"],
                        // Pakai tanda kutip satu di depan agar Excel membaca sebagai Teks, bukan Angka Ilmiah (E+)
                        "'" . preg_replace("/[^0-9]/", "", $donor["phone"]),
                        $donor["email"],
                        $donor["total_amount"],
                        $donor["frequency"],
                        $donor["last_donation"],
                    ],
                    $separator,
                );
            }

            fclose($handle);
        }, "data-donatur-unik-" . date("Y-m-d-His") . ".csv");
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view("pages.admin.users", compact("users"));
    }

    public function toggleAdmin(User $user)
    {
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return back()->with("error", "Anda tidak bisa mengubah peran Anda sendiri!");
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $role = $user->is_admin ? "Admin" : "User";
        return back()->with("success", "Peran {$user->name} berhasil diubah menjadi {$role}.");
    }
}
