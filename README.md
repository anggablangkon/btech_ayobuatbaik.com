# AyoBuatBaik - Platform Donasi Digital 💙

![AyoBuatBaik Banner](public/logo-ayobuatbaik.png) <!-- Pastikan ada logo atau ganti dengan text header yang bagus -->

**AyoBuatBaik** adalah platform donasi digital berbasis web (dan PWA) yang memudahkan pengguna untuk menyalurkan kebaikan melalui berbagai program donasi terpercaya. Aplikasi ini dibangun dengan teknologi modern untuk memastikan pengalaman pengguna yang cepat, aman, dan transparan.

## ✨ Fitur Utama

- **🏠 Beranda Interaktif**: Menampilkan program pilihan, kategori donasi, dan banner slider yang informatif.
- **💝 Program Donasi**:
    - Detail program lengkap dengan deskripsi, target dana, dan progress terkini.
    - Indikator **Verified** untuk program terpercaya.
    - Filter kategori untuk memudahkan pencarian program.
- **🤲 Doa & Dukungan**: Fitur komunitas di mana donatur dapat menuliskan doa dan dukungan mereka untuk program yang dibantu.
- **📰 Berita & Artikel**: Update terbaru mengenai penyaluran donasi dan artikel inspiratif.
- **📚 Kitab & Hikmah**: Akses ke sumber literasi islami seperti Kitab Nashohul Ibad (dan fitur Al-Qur'an, Sholawat segera hadir).
- **📱 PWA Support**: Dapat diinstal sebagai aplikasi di smartphone (Android/iOS) untuk akses lebih cepat.
- **💳 Payment Gateway**: Integrasi pembayaran yang aman dan mudah (via Midtrans).
- **📲 Broadcast WA**:
    - Kirim pesan massal ke donatur (dengan fitur antrian/queue).
    - Dukungan pengiriman gambar.
    - Fitur **Edit & Resend** untuk kirim ulang broadcast tanpa upload ulang.
    - Opsi **Proses Antrian Manual** via Admin Panel.
- **🎨 Pengaturan Tema & Situs**:
    - Ubah warna utama (Primary Color) aplikasi secara dinamis via Admin Panel.
    - Upload Logo & Favicon kustom.
    - Pengaturan informasi kontak & sosial media.
- **⏰ WA Followup Reminder**: Kirim reminder otomatis via WhatsApp ke donatur yang sudah X hari tidak donasi.
- **🔍 Pencarian**: Fitur pencarian program donasi yang responsif.

## ⚙️ Konfigurasi Cron Job (cPanel)

Karena menggunakan *Shared Hosting* (cPanel), penjadwalan tugas diatur secara manual (bukan via `schedule:run` tunggal) untuk keandalan maksimal. Berikut adalah **4 Cron Job Wajib** yang harus dipasang:

### 1. Queue Worker (Wajib untuk Broadcast)
Menjalankan antrian pengiriman pesan massal di latar belakang.
- **Jadwal**: `* * * * *` (Setiap Menit)
- **Command**:
  ```bash
  /usr/bin/php /home/uXXXX/domains/domain.com/public_html/artisan queue:work --stop-when-empty >> /dev/null 2>&1
  ```

### 2. Kirim Reminder Donasi Tertunda
Mengirim WA ke donatur yang belum transfer setelah checkout.
- **Jadwal**: `*/10 * * * *` (Setiap 10 Menit)
- **Command**:
  ```bash
  /usr/bin/php /home/uXXXX/domains/domain.com/public_html/artisan donations:send-reminders >> /dev/null 2>&1
  ```

### 3. Maintenance Harian (Auto Expire & Bersih-bersih)
Membatalkan donasi kadaluarsa dan menghapus data sampah.
- **Jadwal**: `0 0 * * *` (Setiap Jam 00:00)
- **Command**:
  ```bash
  /usr/bin/php /home/uXXXX/domains/domain.com/public_html/artisan donations:auto-expire && php artisan donations:delete-failed >> /dev/null 2>&1
  ```

### 4. Followup Donatur Lama
Mengirim sapaan ke donatur yang sudah lama tidak berdonasi.
- **Jadwal**: `0 9 * * *` (Setiap Hari Jam 09:00 Pagi)
- **Command**:
  ```bash
  /usr/bin/php /home/uXXXX/domains/domain.com/public_html/artisan donations:send-followup >> /dev/null 2>&1
  ```

> **Catatan**: Sesuaikan path `/home/uXXXX/...` dengan konfigurasi hosting Anda.

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan stack teknologi modern untuk performa dan skalabilitas:

- **Back-End**: [Laravel 11](https://laravel.com)
    - Authentication (Sanctum)
    - Image Optimization (Intervention Image)
    - Social Auth (Socialite)
    - Payment Gateway (Midtrans)
- **Front-End**:
    - [Blade Templates](https://laravel.com/docs/blade)
    - [Tailwind CSS](https://tailwindcss.com) & Typography
    - [Alpine.js](https://alpinejs.dev) (JavaScript Logic)
    - [Vite](https://vitejs.dev) (Asset Bundling)
    - SweetAlert2 (Notifikasi)
- **Database**: MySQL

## 🚀 Instalasi & Menjalankan Project

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal Anda:

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Langkah-langkah

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/ayobuatbaik.git
   cd ayobuatbaik
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur DB_DATABASE, DB_USERNAME, DB_PASSWORD, dll.

4. **Generate Key & Migrate**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Jalankan Vite (Frontend)**
   ```bash
   npm run dev
   ```

6. **Jalankan Server (Backend)**
   Buka terminal baru dan jalankan:
   ```bash
   php artisan serve
   ```

7. **Akses Aplikasi**
   Buka browser dan kunjungi `http://localhost:8000`.

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan buat *Pull Request* baru atau buka *Issue* jika menemukan bug atau memiliki saran fitur.

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---
*Dibuat dengan ❤️ untuk menebar kebaikan.*

## 📝 Catatan Update (Januari 2026)

### 1. Ekspor Data Donatur (Smart Logic)
- **Ekspor Donatur Unik**: Fitur baru untuk mengunduh daftar donatur yang sudah dideduplikasi berdasarkan Nomor HP.
- **Smart Name**: Sistem otomatis memilih "Nama Asli" dari riwayat donasi jika donatur pernah menggunakan nama asli, meskipun di donasi terakhir menggunakan "Hamba Allah".
- **Smart Email**: Jika email kosong pada donasi terakhir, sistem akan mencari email dari riwayat donasi sebelumnya.
- **Excel Friendly**: Format CSV kini menggunakan pemisah **Titik Koma (;)** dan **BOM Header** agar langsung rapi saat dibuka di Excel (Region Indonesia). Nomor HP juga diformat dengan tanda kutip (`'`) agar tidak berubah menjadi angka ilmiah.

### 2. Fitur Broadcast WhatsApp
- **Target CSV (Jamaah)**: Admin kini bisa mengirim broadcast ke daftar nomor HP kustom dengan mengupload file `.csv` atau `.txt`.
- **Standarisasi Nomor HP**: Sistem "Centralized Phone Normalization" telah diterapkan. Input nomor seperti `0812...`, `812...`, atau `62812...` akan otomatis dikonversi menjadi standar Fonnte (`628...`) di seluruh fitur aplikasi.
