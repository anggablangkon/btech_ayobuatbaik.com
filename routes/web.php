<?php

use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\KategoriDonasiController;
use App\Http\Controllers\Admin\PenggalangDanaController;
use App\Http\Controllers\Admin\ProgramDonasiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CKEditorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KitabChapterController;
use App\Http\Controllers\KitabMaqolahController;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\KitabController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\KitabController as AdminKitabController;
use App\Http\Controllers\QurbanParticipantController;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// PWA Offline Fallback
Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
})->name('offline');

Route::get("/", [HomeController::class, "index"])->name("home");

// Kitab - Multi Kitab Support
Route::get("/kitab", [KitabController::class, "index"])->name("home.kitab.index");
Route::get("/kitab/api/urls", [KitabController::class, "getAllUrls"])->name("home.kitab.urls"); // API for offline download
Route::get("/kitab/{kitabSlug}", [KitabController::class, "show"])->name("home.kitab.show");
Route::get("/kitab/{kitabSlug}/{chapterSlug}", [KitabController::class, "showChapter"])->name("home.kitab.chapter");
Route::get("/kitab/{kitabSlug}/{chapterSlug}/maqolah/{id}", [KitabController::class, "showMaqolah"])->name("home.kitab.maqolah");

Route::get("/programs", [HomeController::class, "programs"])->name("home.program");
Route::get("/program/{slug}", [HomeController::class, "program"])->name("home.program.show");

Route::get("/berita", [HomeController::class, "berita"])->name("home.berita");
Route::get("/berita/{slug}", [HomeController::class, "showBerita"])->name("home.berita.show");

// donasi
Route::get("/donate/status/{code}", [DonasiController::class, "showStatus"])->name("donation.status");

// Kupon qurban (publik — tanpa login)
Route::get('/qurban/voucher/{coupon_code}', [QurbanParticipantController::class, 'publicVoucher'])
    ->name('qurban.voucher.public')
    ->where('coupon_code', '[A-Za-z0-9]+');

Route::get("/search", [HomeController::class, "search"])->name("home.search");

Route::get("oauth/google", [OauthController::class, "redirectToProvider"])->name("oauth.google");
Route::get("oauth/google/callback", [OauthController::class, "handleProviderCallback"])->name("oauth.google.callback");

Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

Route::middleware(["auth"])->group(function () {
    Route::get("/profile", [UserProfileController::class, "index"])->name("profile");
    Route::post("/profile/update", [UserProfileController::class, "updateProfile"])->name("profile.update");
    Route::post("/profile/password", [UserProfileController::class, "updatePassword"])->name("profile.password");
});

// Admin Routes dengan middleware
Route::middleware(["auth", "isAdmin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard", [AdminController::class, "dashboard"])->name("dashboard");
        Route::get("donasi", [AdminController::class, "donasi"])->name("donasi.index");
        Route::get("donasi/createManual", [AdminController::class, "pageStoreManualDonasi"])->name("donasi.createManual");
        Route::post("donasi/storeManual", [AdminController::class, "storeManualDonasi"])->name("donasi.storeManual");
        Route::get("donasi/{id}/editManual", [AdminController::class, "pageEditManualDonasi"])->name("donasi.editManual");
        Route::put("donasi/{id}", [AdminController::class, "updateManualDonasi"])->name("donasi.updateManual");
        Route::get("donasi/export", [AdminController::class, "exportDonasi"])->name("donasi.export");
        Route::get("donasi/export-donors", [AdminController::class, "exportDonors"])->name("donasi.export_donors");
        Route::get("/users", [AdminController::class, "users"])->name("users");
        Route::post("/users/{user}/toggle-admin", [AdminController::class, "toggleAdmin"])->name("users.toggle-admin");

        // CRUD Program Donasi
        Route::resource("programs", ProgramDonasiController::class)->names("programs");
        // routes/web.php
        Route::post("ckeditor/upload", [CKEditorController::class, "upload"])->name("ckeditor.upload");

        Route::resource("penggalang-dana", PenggalangDanaController::class)->names("penggalang_dana");
        Route::resource("kategori-donasi", KategoriDonasiController::class)->names("kategori_donasi");
        Route::resource("sliders", SliderController::class)->names("sliders");
        Route::resource("berita", BeritaController::class)
            ->parameters(["berita" => "berita"])
            ->names("berita");
        Route::post("/sliders/reorder", [SliderController::class, "reorder"])->name("sliders.reorder");

        Route::resource("kitab", AdminKitabController::class)->names("kitab");
        Route::resource("kitab-chapter", KitabChapterController::class)->names("kitab_chapter");
        Route::resource("kitab-maqolah", KitabMaqolahController::class)->names("kitab_maqolah");

        // Broadcast WA
        Route::resource('broadcast', BroadcastController::class);
        Route::post('broadcast/process-queue', [BroadcastController::class, 'processQueue'])->name('broadcast.process_queue');

        // Qurban (static paths must be registered before resource {qurban})
        Route::get('qurban/export', [QurbanParticipantController::class, 'export'])->name('qurban.export');
        Route::get('qurban/scan-coupon', [QurbanParticipantController::class, 'scanCouponPage'])->name('qurban.scan_coupon');
        Route::post('qurban/scan-coupon/submit', [QurbanParticipantController::class, 'submitScanCoupon'])->name('qurban.scan_coupon.submit');
        Route::resource('qurban', QurbanParticipantController::class);
        Route::post('qurban/{qurban}/send-whatsapp', [QurbanParticipantController::class, 'sendQurbanWhatsaap'])->name('qurban.send_whatsapp');
        // Site Settings
        Route::get("settings", [SettingsController::class, "index"])->name("settings.index");
        Route::post("settings", [SettingsController::class, "update"])->name("settings.update");
    });

// Route::get('/programs', [ProgramController::class, 'index'])->name('program.index');
// Route::get('/single-program', [ProgramController::class, 'show'])->name('program.show');
