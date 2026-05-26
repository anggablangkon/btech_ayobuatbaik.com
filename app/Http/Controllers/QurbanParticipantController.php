<?php

namespace App\Http\Controllers;

use App\Helpers\Fonnte;
use App\Helpers\FonnteMessageHelper;
use App\Helpers\QrCode;
use App\Http\Requests\StoreQurbanParticipantRequest;
use App\Http\Requests\UpdateQurbanParticipantRequest;
use App\Jobs\SendBroadcastJob;
use App\Jobs\SendQurbanJob;
use App\Models\Broadcast;
use App\Exports\QurbanParticipantsExport;
use App\Models\QurbanCouponScan;
use App\Models\QurbanParticipant;
use App\Models\QurbanParticipantItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeFacade;

class QurbanParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = (int) request()->get("perPage", 10);
        $qurbanParticipants = QurbanParticipant::tableSearch()->with("items")->latest()->paginate($perPage);

        return view("pages.admin.qurban.index", compact("qurbanParticipants"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $qurbanTypes = QurbanParticipantItem::QURBAN_TYPES;
        return view("pages.admin.qurban.create", compact("qurbanTypes"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQurbanParticipantRequest $request)
    {
        $validated = $request->validated();

        $participantData = collect($validated)
            ->except(["qurban_items"])
            ->merge(["coupon_code" => QurbanParticipant::generateCouponCode()])
            ->all();

        $qurbanParticipant = DB::transaction(function () use ($participantData) {
            $qurbanParticipant = QurbanParticipant::create($participantData);

            $qrCodePath = QrCode::generateAndSave($qurbanParticipant->coupon_code, 200, "png", "qrcode/{$qurbanParticipant->id}.webp");
            $qurbanParticipant->update(["image_qr_path" => $qrCodePath]);

            return $qurbanParticipant;
        });

        SendQurbanJob::dispatchSync($qurbanParticipant);
        return redirect()->route("admin.qurban.index")->with("success", "Qurban Participant berhasil ditambahkan");
    }

    /**
     * Halaman publik voucher + QR (payload = coupon_code). Tanpa login.
     */
    public function publicVoucher(string $coupon_code)
    {
        $participant = QurbanParticipant::query()
            ->whereRaw("UPPER(coupon_code) = UPPER(?)", [$coupon_code])
            ->first();

        abort_if(!$participant, 404);

        $participant->load("items");

        $build = $this->buildVoucherCards($participant);
        return view("pages.qurban.voucher", [
            "participant" => $participant,
            "voucher" => $build["voucher"],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(QurbanParticipant $qurban)
    {
        $qurban->load("items");

        return view("pages.admin.qurban.show", ["qurbanParticipant" => $qurban]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QurbanParticipant $qurban)
    {
        $qurban->load("items");

        return view("pages.admin.qurban.edit", ["qurbanParticipant" => $qurban]);
    }

    public function sendQurbanWhatsaap(QurbanParticipant $qurban)
    {
        SendQurbanJob::dispatchSync($qurban);

        return redirect()->route("admin.qurban.index")->with("success", "Qurban Participant berhasil dikirim");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQurbanParticipantRequest $request, QurbanParticipant $qurban)
    {
        $validated = $request->validated();

        $participantData = collect($validated)
            ->except(["qurban_items"])
            ->all();
        $qurban->update($participantData);

        // foreach ($validated['qurban_items'] as $key => $qurbanItem) {
        //     $qurban->items()->updateOrCreate(
        //         ['qurban_type' => $qurbanItem],
        //         ['total_coupon' => (int) $validated['total_coupon'][$key]],
        //     );
        // }

        // $qurban->items()->whereNotIn('qurban_type', $validated['qurban_items'])->delete();

        return redirect()->route("admin.qurban.index")->with("success", "Qurban Participant berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QurbanParticipant $qurban)
    {
        $qurban->delete();

        return redirect()->route("admin.qurban.index")->with("success", "Qurban Participant berhasil dihapus");
    }

    public function export()
    {
        $filename = "peserta-qurban-" . now()->format("Y-m-d-His") . ".xlsx";

        return Excel::download(new QurbanParticipantsExport(), $filename);
    }

    /**
     * Halaman admin: scan kupon + riwayat scan hari ini.
     */
    public function scanCouponPage()
    {
        $todayScans = QurbanCouponScan::query()
            ->with(["participant", "scanner"])
            ->whereDate("created_at", now()->toDateString())
            ->latest()
            ->get();

        return view("pages.admin.qurban.scan", compact("todayScans"));
    }

    /**
     * Verifikasi kupon (manual atau dari QR), simpan riwayat scan, kembalikan detail pemilik (JSON).
     */
    public function submitScanCoupon(Request $request)
    {
        $validated = $request->validate([
            "coupon_code" => ["required", "string", "max:500"],
        ]);

        $code = $this->normalizeCouponCodeInput($validated["coupon_code"]);

        if ($code === "") {
            return response()->json(
                [
                    "ok" => false,
                    "message" => "Kode kupon tidak valid.",
                ],
                422,
            );
        }

        $participant = QurbanParticipant::query()
            ->whereRaw("UPPER(coupon_code) = UPPER(?)", [$code])
            ->with("items")
            ->first();

        if (!$participant) {
            return response()->json(
                [
                    "ok" => false,
                    "message" => "Kupon tidak ditemukan.",
                ],
                422,
            );
        }

        if ($participant->status === "taken") {
            return response()->json(
                [
                    "ok" => false,
                    "message" => "Kupon telah diambil.",
                ],
                400,
            );
        }

        if (in_array($participant->status, ["pending", "sended"])) {
            $participant->update(["status" => "taken"]);
        }

        $scan = QurbanCouponScan::create([
            "qurban_participant_id" => $participant->id,
            "coupon_code" => $participant->coupon_code,
            "scanned_by" => $request->user()->id,
        ]);

        return response()->json([
            "ok" => true,
            "message" => "Kupon berhasil divalidasi.",
            "scan" => [
                "id" => $scan->id,
                "scanned_at" => $scan->created_at->toIso8601String(),
            ],
            "participant" => $this->participantToScanPayload($participant),
        ]);
    }

    protected function normalizeCouponCodeInput(string $raw): string
    {
        $raw = trim($raw);
        if ($raw !== "" && preg_match("#/qurban/voucher/([A-Za-z0-9]+)#", $raw, $m)) {
            return strtoupper($m[1]);
        }

        return strtoupper($raw);
    }

    /**
     * @return array<string, mixed>
     */
    protected function participantToScanPayload(QurbanParticipant $participant): array
    {
        return [
            "id" => $participant->id,
            "full_name" => $participant->full_name,
            "coupon_code" => $participant->coupon_code,
            "nik" => $participant->nik,
            "contact_number" => $participant->contact_number,
            "email" => $participant->email,
            "address" => $participant->address,
            "city" => $participant->city,
            "province" => $participant->province,
            "status" => $participant->status,
            "total_coupon" => $participant->total_coupon,
            "pickup_date" => $participant->pickup_date,
            "pickup_time" => $participant->pickup_time,
            "note" => $participant->note,
            "items" => $participant->items
                ->map(
                    fn(QurbanParticipantItem $item) => [
                        "qurban_type" => $item->qurban_type,
                        "total_coupon" => $item->total_coupon,
                    ],
                )
                ->values()
                ->all(),
            "admin_url" => route("admin.qurban.show", ["qurban" => $participant->id]),
        ];
    }

    /**
     * @return array{voucher: array{serial: string, qr_src: string}}
     */
    protected function buildVoucherCards(QurbanParticipant $participant): array
    {
        $itemCoupons = (int) $participant->items->sum("total_coupon");
        $fieldCoupon = (int) ($participant->total_coupon ?? 0);
        $count = max(1, $itemCoupons > 0 ? $itemCoupons : ($fieldCoupon > 0 ? $fieldCoupon : 1));

        $payload = $participant->coupon_code;
        $generated = QrCodeFacade::format("png")->size(200)->margin(2)->generate($payload);
        $binary = $generated instanceof \Illuminate\Support\HtmlString ? $generated->toHtml() : (string) $generated;
        $voucher = [
            "serial" => $participant->coupon_code,
            "qr_src" => "data:image/png;base64," . base64_encode($binary),
        ];

        return [
            "voucher" => $voucher,
        ];
    }
}
