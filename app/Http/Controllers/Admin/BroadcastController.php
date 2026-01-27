<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBroadcastJob;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BroadcastController extends Controller
{
    public function processQueue()
    {
        try {
            // Jalankan worker sampai kosong, lalu berhenti (agar page tidak loading selamanya)
            Artisan::call('queue:work --stop-when-empty --timeout=60');
            return back()->with('success', 'Antrian berhasil dijalankan manual!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan antrian: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $broadcasts = Broadcast::latest()->paginate(10);
        return view('pages.admin.broadcast.index', compact('broadcasts'));
    }

    public function create()
    {
        $broadcast = null;
        if (request('duplicate_id')) {
            $broadcast = Broadcast::find(request('duplicate_id'));
        }
        return view('pages.admin.broadcast.create', compact('broadcast'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'target' => 'required|in:donors,test,csv_audience',
            'test_number' => 'required_if:target,test',
            'csv_file' => 'required_if:target,csv_audience|file|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
            'target' => $request->target,
            'status' => 'pending',
            'target_data' => [],
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('broadcasts', 'public');
            $data['image_path'] = 'storage/' . $path;
        } elseif ($request->old_image_path) {
            // Reuse old image if duplicating
            $data['image_path'] = $request->old_image_path;
        }

        if ($request->target === 'test') {
            $data['target_data'] = ['test_number' => $request->test_number];
        } elseif ($request->target === 'csv_audience') {
            if ($request->hasFile('csv_file')) {
                $path = $request->file('csv_file')->getRealPath();
                // Baca file CSV
                // Perkiraan: bisa jadi separator koma atau titik koma
                $content = file_get_contents($path);
                
                // Normalisasi baris baru
                $lines = preg_split("/\r\n|\n|\r/", $content);
                $numbers = [];

                foreach ($lines as $line) {
                    // Cari semua yang berbentuk angka di baris ini
                    // Kita ambil semua digit extract, lalu filter length
                    
                    // Simple parser: ambil kolom pertama?
                    // Flexible parser: ambil semua yg terlihat seperti nomor HP
                    // Tapi lebih aman kita asumsikan 1 baris 1 nomor, atau delimiternya jelas.
                    // Mari kita coba explode by comma atau semicolon
                    $cells = preg_split('/[;,]/', $line);
                    
                    foreach ($cells as $cell) {
                        $clean = preg_replace('/[^0-9]/', '', $cell);
                        // Filter minimal 9 digit (misal 081234567)
                        if (strlen($clean) >= 9) {
                            $numbers[] = $clean;
                        }
                    }
                }
                
                $numbers = array_values(array_unique($numbers));
                $data['target_data'] = ['csv_numbers' => $numbers];
                
                if (empty($numbers)) {
                    return back()->withInput()->with('error', 'Tidak ditemukan nomor HP valid dalam file CSV.');
                }
            }
        }

        $broadcast = Broadcast::create($data);

        // Jika Test, kirim langsung (Sync) agar admin langsung tahu hasilnya
        if ($request->target === 'test') {
            SendBroadcastJob::dispatchSync($broadcast);
            return redirect()->route('admin.broadcast.index')
                ->with('success', 'Broadcast Test berhasil dikirim!');
        }

        // Jika Massal, masuk antrian (Queue)
        SendBroadcastJob::dispatch($broadcast);

        return redirect()->route('admin.broadcast.index')
            ->with('success', 'Broadcast sedang diproses di latar belakang.');
    }
}
