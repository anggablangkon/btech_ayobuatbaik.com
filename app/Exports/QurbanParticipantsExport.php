<?php

namespace App\Exports;

use App\Models\QurbanParticipant;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class QurbanParticipantsExport implements FromCollection, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithTitle
{
    private const HEADER_ROW = 8;

    private const LAST_COL = "H";

    public function title(): string
    {
        return "Peserta Qurban";
    }

    public function startCell(): string
    {
        return "A" . self::HEADER_ROW;
    }

    public function collection(): Collection
    {
        return QurbanParticipant::tableSearch()
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            "No",
            "Nama Lengkap",
            "Handphone",
            "Daerah",
            "Kode Kupon",
            "Total Paket",
            "Tgl Ambil",
            "Terdaftar",
        ];
    }

    /**
     * @param  QurbanParticipant  $participant
     * @return array<int, mixed>
     */
    public function map($participant): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $participant->full_name ?? "—",
            $participant->contact_number ?? "—",
            $participant->address ?? "—",
            $participant->coupon_code ?? "—",
            $participant->total_coupon ?? "0",
            $participant->pickup_date ?? "—",
            $participant->created_at?->timezone(config("app.timezone"))->format("Y-m-d H:i") ?? "—",
        ];
    }

    public function columnWidths(): array
    {
        return [
            "A" => 6,
            "B" => 28,
            "C" => 16,
            "D" => 32,
            "E" => 14,
            "F" => 12,
            "G" => 14,
            "H" => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastCol = self::LAST_COL;

                $sheet->setCellValue("A1", "Laporan Peserta Qurban");
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle("A1")->applyFromArray([
                    "font" => [
                        "bold" => true,
                        "size" => 20,
                        "color" => ["rgb" => "14532D"],
                    ],
                    "alignment" => [
                        "horizontal" => Alignment::HORIZONTAL_CENTER,
                        "vertical" => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                $sheet->setCellValue("A2", site_name());
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle("A2")->applyFromArray([
                    "font" => [
                        "size" => 12,
                        "color" => ["rgb" => "166534"],
                        "italic" => true,
                    ],
                    "alignment" => [
                        "horizontal" => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->setCellValue("A3", self::filterDescription());
                $sheet->mergeCells("A3:{$lastCol}3");
                $sheet->getStyle("A3")->applyFromArray([
                    "font" => ["size" => 10, "color" => ["rgb" => "374151"]],
                    "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->setCellValue(
                    "A4",
                    "Diekspor pada: " . now()->timezone(config("app.timezone"))->format("d/m/Y H:i"),
                );
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->getStyle("A4")->applyFromArray([
                    "font" => ["size" => 9, "color" => ["rgb" => "6B7280"]],
                    "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getRowDimension(5)->setRowHeight(6);

                $headerRow = self::HEADER_ROW;
                $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
                    "font" => [
                        "bold" => true,
                        "color" => ["rgb" => "FFFFFF"],
                        "size" => 11,
                    ],
                    "fill" => [
                        "fillType" => Fill::FILL_SOLID,
                        "startColor" => ["rgb" => "14532D"],
                    ],
                    "alignment" => [
                        "horizontal" => Alignment::HORIZONTAL_CENTER,
                        "vertical" => Alignment::VERTICAL_CENTER,
                        "wrapText" => true,
                    ],
                    "borders" => [
                        "allBorders" => [
                            "borderStyle" => Border::BORDER_THIN,
                            "color" => ["rgb" => "0F3D26"],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(28);

                $highestRow = $sheet->getHighestRow();
                if ($highestRow > $headerRow) {
                    $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$highestRow}")->applyFromArray([
                        "alignment" => [
                            "vertical" => Alignment::VERTICAL_TOP,
                            "wrapText" => true,
                        ],
                        "borders" => [
                            "allBorders" => [
                                "borderStyle" => Border::BORDER_THIN,
                                "color" => ["rgb" => "D1D5DB"],
                            ],
                        ],
                    ]);

                    for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                        if (($row - $headerRow) % 2 === 0) {
                            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                                "fill" => [
                                    "fillType" => Fill::FILL_SOLID,
                                    "startColor" => ["rgb" => "F0FDF4"],
                                ],
                            ]);
                        }
                    }
                }

                foreach (["C", "E"] as $col) {
                    $sheet->getStyle("{$col}{$headerRow}:{$col}{$highestRow}")->getNumberFormat()->setFormatCode("@");
                }

                $sheet->freezePane("A" . ($headerRow + 1));
                $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$headerRow}");
            },
        ];
    }

    private static function filterDescription(): string
    {
        $status = request()->get("status");
        $start = request()->get("start_date", Carbon::now()->startOfMonth()->format("Y-m-d"));
        $end = request()->get("end_date", Carbon::now()->endOfMonth()->format("Y-m-d"));

        $statusLabel = $status
            ? match ($status) {
                "pending" => "Pending",
                "taken" => "Diambil",
                "rejected" => "Ditolak",
                "sended" => "Terkirim (WA)",
                default => (string) $status,
            }
            : "Semua status";

        $startFmt = Carbon::parse($start)->format("d/m/Y");
        $endFmt = Carbon::parse($end)->format("d/m/Y");

        return "Filter: {$statusLabel}  |  Periode pendaftaran: {$startFmt} — {$endFmt}";
    }

}
