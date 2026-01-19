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
- **📲 WA Followup Reminder**: Kirim reminder otomatis via WhatsApp ke donatur yang sudah X hari tidak donasi.
- **🔍 Pencarian**: Fitur pencarian program donasi yang responsif.

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
