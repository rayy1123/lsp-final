<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LspController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Sertifikasi LSP USK
|--------------------------------------------------------------------------
*/

// ROUTE: '/' (Landing Page Utama)
// UNTUK APA: Menampilkan portal publik resmi LSP USK tanpa langsung memaksakan login
Route::get('/', [LspController::class, 'landing'])->name('lsp.landing');

// ROUTE: '/login' & '/register' (Alias Akses Luar)
Route::get('/login', [LspController::class, 'showLogin'])->name('login');
Route::post('/login', [LspController::class, 'login'])->name('login.post');
Route::get('/register', [LspController::class, 'showRegister'])->name('register');
Route::post('/register', [LspController::class, 'register'])->name('register.post');

// ROUTE GROUP: '/lsp' (Prefix Modul Sistem LSP)
// UNTUK APA: Mengelompokkan seluruh endpoint aplikasi LSP di bawah prefix URL '/lsp'
Route::prefix('lsp')->name('lsp.')->group(function () {

    // --- SUB-GROUP: AUTENTIKASI ---
    // UNTUK APA: Menangani antarmuka login, registrasi asesi, pemrosesan verifikasi akun, dan pemutusan sesi logout
    Route::get('/login', [LspController::class, 'showLogin'])->name('login');
    Route::post('/login', [LspController::class, 'login'])->name('login.post');
    Route::get('/register', [LspController::class, 'showRegister'])->name('register');
    Route::post('/register', [LspController::class, 'register'])->name('register.post');
    Route::post('/logout', [LspController::class, 'logout'])->name('logout');

    // --- SUB-GROUP: AREA TERPROTEKSI (Middleware 'auth') ---
    // UNTUK APA: Menjaga akses modul hanya untuk pengguna yang sudah berhasil login (Admin & Asesi)
    Route::middleware('auth')->group(function () {

        // ROUTE: '/lsp/dashboard'
        // UNTUK APA: Menampilkan panel kendali metrik statistik (Admin) atau portal status kelulusan (Asesi/User)
        Route::get('/dashboard', [LspController::class, 'dashboard'])->name('dashboard');

        // ROUTE: Update Master Data & Biodata Akun User / Asesi
        Route::put('/profile', [LspController::class, 'updateProfile'])->name('profile.update');

        // ROUTE: Menu Khusus Hasil & Rekomendasi Asesmen (Asesi)
        Route::get('/assessment-result', [LspController::class, 'assessmentResult'])->name('assessment.result');

        // ROUTE: Pendaftaran Skema oleh Asesi / User Mandiri
        Route::post('/apply-scheme', [LspController::class, 'applyScheme'])->name('apply.scheme');

        // ROUTE: Master Skema Sertifikasi (CRUD)
        // UNTUK APA: Mengelola master data unit kompetensi skema okupasi nasional
        Route::get('/schemes', [LspController::class, 'schemes'])->name('schemes');
        Route::post('/schemes', [LspController::class, 'storeScheme'])->name('schemes.store');
        Route::put('/schemes/{scheme}', [LspController::class, 'updateScheme'])->name('schemes.update');
        Route::delete('/schemes/{scheme}', [LspController::class, 'destroyScheme'])->name('schemes.destroy');

        // ROUTE: Peserta Sertifikasi (CRUD)
        // UNTUK APA: Mengelola pendaftaran asesi, detail kontak, serta status rekomendasi kelulusan
        Route::get('/participants', [LspController::class, 'participants'])->name('participants');
        Route::post('/participants', [LspController::class, 'storeParticipant'])->name('participants.store');
        Route::put('/participants/{participant}', [LspController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{participant}', [LspController::class, 'destroyParticipant'])->name('participants.destroy');

        // ROUTE: Manajemen Unit Tugas / Skema Ujian (Admin) & Pengumpulan Tugas (Asesi)
        Route::post('/tasks', [LspController::class, 'storeTask'])->name('tasks.store');
        Route::delete('/tasks/{task}', [LspController::class, 'destroyTask'])->name('tasks.destroy');
        Route::post('/tasks/{task}/submit', [LspController::class, 'submitTask'])->name('tasks.submit');
        Route::post('/submissions/{submission}/review', [LspController::class, 'reviewSubmission'])->name('submissions.review');

        // ROUTE: '/lsp/report' (Laporan USK & CRUD Konfigurasi Cetak)
        // UNTUK APA: Menampilkan, mencetak, serta mengedit isi teks Berita Acara USK (CRUD Dokumen Cetak)
        Route::get('/report', [LspController::class, 'report'])->name('report');
        Route::post('/report/settings', [LspController::class, 'storeReportSetting'])->name('report.settings.store');
        Route::put('/report/settings/{setting}', [LspController::class, 'updateReportSetting'])->name('report.settings.update');
        Route::delete('/report/settings/{setting}', [LspController::class, 'destroyReportSetting'])->name('report.settings.destroy');
    });
});
