# Task: Tampilkan Nilai Bersih Donasi (`net amount`) per Transaksi

## Context
- Project ini adalah Laravel app untuk donasi.
- Integrasi pembayaran menggunakan Midtrans Snap.
- Saat ini data transaksi di database hanya menyimpan nominal kotor melalui kolom `donations.amount`.
- Admin/user belum bisa melihat hasil bersih yang benar-benar diterima setelah dipotong fee Midtrans.

## Problem
Saat ini UI hanya menampilkan nominal donasi kotor (`gross amount`). Ini berpotensi membingungkan karena angka tersebut bukan nilai akhir yang diterima.

Target perubahan:
- Admin bisa melihat `gross`, `fee`, dan `net amount` per transaksi.
- Perhitungan tidak boleh hardcoded jika data fee riil bisa didapat dari report/reconciliation Midtrans.
- Data lama tetap aman dan backward-compatible.
- Semua transaksi lama non-manual juga harus ikut dihitung/backfill, bukan hanya transaksi baru.

## Important Decision
Jangan pakai rumus fee statis sebagai source of truth untuk laporan final.

Alasan:
- Fee Midtrans bisa berbeda tergantung payment method.
- Bisa ada MDR, VAT fee, promo, atau perubahan pricing.
- Jika hardcoded, angka dashboard bisa beda dengan settlement/payout Midtrans.

Pendekatan yang disarankan:
- Simpan `gross`, `midtrans_fee_amount`, dan `net_amount` di tabel `donations`.
- Gunakan data resmi Midtrans sebagai source of truth untuk fee/net.
- Jika untuk fase awal perlu fallback, tandai sebagai `estimated`, jangan dianggap final.

## Simple Explanation
Versi sederhananya seperti ini:

- `Gross` = nominal donasi yang dibayar donatur.
- `Fee` = potongan dari Midtrans, nilainya bisa beda tergantung metode bayar.
- `Net` = uang bersih yang benar-benar diterima setelah dipotong fee.

Kenapa tidak bisa dihitung dengan satu rumus?
- Karena transaksi QRIS, bank transfer, e-wallet, dan channel lain bisa punya fee berbeda.
- Karena ada kemungkinan MDR, transaction fee, VAT, atau aturan fee lain yang berubah.

Jadi strategi yang benar:
- simpan dulu data transaksi dan metode bayarnya
- ambil angka fee/net final dari data resmi Midtrans
- tampilkan hasilnya di dashboard/admin

## Mental Model
Project ini nantinya membagi transaksi jadi 2 kelompok:

### A. Transaksi Midtrans
Contoh:
- QRIS
- Bank Transfer / Virtual Account
- E-wallet
- Convenience store

Untuk transaksi ini:
- boleh punya fee Midtrans
- perlu dihitung `net amount`
- perlu ikut reconciliation dengan data/report Midtrans

### B. Transaksi Manual / Non-Midtrans
Contoh:
- cash
- transfer manual yang dicatat admin

Untuk transaksi ini:
- tidak punya fee Midtrans
- tidak perlu dihitung `net amount` versi Midtrans
- tidak ikut reconciliation report Midtrans

## How We Know the Payment Method
Untuk transaksi Midtrans, metode pembayaran harus diambil dari data Midtrans, bukan ditebak dari nominal.

Data yang dipakai:
- `payment_type` dari webhook/notification Midtrans
- detail channel lain jika ada, misalnya bank VA
- `order_id` untuk pencocokan dengan data lokal

Contoh interpretasi:
- `payment_type = qris` -> transaksi QRIS
- `payment_type = bank_transfer` -> transaksi VA/bank transfer
- `payment_type = cstore` -> Indomaret/Alfamart
- `payment_type = echannel` -> Mandiri bill payment

Kalau transaksi lama belum menyimpan `payment_type`, maka ambil dari:
- historical report Midtrans
- atau status/query transaksi Midtrans berdasarkan `order_id`

## New vs Old Transaction Strategy

### Transaksi Baru
Flow untuk transaksi baru:
1. User bayar via Midtrans.
2. Aplikasi menerima webhook Midtrans.
3. Simpan:
   - `gross_amount`
   - `payment_type`
   - `settlement_time`
   - `midtrans_payload`
4. Jika fee final belum tersedia di webhook, jangan isi angka palsu.
5. Saat payout/report tersedia, lakukan reconciliation dan isi:
   - `midtrans_fee_amount`
   - `net_amount`
   - `net_amount_source`

### Transaksi Lama
Flow untuk transaksi lama:
1. Isi dulu `gross_amount` dari `amount` jika masih kosong.
2. Pisahkan transaksi manual vs non-manual.
3. Untuk semua transaksi lama non-manual:
   - cari data pasangan di report Midtrans dengan `donation_code = order_id`
   - ambil `payment_type`, fee, dan net jika tersedia
4. Jika match berhasil:
   - isi nilai final
5. Jika belum match:
   - tandai `pending_reconciliation`

## Business Rule Summary
- Semua transaksi lama non-manual harus ikut dihitung.
- Semua transaksi baru Midtrans harus otomatis disiapkan untuk dihitung.
- Transaksi manual/cash tidak ikut fee/net Midtrans.
- Summary `Total Fee Midtrans` dan `Total Net Midtrans` hanya menghitung transaksi Midtrans.
- Dashboard tidak boleh menampilkan angka `0` palsu untuk fee/net yang sebenarnya belum diketahui.

## Existing Files to Review
- `app/Models/Donation.php`
- `app/Http/Controllers/DonasiController.php`
- `resources/views/pages/admin/donasi/index.blade.php`
- `routes/api.php`
- `config/services.php`

## Current Findings
- `Donation` model belum punya field fee/net.
- `DonasiController@store` hanya mengirim `gross_amount` ke Midtrans.
- `DonasiController@notification` hanya update status transaksi.
- Halaman admin donasi hanya menampilkan satu angka nominal.
- Perlu plan eksplisit untuk merapikan transaksi lama (`existing donations`) agar data lama tidak menggantung setelah fitur baru ditambahkan.
- Ada transaksi manual dari admin (`storeManualDonasi`) dengan kode prefix `MANUAL-` dan `donation_type` seperti `manual` atau `cash`.
- Transaksi manual bukan transaksi Midtrans, jadi tidak boleh ikut reconciliation fee/net Midtrans.

## Implementation Plan

### 1. Database
Buat migration baru untuk menambah kolom berikut ke tabel `donations`:
- `gross_amount` nullable unsigned big integer
- `midtrans_fee_amount` nullable unsigned big integer
- `net_amount` nullable unsigned big integer
- `payment_type` nullable string
- `settlement_time` nullable timestamp
- `midtrans_payload` nullable json
- `net_amount_source` nullable string

Catatan:
- Untuk backward compatibility, isi `gross_amount` dari `amount` pada existing rows bila perlu.
- Jika ingin lebih minimal, `amount` tetap dipertahankan sebagai nominal gross lama, lalu `gross_amount` bisa di-skip. Tapi opsi paling jelas adalah punya field eksplisit.

### 1.1 Existing Data Cleanup Plan
Wajib ada langkah khusus untuk merapikan transaksi yang sudah ada sebelum fitur ini hidup penuh.

Minimum cleanup yang harus dilakukan:
- Untuk semua row lama, set `gross_amount = amount` jika `gross_amount` masih `null`.
- Untuk semua transaksi lama non-manual:
  - wajib masuk proses backfill fee/net amount
  - cocokkan ke data Midtrans berdasarkan `donation_code`
  - jika ditemukan di report/reconciliation, isi nilai final
  - jika belum ditemukan, baru tandai sebagai pending
- Untuk transaksi lama yang belum punya data fee/net dan belum ketemu di source Midtrans:
  - set `midtrans_fee_amount = null`
  - set `net_amount = null`
  - set `net_amount_source = 'pending_reconciliation'`
- Untuk transaksi lama dengan status selain transaksi sukses (`success`, `settlement`, `capture` jika masih dipakai mapping internal), jangan paksa isi `net_amount`.
- Untuk transaksi manual / cash:
  - jangan tandai sebagai `pending_reconciliation`
  - jangan ikut job import report Midtrans
  - gunakan state khusus seperti `not_applicable_manual` atau nilai serupa pada `net_amount_source`
- Pastikan UI membedakan antara:
  - `net belum dihitung`
  - `net tidak applicable`
  - `net sudah final dari Midtrans`

Kalau memungkinkan, buat backfill command atau one-time migration script untuk cleanup data lama.

### 2. Model Update
Update `app/Models/Donation.php`:
- Tambahkan field baru ke `$fillable` jika memang perlu mass assignment.
- Tambahkan casts:
  - `gross_amount` => `integer`
  - `midtrans_fee_amount` => `integer`
  - `net_amount` => `integer`
  - `settlement_time` => `datetime`
  - `midtrans_payload` => `array`
- Pertimbangkan accessor helper:
  - `display_gross_amount`
  - `display_fee_amount`
  - `display_net_amount`

### 3. Midtrans Notification Handling
Update `app/Http/Controllers/DonasiController.php`:
- Saat callback Midtrans diterima, simpan payload mentah ke `midtrans_payload`.
- Simpan `payment_type` dan `settlement_time` jika tersedia di payload.
- Jangan langsung asumsi fee dari callback jika callback tidak menyertakan fee final.

Catatan penting:
- Jika payload callback tidak memiliki fee/net final, jangan isi `midtrans_fee_amount` dan `net_amount` dengan angka palsu.
- Set `net_amount_source = 'pending_reconciliation'` atau label serupa untuk menandai data belum final.

### 4. Reconciliation Strategy
Buat mekanisme sinkronisasi fee/net dari data resmi Midtrans.

Minimal salah satu dari dua pendekatan ini:

#### Opsi A - Recommended
Import dari Settlement Report / Payout Report Midtrans.
- Cocokkan berdasarkan `order_id` dengan `donation_code`.
- Isi:
  - `midtrans_fee_amount`
  - `net_amount`
  - `net_amount_source = 'midtrans_report'`

#### Opsi B
Sinkronisasi dari endpoint/API resmi Midtrans yang memang menyediakan detail fee/net jika tersedia di akun/produk yang dipakai.

Jika Opsi A dipilih, implementasi awal bisa berupa:
- Artisan command untuk import CSV/XLSX report
- atau service class khusus reconciliation

Untuk transaksi yang sudah ada:
- Jalankan reconciliation ke data lama lebih dulu, bukan hanya transaksi baru.
- Matching utama tetap pakai `donation_code` <-> `order_id`.
- Hasil reconciliation harus bisa di-run berulang dengan aman (`idempotent`), supaya kalau report diimpor ulang tidak membuat data kacau.
- Exclude transaksi manual dari reconciliation. Gunakan rule yang eksplisit, misalnya:
  - `donation_code` prefix `MANUAL-`, atau
  - `donation_type` in `manual`, `cash`, atau
  - field source/channel khusus jika nanti ditambahkan

Target hasil reconciliation historical data:
- Semua transaksi lama non-manual yang match dengan report Midtrans harus punya:
  - `gross_amount`
  - `midtrans_fee_amount`
  - `net_amount`
  - `net_amount_source = 'midtrans_report'` atau source final lain yang dipakai
- Hanya transaksi yang benar-benar belum berhasil dimatch yang boleh tersisa di state `pending_reconciliation`

### 4.1 Manual Donation Rule
Transaksi manual harus diperlakukan terpisah dari transaksi Midtrans.

Rule bisnis:
- Donasi manual/cash tetap valid sebagai donasi dan tetap boleh masuk ke `collected_amount` program jika statusnya `success`.
- Tetapi donasi manual/cash tidak dihitung sebagai transaksi yang perlu:
  - fee Midtrans
  - `net amount` hasil settlement Midtrans
  - reconciliation/import report Midtrans
  - summary total fee Midtrans

Implikasi UI:
- Untuk transaksi manual, jangan tampilkan `Menunggu sinkronisasi fee`.
- Tampilkan label yang jujur seperti `Manual / Non-Midtrans` atau `Tidak kena fee Midtrans`.
- Jika ada summary khusus `Total Net Midtrans`, exclude transaksi manual dari agregasi.

### 5. Admin UI
Update `resources/views/pages/admin/donasi/index.blade.php` agar admin mudah membaca hasil bersih.

Ubah tampilan nominal menjadi:
- `Gross`
- `Fee`
- `Net`

Contoh presentasi:

```text
Gross : Rp 100.000
Fee   : Rp 4.440
Net   : Rp 95.560
```

UX rules:
- `Net` harus paling menonjol secara visual karena itu angka utama.
- Jika `net_amount` belum tersedia, tampilkan:
  - `Menunggu sinkronisasi fee`
  - atau badge `Estimated` / `Pending reconciliation`
- Jangan tampilkan `Rp 0` kalau data fee/net memang belum ada.

### 6. Optional Enhancement
Tambahkan summary di halaman admin:
- Total Gross
- Total Fee
- Total Net

Summary harus mengikuti filter aktif:
- search
- program
- status
- pagination tidak mempengaruhi aggregate total jika summary dimaksudkan untuk seluruh hasil filter

## Acceptance Criteria
- Ada struktur data untuk menyimpan fee dan nominal bersih per transaksi.
- Data existing tidak rusak.
- Data existing non-manual sudah diproses untuk dihitung fee/net-nya.
- Data existing yang berhasil dimatch dengan data Midtrans sudah memiliki nilai final yang valid.
- Hanya data existing non-manual yang belum berhasil dimatch yang boleh berada di state `pending_reconciliation`.
- Admin bisa melihat `gross`, `fee`, dan `net` pada daftar transaksi.
- Jika net belum diketahui, UI menampilkan status yang jujur, bukan angka palsu.
- Source of truth net amount berasal dari Midtrans report/reconciliation, bukan hardcoded formula.
- Status transaksi existing tetap berjalan seperti sebelumnya.
- Transaksi manual/cash tidak ikut perhitungan fee/net Midtrans dan tidak ikut reconciliation Midtrans.

## Non-Goals
- Jangan refactor besar flow pembayaran jika tidak dibutuhkan.
- Jangan ubah seluruh naming/domain model kecuali memang perlu untuk support fee/net.
- Jangan menambah dependency baru tanpa justifikasi jelas.

## Suggested Execution Order
1. Tambah migration kolom baru.
2. Rapikan data lama dengan backfill `gross_amount`.
3. Jalankan backfill historical fee/net untuk semua transaksi lama non-manual dari source Midtrans.
4. Tandai hanya sisa transaksi yang belum match sebagai `pending_reconciliation`.
5. Update model `Donation`.
6. Simpan metadata Midtrans tambahan di callback.
7. Update UI admin untuk menampilkan gross/fee/net dengan fallback state.
8. Tambah mekanisme reconciliation dari report Midtrans untuk transaksi berjalan.
9. Verifikasi data transaksi lama dan baru.

## Testing Checklist
- Buat transaksi baru dan pastikan status flow existing tetap normal.
- Simulasikan callback `pending`, `settlement`, `expire`, `deny`.
- Pastikan halaman admin tetap render untuk transaksi lama yang belum punya `net_amount`.
- Pastikan transaksi lama setelah backfill punya `gross_amount` yang terisi dan `net_amount_source` yang konsisten.
- Pastikan transaksi lama non-manual yang ada di report Midtrans benar-benar terisi `fee` dan `net`.
- Pastikan hanya transaksi lama non-manual yang gagal dimatch yang tersisa sebagai `pending_reconciliation`.
- Pastikan transaksi manual dengan `donation_code` `MANUAL-*` atau `donation_type` `manual/cash` tidak ikut job reconciliation.
- Pastikan transaksi manual tidak menampilkan status `pending_reconciliation`.
- Uji tampilan ketika:
  - fee/net tersedia
  - fee/net belum tersedia
  - transaksi manual / non-Midtrans
- Uji kecocokan reconciliation berdasarkan `donation_code` vs `order_id`.

## Notes for Next AI Model
- Fokus ke solusi production-ready, bukan sekadar estimasi angka.
- Jika perlu fase cepat, boleh tambah fallback `estimated` net amount, tapi wajib ditandai jelas dan jangan dijadikan nilai final.
- Perubahan utama kemungkinan ada di:
  - `database/migrations/...`
  - `app/Models/Donation.php`
  - `app/Http/Controllers/DonasiController.php`
  - `resources/views/pages/admin/donasi/index.blade.php`
