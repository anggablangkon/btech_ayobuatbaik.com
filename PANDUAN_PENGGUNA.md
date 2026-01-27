# 📘 Panduan Pengguna Aplikasi AyoBuatBaik

Selamat datang di **AyoBuatBaik**, sebuah platform donasi digital lengkap yang dirancang untuk memudahkan yayasan atau lembaga sosial dalam menggalang dana, mengelola donatur, dan menyebarkan kebaikan.

Dokumen ini berisi panduan lengkap mengenai fitur-fitur aplikasi dan cara penggunaannya.

---

## 🌟 Fitur Unggulan untuk Pengguna (Donatur)

Aplikasi ini didesain agar ramah pengguna (User Friendly) dan aksesibel di berbagai perangkat.

### 1. 🏠 Beranda & Navigasi
- **Slider Banner**: Informasi program unggulan yang menarik perhatian.
- **Kategori Donasi**: Memudahkan donatur mencari program donasi (Zakat, Infak, Wakaf, Kemanusiaan, dll).
- **Pencarian Cepat**: Kolom pencarian responsif untuk menemukan program spesifik.

### 2. 💝 Berdonasi
- **Alur Donasi Mudah**: Pilih Program -> Masukkan Nominal -> Bayar.
- **Support Banyak Metode Pembayaran**: Terintegrasi dengan **Midtrans** (GoPay, OVO, ShopeePay, Virtual Account Bank, Alfamart/Indomaret).
- **Notifikasi WA Otomatis**: Donatur langsung mendapatkan pesan WhatsApp berisi instruksi pembayaran dan ucapan terima kasih setelah sukses bayar.

### 3. 🤲 Doa & Dukungan
- Donatur dapat menuliskan doa atau pesan dukungan saat berdonasi.
- Doa-doa ini akan tampil di halaman program, menginspirasi donatur lain (mirip fitur "Orang Baik").

### 4. 📚 Fitur Islami (Kitab & Hikmah)
- **Baca Kitab Online**: Tersedia fitur untuk membaca kitab kuning (contoh: Nashohul Ibad) per bab/maqolah.
- Tampilan teks Arab dan terjemahan yang nyaman dibaca.
- Fitur ini meningkatkan _engagement_ pengguna agar sering kembali membuka aplikasi selain untuk donasi.

### 5. 📱 Instalasi PWA (Aplikasi HP)
- Pengguna tidak perlu download dari PlayStore.
- Cukup buka website di Chrome/Safari -> Klik "Install App" atau "Add to Home Screen".
- Aplikasi akan muncul di layar HP layaknya aplikasi native, ringan, dan cepat.

---

## 🔐 Fitur Panel Admin (Untuk Pengelola)

Halaman khusus untuk pemilik/admin mengelola seluruh aktivitas platform.

### 1. 📊 Dashboard Utama
- **Ringkasan Real-time**: Total donasi terkumpul, jumlah donatur, dan grafik performa bulanan.
- **Status Donasi**: Monitoring donasi Pending, Sukses, dan Gagal.

### 2. 📢 Broadcast WhatsApp (Fitur Premium)
Alat pemasaran yang sangat powerful untuk menjangkau donatur.
- **Kirim Pesan Massal**: Kirim info program baru ke seluruh database donatur sekaligus.
- **Upload Target CSV (Baru)**: Kirim broadcast ke daftar nomor HP kustom (misal: jamaah kajian) dengan mengupload file Excel/CSV.
- **Auto-Format Nomor**: Sistem otomatis memperbaiki format nomor HP (08xx/628xx) menjadi standar internasional.
- **Dukungan Gambar**: Bisa menyertakan poster/banner program (Syarat: Akun Fonnte support file).
- **Sistem Antrian (Queue)**: Pengiriman dilakukan bertahap di background agar server tidak down.

### 3. 💰 Manajemen Transaksi Donasi (Baru)
Pusat data keuangan dan riwayat donasi.
- **Daftar Donasi Lengkap**: Melihat seluruh transaksi masuk baik Online maupun Offline.
- **Input Donasi Manual**: Admin bisa mencatat donasi Tunai/Titipan agar tetap terdata di laporan pembukuan.
- **Filter Canggih**: Cari donasi berdasarkan Nama, Program, Status, atau Tanggal.
- **Ekspor Laporan**:
    - **Ekspor CSV**: Unduh data transaksi lengkap.
    - **Ekspor Donatur Unik**: Fitur pintar untuk merekap siapa saja yang berdonasi (Deduplikasi per Nomor HP). Sistem otomatis mengenali "Nama Asli" donatur dan melengkapi email yang kosong.

### 4. 📝 Manajemen Program Donasi
- **Buat/Edit Program**: Upload foto, tulis deskripsi, set target dana, dan batas waktu.
- **Kategori & Verified**: Kelola kategori donasi dan tanda "Verified" untuk program terpercaya.
- **Kabar Terbaru**: Admin bisa memposting update penyaluran dana ("Kabar") di setiap program untuk transparansi ke donatur.

### 5. ⚙️ Pengaturan Tema & Situs (Dynamic Settings)
Admin dapat mengubah tampilan website tanpa koding:
- **Ganti Warna Utama**: Ubah warna tema aplikasi (misal: dari Hijau ke Biru) langsung dari admin.
- **Upload Identitas**: Ganti Logo, Favicon, Nama Situs, dan Deskripsi SEO.
- **Kontak & Sosmed**: Update nomor WA admin, link Instagram, Facebook, dll.

### 6. 📖 Manajemen Konten Islami (Kitab)
- **Input Kitab Baru**: Tambahkan judul kitab baru.
- **Kelola Bab & Maqolah**: Tulis teks Arab dan terjemahan untuk konten kitab.

### 7. 👥 Manajemen Pengguna
- **Kelola User/Admin**: Tambah admin baru atau kelola akun donatur yang terdaftar.

---

## 🤖 Sistem Otomatis (Auto-Pilot)

Aplikasi ini memiliki "robot" yang bekerja sendiri 24 jam (via Cron Job) untuk membantu Admin:

1.  **Reminder Donasi Pending**:
    - Jika ada donatur checkout tapi lupa bayar, sistem otomatis kirim WA pengingat setiap 10 menit (atau sesuai setting).
2.  **Follow-Up Donatur Lama**:
    - Sistem menyapa donatur yang sudah lama tidak berdonasi (misal: 30 hari) untuk mengajak berdonasi kembali.
    - Script berjalan otomatis setiap pagi jam 09:00.
3.  **Auto-Expire**:
    - Membatalkan tagihan donasi yang sudah kadaluarsa (lebih dari 24 jam) setiap tengah malam agar database bersih.

---

## 🛠️ Catatan Teknis (Penting untuk Pemilik)

1.  **WhatsApp Gateway (Fonnte)**:
    - Aplikasi menggunakan **Fonnte** untuk kirim WA.
    - Pastikan Token Fonnte selalu aktif di menu Pengaturan.
    - *Catatan:* Untuk fitur kirim **Gambar** di Broadcast, wajib menggunakan akun Fonnte berbayar (Premium). Akun Gratis hanya bisa kirim Teks.

2.  **Payment Gateway (Midtrans)**:
    - Menggunakan Midtrans Snap.
    - Pastikan `Client Key` dan `Server Key` Midtrans sudah terpasang benar di file `.env`.

3.  **Hosting & Cron Job**:
    - Karena sistem ini memiliki banyak fitur otomatis, pastikan **Cron Job** di cPanel hosting sudah disetting dengan benar (Mintalah panduan setting Cron Job kepada developer saat serah terima).

---
*Dokumen ini dibuat untuk memudahkan pemilik aplikasi memaksimalkan potensi AyoBuatBaik dalam menebar manfaat.*

<!-- Meta Pixel Code -->
2777910462416668