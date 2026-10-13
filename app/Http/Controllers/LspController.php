<?php

namespace App\Http\Controllers;

use App\Models\CertificationScheme;
use App\Models\LspReportSetting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Controller: LspController
 * Pengendali utama seluruh logika modul sertifikasi LSP USK:
 * Landing page, otentikasi admin, dashboard metrik, CRUD skema, CRUD peserta, dan CRUD konfigurasi cetak Berita Acara USK.
 */
class LspController extends Controller
{
    /**
     * FUNGSI: landing()
     * UNTUK APA: Menampilkan halaman depan (Landing Page) resmi LSP untuk publik/asesi sebelum masuk ke sistem inti.
     * ALUR KERJA: Mengambil ringkasan skema aktif dan kalkulasi total peserta/kelulusan untuk ditampilkan pada kartu statistik publik.
     */
    public function landing()
    {
        $schemes = collect();
        $totalSchemes = 0;
        $totalParticipants = 0;
        $totalKompeten = 0;

        if (Schema::hasTable('certification_schemes')) {
            $schemes = CertificationScheme::withCount('participants')->take(6)->get();
            $totalSchemes = CertificationScheme::count();
        }

        if (Schema::hasTable('participants')) {
            $totalParticipants = Participant::count();
            $totalKompeten = Participant::where('status', 'like', '%Kompeten%')->count();
        }

        return view('lsp.landing', compact('schemes', 'totalSchemes', 'totalParticipants', 'totalKompeten'));
    }

    /**
     * FUNGSI: showLogin()
     * UNTUK APA: Menampilkan formulir login admin LSP.
     * ALUR KERJA: Memeriksa session aktif. Jika admin sudah terotentikasi, otomatis dialihkan ke dashboard. Jika belum, tampilkan view login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('lsp.dashboard');
        }
        return view('lsp.login');
    }

    /**
     * FUNGSI: login()
     * UNTUK APA: Memvalidasi kredensial login admin dan membuat sesi otentikasi baru.
     * ALUR KERJA: Validasi input email/username dan password, cocokkan kredensial, regenerasi session, dan auto-fallback admin.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$field => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended(route('lsp.dashboard'));
        }

        if (User::count() === 0 && $request->email === 'admin' && $request->password === 'admin123') {
            $user = User::create([
                'name' => 'admin',
                'email' => 'admin@lsp.id',
                'password' => Hash::make('admin123'),
            ]);
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('lsp.dashboard'));
        }

        return back()->withErrors([
            'login' => 'Username atau password tidak sesuai.',
        ]);
    }

    /**
     * FUNGSI: logout()
     * UNTUK APA: Mengakhiri sesi pengguna admin yang sedang aktif secara aman.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('lsp.landing');
    }

    /**
     * FUNGSI: dashboard()
     * UNTUK APA: Menyajikan ringkasan metrik statistik asesmen dan 5 riwayat pendaftaran asesi terbaru.
     */
    public function dashboard()
    {
        $totalParticipants = Participant::count();
        $totalSchemes = CertificationScheme::count();
        $totalKompeten = Participant::where('status', 'like', '%Kompeten%')->count();
        $totalBelum = Participant::where('status', 'not like', '%Kompeten%')->count();
        $recentParticipants = Participant::with('scheme')->latest()->take(5)->get();

        return view('lsp.dashboard', compact(
            'totalParticipants',
            'totalSchemes',
            'totalKompeten',
            'totalBelum',
            'recentParticipants'
        ));
    }

    // ==========================================
    // MODUL SKEMA SERTIFIKASI
    // ==========================================

    /**
     * FUNGSI: schemes()
     * UNTUK APA: Menampilkan tabel master data skema sertifikasi beserta fitur pencarian dan relasi peserta.
     */
    public function schemes(Request $request)
    {
        $search = $request->input('search');
        $query = CertificationScheme::withCount('participants')->with('participants')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('scheme_code', 'like', "%{$search}%")
                  ->orWhere('scheme_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('participants', function($pq) use ($search) {
                      $pq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        $schemes = $query->get();
        return view('lsp.schemes', compact('schemes', 'search'));
    }

    /**
     * FUNGSI: storeScheme()
     * UNTUK APA: Menyimpan data master skema sertifikasi baru ke tabel certification_schemes.
     */
    public function storeScheme(Request $request)
    {
        $validated = $request->validate([
            'scheme_code' => 'required|string|unique:certification_schemes,scheme_code',
            'scheme_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        CertificationScheme::create($validated);
        return back()->with('success', 'Skema sertifikasi berhasil ditambahkan.');
    }

    /**
     * FUNGSI: updateScheme()
     * UNTUK APA: Memperbarui informasi skema sertifikasi yang sudah ada.
     */
    public function updateScheme(Request $request, CertificationScheme $scheme)
    {
        $validated = $request->validate([
            'scheme_code' => 'required|string|unique:certification_schemes,scheme_code,' . $scheme->id,
            'scheme_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $scheme->update($validated);
        return back()->with('success', 'Skema sertifikasi berhasil diperbarui.');
    }

    /**
     * FUNGSI: destroyScheme()
     * UNTUK APA: Menghapus data skema sertifikasi dari database.
     */
    public function destroyScheme(CertificationScheme $scheme)
    {
        $scheme->delete();
        return back()->with('success', 'Skema sertifikasi berhasil dihapus.');
    }

    // ==========================================
    // MODUL PESERTA SERTIFIKASI (ASESI)
    // ==========================================

    /**
     * FUNGSI: participants()
     * UNTUK APA: Menampilkan daftar seluruh asesi terdaftar dengan paginasi dan pencarian.
     */
    public function participants(Request $request)
    {
        $search = $request->input('search');
        $query = Participant::with('scheme')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('scheme', function($sq) use ($search) {
                      $sq->where('scheme_name', 'like', "%{$search}%")
                         ->orWhere('scheme_code', 'like', "%{$search}%");
                  });
            });
        }

        $participants = $query->paginate(10)->withQueryString();
        $schemes = CertificationScheme::orderBy('scheme_name')->get();

        return view('lsp.participants', compact('participants', 'schemes', 'search'));
    }

    /**
     * FUNGSI: storeParticipant()
     * UNTUK APA: Mendaftarkan data peserta asesmen baru ke dalam skema tertentu.
     */
    public function storeParticipant(Request $request)
    {
        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'registration_number' => 'required|string|unique:participants,registration_number',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        Participant::create($validated);
        return back()->with('success', 'Peserta berhasil didaftarkan.');
    }

    /**
     * FUNGSI: updateParticipant()
     * UNTUK APA: Mengubah data profil asesi maupun memperbarui status hasil uji kompetensi.
     */
    public function updateParticipant(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'registration_number' => 'required|string|unique:participants,registration_number,' . $participant->id,
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $participant->update($validated);
        return back()->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * FUNGSI: destroyParticipant()
     * UNTUK APA: Menghapus data peserta/asesi tertentu dari sistem.
     */
    public function destroyParticipant(Participant $participant)
    {
        $participant->delete();
        return back()->with('success', 'Data peserta berhasil dihapus.');
    }

    // ==========================================
    // MODUL LAPORAN RESMI USK & CRUD KONTEN CETAK
    // ==========================================

    /**
     * FUNGSI: report()
     * UNTUK APA: Menghasilkan lembar berita acara & rekapitulasi nilai Uji Sertifikasi Kompetensi (USK) siap cetak/ekspor PDF, menggunakan konfigurasi dinamis yang dapat diubah melalui CRUD.
     * ALUR KERJA:
     * 1. Mengambil konfigurasi template cetak aktif atau yang dipilih via setting_id.
     * 2. Mengambil data peserta terfilter per skema atau seluruh skema.
     * 3. Menghitung persentase kelulusan (Kompeten vs Belum Kompeten).
     * 4. Mengagregasi data ringkasan performa per skema.
     */
    public function report(Request $request)
    {
        $schemeId = $request->input('scheme_id');
        $settingId = $request->input('setting_id');

        // Ambil template laporan yang dipilih atau template aktif default
        if ($settingId) {
            $reportSetting = LspReportSetting::find($settingId) ?? LspReportSetting::getActiveOrDefault();
        } else {
            $reportSetting = LspReportSetting::getActiveOrDefault();
        }

        // Daftar semua template konfigurasi laporan yang tersedia (untuk fitur switch CRUD)
        $reportSettingsList = LspReportSetting::orderBy('template_name')->get();

        // Query peserta
        $participantsQuery = Participant::with('scheme')->orderBy('registration_number', 'asc');
        if ($schemeId) {
            $participantsQuery->where('scheme_id', $schemeId);
        }
        $participants = $participantsQuery->get();

        // Kalkulasi statistik
        $totalParticipants = $participants->count();
        $totalKompeten = $participants->where('status', 'Kompensasi / Kompeten')->count()
            + $participants->filter(fn($p) => str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum'))->count();
        $totalBelum = $totalParticipants - $totalKompeten;
        $passRate = $totalParticipants > 0 ? round(($totalKompeten / $totalParticipants) * 100, 1) : 0;

        // Agregasi per skema
        $schemes = CertificationScheme::with(['participants'])->get()->map(function ($s) {
            $total = $s->participants->count();
            $kompeten = $s->participants->filter(fn($p) => str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum'))->count();
            $belum = $total - $kompeten;
            $rate = $total > 0 ? round(($kompeten / $total) * 100, 1) : 0;

            return (object) [
                'id' => $s->id,
                'scheme_code' => $s->scheme_code,
                'scheme_name' => $s->scheme_name,
                'total' => $total,
                'kompeten' => $kompeten,
                'belum' => $belum,
                'rate' => $rate,
            ];
        });

        $selectedScheme = $schemeId ? CertificationScheme::find($schemeId) : null;

        return view('lsp.report', compact(
            'participants',
            'totalParticipants',
            'totalKompeten',
            'totalBelum',
            'passRate',
            'schemes',
            'selectedScheme',
            'schemeId',
            'reportSetting',
            'reportSettingsList'
        ));
    }

    /**
     * FUNGSI: storeReportSetting()
     * UNTUK APA: Menyimpan template konfigurasi konten cetak laporan USK yang baru (Create).
     * ALUR KERJA: Validasi form isian kop surat, nomor dokumen, tanggal, TUK, dan penandatangan, lalu simpan ke tabel lsp_report_settings.
     */
    public function storeReportSetting(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'tuk_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_info' => 'required|string',
            'document_title' => 'required|string|max:255',
            'document_number' => 'required|string|max:255',
            'tuk_location' => 'required|string|max:255',
            'assessment_date' => 'nullable|date',
            'assessment_method' => 'required|string|max:255',
            'closing_statement' => 'required|string',
            'signer1_role' => 'required|string|max:255',
            'signer1_name' => 'required|string|max:255',
            'signer1_reg_number' => 'nullable|string|max:255',
            'signer2_city' => 'required|string|max:255',
            'signer2_role' => 'required|string|max:255',
            'signer2_name' => 'required|string|max:255',
            'signer2_reg_number' => 'nullable|string|max:255',
        ]);

        if ($request->has('set_active')) {
            LspReportSetting::query()->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        $setting = LspReportSetting::create($validated);
        return redirect()->route('lsp.report', ['setting_id' => $setting->id])
            ->with('success', 'Template konfigurasi cetak baru berhasil disimpan dan diterapkan.');
    }

    /**
     * FUNGSI: updateReportSetting()
     * UNTUK APA: Memperbarui isi konfigurasi konten cetak laporan USK yang sedang digunakan (Update).
     * ALUR KERJA: Memvalidasi perubahan teks kop, nomor surat, nama asesor/direktur, lalu memperbarui record terkait di tabel lsp_report_settings.
     */
    public function updateReportSetting(Request $request, LspReportSetting $setting)
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'tuk_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_info' => 'required|string',
            'document_title' => 'required|string|max:255',
            'document_number' => 'required|string|max:255',
            'tuk_location' => 'required|string|max:255',
            'assessment_date' => 'nullable|date',
            'assessment_method' => 'required|string|max:255',
            'closing_statement' => 'required|string',
            'signer1_role' => 'required|string|max:255',
            'signer1_name' => 'required|string|max:255',
            'signer1_reg_number' => 'nullable|string|max:255',
            'signer2_city' => 'required|string|max:255',
            'signer2_role' => 'required|string|max:255',
            'signer2_name' => 'required|string|max:255',
            'signer2_reg_number' => 'nullable|string|max:255',
        ]);

        if ($request->has('set_active')) {
            LspReportSetting::query()->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        $setting->update($validated);
        return redirect()->route('lsp.report', ['setting_id' => $setting->id])
            ->with('success', 'Konfigurasi isi dokumen cetak berhasil diperbarui.');
    }

    /**
     * FUNGSI: destroyReportSetting()
     * UNTUK APA: Menghapus template konfigurasi laporan yang tidak digunakan lagi (Delete).
     * ALUR KERJA: Menghapus record dari lsp_report_settings dengan syarat minimal tersisa 1 template.
     */
    public function destroyReportSetting(LspReportSetting $setting)
    {
        if (LspReportSetting::count() <= 1) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus template terakhir. Minimal harus ada 1 template laporan.']);
        }

        $wasActive = $setting->is_active;
        $setting->delete();

        // Jika yang dihapus berstatus aktif, aktifkan template pertama yang tersisa
        if ($wasActive) {
            $firstRemaining = LspReportSetting::first();
            if ($firstRemaining) {
                $firstRemaining->update(['is_active' => true]);
            }
        }

        return redirect()->route('lsp.report')->with('success', 'Template konfigurasi cetak berhasil dihapus.');
    }
}
