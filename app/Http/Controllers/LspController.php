<?php

namespace App\Http\Controllers;

use App\Models\AssessmentTask;
use App\Models\CertificationScheme;
use App\Models\LspReportSetting;
use App\Models\Participant;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $this->ensureUserColumns();
        $this->ensureTransparentLogo();
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
            $totalKompeten = Participant::where('status', 'not like', '%Belum%')
                ->where('status', 'like', '%Kompeten%')
                ->count();
        }

        $myParticipant = null;
        if (Auth::check() && !Auth::user()->isAdmin() && Schema::hasTable('participants')) {
            $myParticipant = Participant::where('email', Auth::user()->email)->first();
        }

        return view('lsp.landing', compact('schemes', 'totalSchemes', 'totalParticipants', 'totalKompeten', 'myParticipant'));
    }

    /**
     * FUNGSI: showLogin()
     * UNTUK APA: Menampilkan formulir login admin / pengguna LSP.
     * ALUR KERJA: Jika sudah ada sesi aktif di tab lain, tetap tampilkan info akun aktif dengan opsi ganti akun/logout.
     */
    public function showLogin(Request $request)
    {
        $this->ensureUserColumns();
        $this->ensureTransparentLogo();

        $currentUser = Auth::user();
        return view('lsp.login', compact('currentUser'));
    }

    /**
     * FUNGSI: login()
     * UNTUK APA: Memvalidasi kredensial login admin dan membuat sesi otentikasi baru.
     * ALUR KERJA: Validasi input email/username dan password, cocokkan kredensial, regenerasi session, dan auto-fallback admin.
     */
    public function login(Request $request)
    {
        $this->ensureUserColumns();

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$field => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            $userName = Auth::user()->name;
            return redirect()->intended(route('lsp.dashboard'))
                ->with('success', 'Berhasil masuk ke portal. Selamat datang, ' . $userName . '!');
        }

        // Fallback akun administrator
        if (($request->email === 'admin@gmail.com' || $request->email === 'admin') && $request->password === '123456') {
            User::where('email', 'admin@lsp.id')->delete();
            $admin = User::updateOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'admin',
                    'role' => 'admin',
                    'password' => Hash::make('123456'),
                    'phone_number' => '081299990001',
                ]
            );
            Auth::login($admin);
            $request->session()->regenerate();
            return redirect()->intended(route('lsp.dashboard'))
                ->with('success', 'Berhasil masuk sebagai Administrator. Selamat datang!');
        }

        return back()->withErrors([
            'login' => 'Username/email atau password tidak sesuai.',
        ]);
    }

    /**
     * FUNGSI: showRegister()
     * UNTUK APA: Menampilkan formulir pendaftaran akun asesi baru (User).
     */
    public function showRegister()
    {
        $this->ensureUserColumns();
        $this->ensureTransparentLogo();
        $currentUser = Auth::user();
        $schemes = CertificationScheme::orderBy('scheme_name')->get();
        return view('lsp.register', compact('schemes', 'currentUser'));
    }

    /**
     * FUNGSI: register()
     * UNTUK APA: Memvalidasi dan menyimpan registrasi akun asesi (role user).
     * ATURAN VALIDASI:
     * - Nama: maksimal 50 karakter, tidak boleh mengandung angka, boleh tanda kutip (misal Ma'ruf).
     * - NIK: maksimal 16 digit angka.
     * - Jenis Kelamin: Laki-laki atau Perempuan.
     * - Email: wajib alamat Gmail (@gmail.com).
     * - Nomor handphone: maksimal 13 karakter, angka saja.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}\s\'`’\-]+$/u',
            ],
            'nik' => [
                'required',
                'string',
                'max:16',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'required',
                'string',
                'in:Laki-laki,Perempuan',
            ],
            'education_type' => [
                'required',
                'string',
                'in:Siswa/Pelajar,Mahasiswa,Umum',
            ],
            'academic_title' => [
                'nullable',
                'string',
                'max:50',
            ],
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
            ],
            'password' => 'required|string|min:6|confirmed',
            'scheme_id' => 'nullable|exists:certification_schemes,id',
            'address' => 'nullable|string|max:500',
            'id_card_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp,pdf',
            ],
            'report_sheet_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx',
            ],
            'certificate_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx',
            ],
            'diploma_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx',
            ],
            'photo_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp',
            ],
            'portfolio_file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpeg,jpg,png,webp,pdf,doc,docx,zip',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 50 karakter.',
            'name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip yang diizinkan).',
            'nik.required' => 'NIK (Nomor Induk Kependudukan) wajib diisi.',
            'nik.max' => 'NIK maksimal 16 digit.',
            'nik.regex' => 'NIK hanya boleh berupa angka (maksimal 16 digit).',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan.',
            'education_type.required' => 'Kategori asesi (Siswa, Mahasiswa, atau Umum) wajib dipilih.',
            'education_type.in' => 'Pilihan kategori asesi tidak valid.',
            'academic_title.max' => 'Gelar akademik/profesi maksimal 50 karakter.',
            'diploma_file.max' => 'Ukuran berkas ijazah/surat keterangan maksimal 2MB (2048 KB).',
            'photo_file.max' => 'Ukuran pas foto maksimal 2MB (2048 KB).',
            'portfolio_file.max' => 'Ukuran berkas portofolio maksimal 2MB (2048 KB).',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 digit.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.regex' => 'Alamat email wajib menggunakan akun Gmail (@gmail.com).',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'id_card_file.file' => 'Berkas foto KTP / Kartu Pelajar harus berupa berkas file.',
            'id_card_file.max' => 'Ukuran foto KTP / Kartu Pelajar maksimal 2MB (2048 KB).',
            'id_card_file.mimes' => 'Format foto KTP / Kartu Pelajar harus JPG, PNG, WEBP, atau PDF.',
            'report_sheet_file.file' => 'Berkas rapor terakhir harus berupa berkas file.',
            'report_sheet_file.max' => 'Ukuran berkas rapor terakhir maksimal 2MB (2048 KB).',
            'report_sheet_file.mimes' => 'Format berkas rapor terakhir harus PDF, DOC, DOCX, JPG, atau PNG.',
            'certificate_file.file' => 'Berkas sertifikat harus berupa berkas file.',
            'certificate_file.max' => 'Ukuran berkas sertifikat maksimal 2MB (2048 KB).',
            'certificate_file.mimes' => 'Format berkas sertifikat harus PDF, DOC, DOCX, JPG, atau PNG.',
        ]);

        $this->ensureUserColumns();

        // Unggah Berkas Persyaratan Asesi
        $targetDir = public_path('uploads/registrations');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $idCardPath = null;
        if ($request->hasFile('id_card_file') && $request->file('id_card_file')->isValid()) {
            $file = $request->file('id_card_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ktp_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $idCardPath = 'uploads/registrations/' . $fileName;
        }

        $reportSheetPath = null;
        if ($request->hasFile('report_sheet_file') && $request->file('report_sheet_file')->isValid()) {
            $file = $request->file('report_sheet_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'rapor_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $reportSheetPath = 'uploads/registrations/' . $fileName;
        }

        $certificatePath = null;
        if ($request->hasFile('certificate_file') && $request->file('certificate_file')->isValid()) {
            $file = $request->file('certificate_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'sertifikat_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $certificatePath = 'uploads/registrations/' . $fileName;
        }

        $diplomaPath = null;
        if ($request->hasFile('diploma_file') && $request->file('diploma_file')->isValid()) {
            $file = $request->file('diploma_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ijazah_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $diplomaPath = 'uploads/registrations/' . $fileName;
        }

        $photoPath = null;
        if ($request->hasFile('photo_file') && $request->file('photo_file')->isValid()) {
            $file = $request->file('photo_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'foto_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $photoPath = 'uploads/registrations/' . $fileName;
        }

        $portfolioPath = null;
        if ($request->hasFile('portfolio_file') && $request->file('portfolio_file')->isValid()) {
            $file = $request->file('portfolio_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'portofolio_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $portfolioPath = 'uploads/registrations/' . $fileName;
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if (Schema::hasColumn('users', 'role')) {
            $userData['role'] = 'user';
        }
        if (Schema::hasColumn('users', 'nik')) {
            $userData['nik'] = $validated['nik'];
        }
        if (Schema::hasColumn('users', 'gender')) {
            $userData['gender'] = $validated['gender'];
        }
        if (Schema::hasColumn('users', 'education_type')) {
            $userData['education_type'] = $validated['education_type'];
        }
        if (Schema::hasColumn('users', 'academic_title')) {
            $userData['academic_title'] = in_array($validated['education_type'], ['Mahasiswa', 'Umum']) ? ($validated['academic_title'] ?? null) : null;
        }
        if (Schema::hasColumn('users', 'phone_number')) {
            $userData['phone_number'] = $validated['phone_number'];
        }
        if (Schema::hasColumn('users', 'address')) {
            $userData['address'] = $validated['address'] ?? null;
        }
        if ($idCardPath && Schema::hasColumn('users', 'id_card_file')) {
            $userData['id_card_file'] = $idCardPath;
        }
        if ($reportSheetPath && Schema::hasColumn('users', 'report_sheet_file')) {
            $userData['report_sheet_file'] = $reportSheetPath;
        }
        if ($certificatePath && Schema::hasColumn('users', 'certificate_file')) {
            $userData['certificate_file'] = $certificatePath;
        }
        if ($diplomaPath && Schema::hasColumn('users', 'diploma_file')) {
            $userData['diploma_file'] = $diplomaPath;
        }
        if ($photoPath && Schema::hasColumn('users', 'photo_file')) {
            $userData['photo_file'] = $photoPath;
        }
        if ($portfolioPath && Schema::hasColumn('users', 'portfolio_file')) {
            $userData['portfolio_file'] = $portfolioPath;
        }

        // Cek jika akun sudah ada (misal akibat error registrasi peserta sebelumnya)
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser) {
            $existingParticipant = Participant::where('email', $validated['email'])->first();
            if ($existingParticipant) {
                return back()->withInput()->withErrors([
                    'email' => 'Email ini sudah terdaftar di sistem. Silakan masuk dengan akun Anda.'
                ]);
            }
        }

        DB::transaction(function () use ($validated, $userData, $existingUser, $idCardPath, $reportSheetPath, $certificatePath, $diplomaPath, $photoPath, $portfolioPath) {
            if ($existingUser) {
                $existingUser->update($userData);
                $user = $existingUser;
            } else {
                $user = User::create($userData);
            }

            // Jika pendaftar langsung memilih skema sertifikasi
            if (!empty($validated['scheme_id'])) {
                $participant = Participant::where('email', $validated['email'])->first();
                if (!$participant) {
                    $regNumber = $this->generateRegistrationNumber();
                    Participant::create([
                        'scheme_id' => $validated['scheme_id'],
                        'registration_number' => $regNumber,
                        'nik' => $validated['nik'],
                        'gender' => $validated['gender'],
                        'education_type' => $userData['education_type'] ?? 'Siswa/Pelajar',
                        'academic_title' => $userData['academic_title'] ?? null,
                        'full_name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone_number' => $validated['phone_number'],
                        'address' => $validated['address'] ?? null,
                        'id_card_file' => $idCardPath,
                        'report_sheet_file' => $reportSheetPath,
                        'certificate_file' => $certificatePath,
                        'diploma_file' => $diplomaPath,
                        'photo_file' => $photoPath,
                        'portfolio_file' => $portfolioPath,
                        'status' => 'Pending',
                    ]);
                } else {
                    $partUpdate = [
                        'scheme_id' => $validated['scheme_id'],
                        'nik' => $validated['nik'],
                        'gender' => $validated['gender'],
                        'education_type' => $userData['education_type'] ?? 'Siswa/Pelajar',
                        'academic_title' => $userData['academic_title'] ?? null,
                        'full_name' => $validated['name'],
                        'phone_number' => $validated['phone_number'],
                        'address' => $validated['address'] ?? $participant->address,
                    ];
                    if ($idCardPath) $partUpdate['id_card_file'] = $idCardPath;
                    if ($reportSheetPath) $partUpdate['report_sheet_file'] = $reportSheetPath;
                    if ($certificatePath) $partUpdate['certificate_file'] = $certificatePath;
                    if ($diplomaPath) $partUpdate['diploma_file'] = $diplomaPath;
                    if ($photoPath) $partUpdate['photo_file'] = $photoPath;
                    if ($portfolioPath) $partUpdate['portfolio_file'] = $portfolioPath;

                    $participant->update($partUpdate);
                }
            }
        });

        // Tidak auto-login, kembalikan ke halaman utama dengan notifikasi sukses
        return redirect()->route('lsp.landing')
            ->with('success', 'Akun pendaftaran asesi berhasil dibuat! Silakan masuk dengan akun Anda.');
    }

    /**
     * FUNGSI: applyScheme()
     * UNTUK APA: Memproses pendaftaran skema baru oleh asesi yang sudah login.
     * SYARAT: Wajib memiliki akun user terdaftar dan kelengkapan kontak (telepon).
     */
    public function applyScheme(Request $request)
    {
        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Syarat kondisi: Pendaftaran skema mewajibkan akun user aktif dengan data kontak
        if (empty($user->phone_number)) {
            return back()->withErrors([
                'error' => 'Pendaftaran skema membutuhkan kelengkapan data. Harap lengkapi nomor WhatsApp/HP pada formulir biodata Anda terlebih dahulu.'
            ]);
        }

        $address = $validated['address'] ?? $user->address ?? null;
        if (!empty($validated['address']) && Schema::hasColumn('users', 'address')) {
            $user->update(['address' => $validated['address']]);
        }

        $participant = Participant::where('email', $user->email)->first();

        if ($participant && $participant->scheme_id == $validated['scheme_id']) {
            return back()->with('info', 'Anda sudah terdaftar dan sedang mengerjakan skema sertifikasi ini.');
        }

        if ($participant) {
            $participant->update([
                'scheme_id' => $validated['scheme_id'],
                'nik' => $user->nik ?? $participant->nik,
                'gender' => $user->gender ?? $participant->gender,
                'address' => $address ?: $participant->address,
                'status' => 'Pending',
            ]);
        } else {
            $regNumber = $this->generateRegistrationNumber();
            Participant::create([
                'scheme_id' => $validated['scheme_id'],
                'registration_number' => $regNumber,
                'nik' => $user->nik ?? null,
                'gender' => $user->gender ?? null,
                'full_name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'address' => $address,
                'status' => 'Pending',
            ]);
        }

        return back()->with('success', 'Skema sertifikasi berhasil dipilih!');
    }

    /**
     * FUNGSI: generateRegistrationNumber()
     * UNTUK APA: Menghasilkan nomor registrasi peserta yang unik dan berurutan secara otomatis,
     * mencegah benturan/duplikasi (1062 Duplicate entry) jika ada data yang dihapus atau dised.
     */
    protected function generateRegistrationNumber(): string
    {
        $prefix = 'REG-' . date('Y') . '-';

        $regNumbers = Participant::where('registration_number', 'like', $prefix . '%')
            ->pluck('registration_number');

        $maxNum = 0;
        foreach ($regNumbers as $reg) {
            $numPart = substr($reg, strlen($prefix));
            if (is_numeric($numPart)) {
                $num = (int)$numPart;
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $nextNum = max($maxNum + 1, Participant::count() + 1);

        while (Participant::where('registration_number', $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT))->exists()) {
            $nextNum++;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * FUNGSI: updateProfile()
     * UNTUK APA: Memperbarui kelengkapan master data & biodata akun asesi (User).
     * Menyinkronkan perubahan biodata ke tabel users dan record participants terkait.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}\s\'`’\-]+$/u',
            ],
            'nik' => [
                'nullable',
                'string',
                'max:16',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'nullable',
                'string',
                'in:Laki-laki,Perempuan',
            ],
            'education_type' => [
                'nullable',
                'string',
                'in:Siswa/Pelajar,Mahasiswa,Umum',
            ],
            'academic_title' => [
                'nullable',
                'string',
                'max:50',
            ],
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500',
            'id_card_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,pdf',
            'report_sheet_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'certificate_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'diploma_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'photo_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'portfolio_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp,zip',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 50 karakter.',
            'name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip yang diizinkan).',
            'nik.max' => 'NIK maksimal 16 digit.',
            'nik.regex' => 'NIK hanya boleh berupa angka (maksimal 16 digit).',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan.',
            'education_type.in' => 'Kategori asesi harus Siswa/Pelajar, Mahasiswa, atau Umum.',
            'academic_title.max' => 'Gelar akademik/profesi maksimal 50 karakter.',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 karakter.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
            'password.min' => 'Password baru minimal terdiri dari 6 karakter.',
            'id_card_file.file' => 'Berkas KTP/kartu pelajar harus berupa berkas file.',
            'id_card_file.max' => 'Ukuran berkas KTP/kartu pelajar maksimal 2MB (2048 KB).',
            'id_card_file.mimes' => 'Format berkas KTP harus JPG, PNG, atau PDF.',
            'report_sheet_file.file' => 'Berkas rapor terakhir harus berupa berkas file.',
            'report_sheet_file.max' => 'Ukuran berkas rapor terakhir maksimal 2MB (2048 KB).',
            'report_sheet_file.mimes' => 'Format berkas rapor terakhir harus PDF, DOC, DOCX, JPG, atau PNG.',
            'certificate_file.file' => 'Berkas sertifikat harus berupa berkas file.',
            'certificate_file.max' => 'Ukuran berkas sertifikat maksimal 2MB (2048 KB).',
            'certificate_file.mimes' => 'Format berkas sertifikat harus PDF, DOC, DOCX, JPG, atau PNG.',
            'diploma_file.max' => 'Ukuran berkas ijazah/surat keterangan maksimal 2MB (2048 KB).',
            'photo_file.max' => 'Ukuran pas foto maksimal 2MB (2048 KB).',
            'portfolio_file.max' => 'Ukuran berkas portofolio maksimal 2MB (2048 KB).',
        ]);

        $this->ensureUserColumns();

        $targetDir = public_path('uploads/registrations');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $idCardPath = null;
        if ($request->hasFile('id_card_file') && $request->file('id_card_file')->isValid()) {
            if ($user->id_card_file && file_exists(public_path($user->id_card_file))) {
                @unlink(public_path($user->id_card_file));
            }
            $file = $request->file('id_card_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ktp_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $idCardPath = 'uploads/registrations/' . $fileName;
        }

        $reportSheetPath = null;
        if ($request->hasFile('report_sheet_file') && $request->file('report_sheet_file')->isValid()) {
            if ($user->report_sheet_file && file_exists(public_path($user->report_sheet_file))) {
                @unlink(public_path($user->report_sheet_file));
            }
            $file = $request->file('report_sheet_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'rapor_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $reportSheetPath = 'uploads/registrations/' . $fileName;
        }

        $certificatePath = null;
        if ($request->hasFile('certificate_file') && $request->file('certificate_file')->isValid()) {
            if ($user->certificate_file && file_exists(public_path($user->certificate_file))) {
                @unlink(public_path($user->certificate_file));
            }
            $file = $request->file('certificate_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'sertifikat_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $certificatePath = 'uploads/registrations/' . $fileName;
        }

        $diplomaPath = null;
        if ($request->hasFile('diploma_file') && $request->file('diploma_file')->isValid()) {
            if ($user->diploma_file && file_exists(public_path($user->diploma_file))) {
                @unlink(public_path($user->diploma_file));
            }
            $file = $request->file('diploma_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ijazah_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $diplomaPath = 'uploads/registrations/' . $fileName;
        }

        $photoPath = null;
        if ($request->hasFile('photo_file') && $request->file('photo_file')->isValid()) {
            if ($user->photo_file && file_exists(public_path($user->photo_file))) {
                @unlink(public_path($user->photo_file));
            }
            $file = $request->file('photo_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'foto_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $photoPath = 'uploads/registrations/' . $fileName;
        }

        $portfolioPath = null;
        if ($request->hasFile('portfolio_file') && $request->file('portfolio_file')->isValid()) {
            if ($user->portfolio_file && file_exists(public_path($user->portfolio_file))) {
                @unlink(public_path($user->portfolio_file));
            }
            $file = $request->file('portfolio_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'portofolio_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $portfolioPath = 'uploads/registrations/' . $fileName;
        }

        $userData = [
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ];
        if (isset($validated['nik']) && Schema::hasColumn('users', 'nik')) {
            $userData['nik'] = $validated['nik'];
        }
        if (isset($validated['gender']) && Schema::hasColumn('users', 'gender')) {
            $userData['gender'] = $validated['gender'];
        }
        if (isset($validated['education_type']) && Schema::hasColumn('users', 'education_type')) {
            $userData['education_type'] = $validated['education_type'];
        }
        if (Schema::hasColumn('users', 'academic_title')) {
            $userData['academic_title'] = in_array($validated['education_type'] ?? '', ['Mahasiswa', 'Umum']) ? ($validated['academic_title'] ?? null) : null;
        }
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        if (Schema::hasColumn('users', 'address')) {
            $userData['address'] = $validated['address'] ?? null;
        }
        if ($idCardPath && Schema::hasColumn('users', 'id_card_file')) {
            $userData['id_card_file'] = $idCardPath;
        }
        if ($reportSheetPath && Schema::hasColumn('users', 'report_sheet_file')) {
            $userData['report_sheet_file'] = $reportSheetPath;
        }
        if ($certificatePath && Schema::hasColumn('users', 'certificate_file')) {
            $userData['certificate_file'] = $certificatePath;
        }
        if ($diplomaPath && Schema::hasColumn('users', 'diploma_file')) {
            $userData['diploma_file'] = $diplomaPath;
        }
        if ($photoPath && Schema::hasColumn('users', 'photo_file')) {
            $userData['photo_file'] = $photoPath;
        }
        if ($portfolioPath && Schema::hasColumn('users', 'portfolio_file')) {
            $userData['portfolio_file'] = $portfolioPath;
        }

        $user->update($userData);

        // Sinkronkan ke record participants jika asesi sudah terdaftar pada skema
        $participant = Participant::where('email', $user->email)->first();
        if ($participant) {
            $participantData = [
                'full_name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'] ?? $participant->address,
            ];
            if (isset($validated['nik'])) {
                $participantData['nik'] = $validated['nik'];
            }
            if (isset($validated['gender'])) {
                $participantData['gender'] = $validated['gender'];
            }
            if (isset($validated['education_type'])) {
                $participantData['education_type'] = $validated['education_type'];
            }
            if (array_key_exists('academic_title', $userData)) {
                $participantData['academic_title'] = $userData['academic_title'];
            }
            if ($idCardPath) {
                $participantData['id_card_file'] = $idCardPath;
            }
            if ($reportSheetPath) {
                $participantData['report_sheet_file'] = $reportSheetPath;
            }
            if ($certificatePath) {
                $participantData['certificate_file'] = $certificatePath;
            }
            if ($diplomaPath) {
                $participantData['diploma_file'] = $diplomaPath;
            }
            if ($photoPath) {
                $participantData['photo_file'] = $photoPath;
            }
            if ($portfolioPath) {
                $participantData['portfolio_file'] = $portfolioPath;
            }
            $participant->update($participantData);
        }

        return back()->with('success', 'Master data, biodata akun, dan berkas persyaratan asesi berhasil diperbarui.');
    }

    /**
     * FUNGSI: updateUser()
     * UNTUK APA: Administrator memperbarui master biodata akun pengguna / asesi dan dokumen persyaratan.
     */
    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses khusus administrator.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}\s\'`’\-]+$/u',
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                'unique:users,email,' . $user->id,
            ],
            'nik' => [
                'nullable',
                'string',
                'max:16',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'nullable',
                'string',
                'in:Laki-laki,Perempuan',
            ],
            'education_type' => [
                'nullable',
                'string',
                'in:Siswa/Pelajar,Mahasiswa,Umum',
            ],
            'academic_title' => [
                'nullable',
                'string',
                'max:50',
            ],
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'id_card_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,pdf',
            'report_sheet_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'certificate_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'diploma_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
            'photo_file' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp',
            'portfolio_file' => 'nullable|file|max:2048|mimes:pdf,doc,docx,jpg,jpeg,png,webp,zip',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 50 karakter.',
            'name.regex' => 'Nama tidak boleh mengandung angka.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nik.max' => 'NIK maksimal 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan.',
            'education_type.in' => 'Kategori asesi harus Siswa/Pelajar, Mahasiswa, atau Umum.',
            'academic_title.max' => 'Gelar akademik/profesi maksimal 50 karakter.',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 karakter.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
            'password.min' => 'Password minimal 6 karakter.',
            'id_card_file.max' => 'Ukuran berkas KTP maksimal 2MB (2048 KB).',
            'report_sheet_file.max' => 'Ukuran berkas rapor maksimal 2MB (2048 KB).',
            'certificate_file.max' => 'Ukuran berkas sertifikat maksimal 2MB (2048 KB).',
            'diploma_file.max' => 'Ukuran berkas ijazah/surat keterangan maksimal 2MB (2048 KB).',
            'photo_file.max' => 'Ukuran pas foto maksimal 2MB (2048 KB).',
            'portfolio_file.max' => 'Ukuran berkas portofolio maksimal 2MB (2048 KB).',
        ]);

        $this->ensureUserColumns();

        $targetDir = public_path('uploads/registrations');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $idCardPath = null;
        if ($request->hasFile('id_card_file') && $request->file('id_card_file')->isValid()) {
            if ($user->id_card_file && file_exists(public_path($user->id_card_file))) {
                @unlink(public_path($user->id_card_file));
            }
            $file = $request->file('id_card_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ktp_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $idCardPath = 'uploads/registrations/' . $fileName;
        }

        $reportSheetPath = null;
        if ($request->hasFile('report_sheet_file') && $request->file('report_sheet_file')->isValid()) {
            if ($user->report_sheet_file && file_exists(public_path($user->report_sheet_file))) {
                @unlink(public_path($user->report_sheet_file));
            }
            $file = $request->file('report_sheet_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'rapor_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $reportSheetPath = 'uploads/registrations/' . $fileName;
        }

        $certificatePath = null;
        if ($request->hasFile('certificate_file') && $request->file('certificate_file')->isValid()) {
            if ($user->certificate_file && file_exists(public_path($user->certificate_file))) {
                @unlink(public_path($user->certificate_file));
            }
            $file = $request->file('certificate_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'sertifikat_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $certificatePath = 'uploads/registrations/' . $fileName;
        }

        $diplomaPath = null;
        if ($request->hasFile('diploma_file') && $request->file('diploma_file')->isValid()) {
            if ($user->diploma_file && file_exists(public_path($user->diploma_file))) {
                @unlink(public_path($user->diploma_file));
            }
            $file = $request->file('diploma_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ijazah_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $diplomaPath = 'uploads/registrations/' . $fileName;
        }

        $photoPath = null;
        if ($request->hasFile('photo_file') && $request->file('photo_file')->isValid()) {
            if ($user->photo_file && file_exists(public_path($user->photo_file))) {
                @unlink(public_path($user->photo_file));
            }
            $file = $request->file('photo_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'foto_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $photoPath = 'uploads/registrations/' . $fileName;
        }

        $portfolioPath = null;
        if ($request->hasFile('portfolio_file') && $request->file('portfolio_file')->isValid()) {
            if ($user->portfolio_file && file_exists(public_path($user->portfolio_file))) {
                @unlink(public_path($user->portfolio_file));
            }
            $file = $request->file('portfolio_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'portofolio_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($targetDir, $fileName);
            $portfolioPath = 'uploads/registrations/' . $fileName;
        }

        $oldEmail = $user->email;
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ];

        if (isset($validated['nik']) && Schema::hasColumn('users', 'nik')) {
            $userData['nik'] = $validated['nik'];
        }
        if (isset($validated['gender']) && Schema::hasColumn('users', 'gender')) {
            $userData['gender'] = $validated['gender'];
        }
        if (isset($validated['education_type']) && Schema::hasColumn('users', 'education_type')) {
            $userData['education_type'] = $validated['education_type'];
        }
        if (Schema::hasColumn('users', 'academic_title')) {
            $userData['academic_title'] = in_array($validated['education_type'] ?? '', ['Mahasiswa', 'Umum']) ? ($validated['academic_title'] ?? null) : null;
        }
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        if (Schema::hasColumn('users', 'address')) {
            $userData['address'] = $validated['address'] ?? null;
        }
        if ($idCardPath && Schema::hasColumn('users', 'id_card_file')) {
            $userData['id_card_file'] = $idCardPath;
        }
        if ($reportSheetPath && Schema::hasColumn('users', 'report_sheet_file')) {
            $userData['report_sheet_file'] = $reportSheetPath;
        }
        if ($certificatePath && Schema::hasColumn('users', 'certificate_file')) {
            $userData['certificate_file'] = $certificatePath;
        }
        if ($diplomaPath && Schema::hasColumn('users', 'diploma_file')) {
            $userData['diploma_file'] = $diplomaPath;
        }
        if ($photoPath && Schema::hasColumn('users', 'photo_file')) {
            $userData['photo_file'] = $photoPath;
        }
        if ($portfolioPath && Schema::hasColumn('users', 'portfolio_file')) {
            $userData['portfolio_file'] = $portfolioPath;
        }

        $user->update($userData);

        // Sinkronkan ke data peserta (Participant)
        $participant = Participant::where('email', $oldEmail)->first();
        if ($participant) {
            $partData = [
                'full_name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'] ?? $participant->address,
            ];
            if (isset($validated['nik'])) {
                $partData['nik'] = $validated['nik'];
            }
            if (isset($validated['gender'])) {
                $partData['gender'] = $validated['gender'];
            }
            if (isset($validated['education_type'])) {
                $partData['education_type'] = $validated['education_type'];
            }
            if (array_key_exists('academic_title', $userData)) {
                $partData['academic_title'] = $userData['academic_title'];
            }
            if ($idCardPath) {
                $partData['id_card_file'] = $idCardPath;
            }
            if ($reportSheetPath) {
                $partData['report_sheet_file'] = $reportSheetPath;
            }
            if ($certificatePath) {
                $partData['certificate_file'] = $certificatePath;
            }
            if ($diplomaPath) {
                $partData['diploma_file'] = $diplomaPath;
            }
            if ($photoPath) {
                $partData['photo_file'] = $photoPath;
            }
            if ($portfolioPath) {
                $partData['portfolio_file'] = $portfolioPath;
            }
            $participant->update($partData);
        }

        return back()->with('success', "Biodata akun {$user->name} berhasil diperbarui.");
    }

    /**
     * FUNGSI: destroyUser()
     * UNTUK APA: Menghapus akun pengguna dan biodata peserta terkait (khusus Admin).
     */
    public function destroyUser(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses khusus administrator.');
        }

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun administrator yang sedang aktif digunakan.']);
        }

        foreach (['id_card_file', 'report_sheet_file', 'certificate_file', 'diploma_file', 'photo_file', 'portfolio_file'] as $col) {
            if ($user->$col && file_exists(public_path($user->$col))) {
                @unlink(public_path($user->$col));
            }
        }

        Participant::where('email', $user->email)->delete();

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun pengguna {$userName} berhasil dihapus dari sistem.");
    }

    /**
     * FUNGSI: assessmentResult()
     * UNTUK APA: Menampilkan menu khusus status & hasil rekomendasi asesmen kompetensi asesi.
     * Dipisahkan dari dashboard utama sesuai instruksi perapihan tata letak dan fokus konten.
     */
    public function assessmentResult()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('lsp.dashboard');
        }

        $this->ensureUserColumns();
        $this->ensureExamTables();
        $participant = Participant::with(['scheme.tasks', 'submissions'])->where('email', $user->email)->first();

        $schemeTasks = collect();
        if ($participant && $participant->scheme_id) {
            $schemeTasks = AssessmentTask::where('scheme_id', $participant->scheme_id)
                ->with(['submissions' => function($sq) use ($participant) {
                    $sq->where('participant_id', $participant->id);
                }])
                ->get();
        }

        return view('lsp.assessment_result', compact('participant', 'schemeTasks'));
    }

    /**
     * FUNGSI: logout()
     * UNTUK APA: Mengakhiri sesi pengguna yang sedang aktif secara aman.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('lsp.landing')
            ->with('success', 'Anda telah berhasil keluar dari sistem LSP.');
    }

    /**
     * FUNGSI: dashboard()
     * UNTUK APA: Menyajikan dashboard sesuai hak akses:
     * - Role Admin: metrik statistik asesmen, riwayat asesi, dan informasi penyelesaian tugas / verifikasi tugas.
     * - Role User: master data akun user (edit biodata), skema sertifikasi aktif, dan tugas ujian asesmen.
     */
    public function dashboard()
    {
        $this->ensureUserColumns();
        $this->ensureExamTables();

        $user = Auth::user();
        $totalParticipants = Participant::count();
        $totalSchemes = CertificationScheme::count();
        $totalKompeten = Participant::where('status', 'not like', '%Belum%')
            ->where('status', 'like', '%Kompeten%')
            ->count();
        $totalBelum = Participant::where('status', 'like', '%Belum%')->count();
        $totalPending = Participant::where(function($q) {
            $q->where('status', 'like', '%Pending%')
              ->orWhereNull('status')
              ->orWhere(function($sub) {
                  $sub->where('status', 'not like', '%Kompeten%')
                      ->where('status', 'not like', '%Belum%');
              });
        })->count();
        $recentParticipants = Participant::with('scheme')->latest()->take(5)->get();

        // Data Penyelesaian Tugas Ujian untuk Verifikasi di Dashboard Admin
        $taskSubmissions = TaskSubmission::with(['task.scheme', 'participant', 'user'])->latest()->get();
        $assessmentTasks = AssessmentTask::with('scheme')->latest()->get();

        // Master Data Akun User & Asesi (Khusus Dashboard Admin)
        $allUsers = collect();
        if ($user->isAdmin()) {
            $allUsers = User::with(['participant.scheme.tasks', 'participant.submissions'])
                ->orderBy('role', 'asc')
                ->latest()
                ->get();
        }

        // Data khusus untuk role User/Asesi
        $myParticipant = Participant::with('scheme')->where('email', $user->email)->first();
        $allSchemes = CertificationScheme::orderBy('scheme_name')->get();

        // Unit Tugas Ujian Skema Asesi
        $schemeTasks = collect();
        if ($myParticipant && $myParticipant->scheme_id) {
            $schemeTasks = AssessmentTask::where('scheme_id', $myParticipant->scheme_id)
                ->with(['submissions' => function($sq) use ($myParticipant) {
                    $sq->where('participant_id', $myParticipant->id);
                }])
                ->get();
        }

        // Kalkulasi Kelengkapan Master Data Akun & 6 Berkas Dokumen Persyaratan Asesi
        $idCard = !empty($user->id_card_file ?: ($myParticipant?->id_card_file ?? null));
        $reportSheet = !empty($user->report_sheet_file ?: ($myParticipant?->report_sheet_file ?? null));
        $certificate = !empty($user->certificate_file ?: ($myParticipant?->certificate_file ?? null));
        $diploma = !empty($user->diploma_file ?: ($myParticipant?->diploma_file ?? null));
        $photo = !empty($user->photo_file ?: ($myParticipant?->photo_file ?? null));
        $portfolio = !empty($user->portfolio_file ?: ($myParticipant?->portfolio_file ?? null));

        $docFields = [
            'id_card_file' => $idCard,
            'report_sheet_file' => $reportSheet,
            'certificate_file' => $certificate,
            'diploma_file' => $diploma,
            'photo_file' => $photo,
            'portfolio_file' => $portfolio,
        ];
        $uploadedDocsCount = count(array_filter($docFields));
        $totalDocsCount = count($docFields);

        $biodataFields = [
            'name' => !empty($user->name),
            'nik' => !empty($user->nik ?: ($myParticipant?->nik ?? null)),
            'gender' => !empty($user->gender ?: ($myParticipant?->gender ?? null)),
            'email' => !empty($user->email),
            'phone_number' => !empty($user->phone_number ?: ($myParticipant?->phone_number ?? null)),
            'address' => !empty($user->address ?: ($myParticipant?->address ?? null)),
        ];

        $profileFields = array_merge($biodataFields, $docFields);
        $completedCount = count(array_filter($profileFields));
        $profileCompleteness = (int) round(($completedCount / count($profileFields)) * 100);

        return view('lsp.dashboard', compact(
            'totalParticipants',
            'totalSchemes',
            'totalKompeten',
            'totalBelum',
            'totalPending',
            'recentParticipants',
            'taskSubmissions',
            'assessmentTasks',
            'allUsers',
            'myParticipant',
            'allSchemes',
            'schemeTasks',
            'profileCompleteness',
            'profileFields',
            'biodataFields',
            'docFields',
            'uploadedDocsCount',
            'totalDocsCount'
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
        $this->ensureUserColumns();

        $search = $request->input('search');
        $myParticipant = null;
        $currentScheme = null;

        if (Auth::user()->isAdmin()) {
            // Role Admin: Akses master data skema dan inspeksi relasi peserta (1:N)
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
        } else {
            // Role User / Asesi: Ambil data skema yang sedang aktif dikerjakan
            $myParticipant = Participant::where('email', Auth::user()->email)->with('scheme')->first();
            $currentScheme = $myParticipant ? $myParticipant->scheme : null;
            if ($currentScheme) {
                $currentScheme->loadCount('participants');
            }

            $query = CertificationScheme::withCount('participants')->latest();

            // Kecualikan skema yang sedang aktif dikerjakan asesi dari daftar pilihan
            if ($currentScheme) {
                $query->where('id', '!=', $currentScheme->id);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('scheme_code', 'like', "%{$search}%")
                      ->orWhere('scheme_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }

        $schemes = $query->get();
        return view('lsp.schemes', compact('schemes', 'search', 'myParticipant', 'currentScheme'));
    }

    /**
     * FUNGSI: storeScheme()
     * UNTUK APA: Menyimpan data master skema sertifikasi baru ke tabel certification_schemes.
     */
    public function storeScheme(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('lsp.dashboard')->withErrors(['error' => 'Akses data peserta hanya untuk administrator.']);
        }

        $this->ensureUserColumns();

        $search = $request->input('search');
        $query = Participant::with(['scheme.tasks', 'submissions'])->latest();

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
        $schemes = CertificationScheme::with('tasks')->orderBy('scheme_name')->get();

        // Pemanggilan data akun user terdaftar untuk form pendaftaran peserta
        $registeredUsers = User::where(function($q) {
            $q->where('role', 'user')->orWhereNull('role');
        })->orderBy('name')->get();

        $nextRegNumber = $this->generateRegistrationNumber();

        return view('lsp.participants', compact('participants', 'schemes', 'search', 'registeredUsers', 'nextRegNumber'));
    }

    /**
     * FUNGSI: storeParticipant()
     * UNTUK APA: Mendaftarkan data peserta asesmen baru ke dalam skema tertentu.
     * SYARAT MUTLAK: Wajib memiliki akun user yang sudah terdaftar di sistem.
     */
    public function storeParticipant(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $this->ensureUserColumns();

        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'registration_number' => 'required|string|unique:participants,registration_number',
            'nik' => [
                'nullable',
                'string',
                'max:16',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'nullable',
                'string',
                'in:Laki-laki,Perempuan',
            ],
            'full_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}\s\'`’\-]+$/u',
            ],
            'email' => 'required|email|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'address' => 'nullable|string',
            'status' => 'required|string',
        ], [
            'nik.max' => 'NIK maksimal 16 digit.',
            'nik.regex' => 'NIK hanya boleh berupa angka (maksimal 16 digit).',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal 50 karakter.',
            'full_name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip).',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 karakter.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
        ]);

        // SYARAT KONDISI PENDAFTARAN SKEMA:
        // Wajib memiliki akun user terdaftar di sistem
        $userAccount = User::where('email', $validated['email'])->first();
        if (!$userAccount) {
            return back()->withInput()->withErrors([
                'email' => 'Syarat pendaftaran skema tidak terpenuhi: Akun user untuk email (' . $validated['email'] . ') belum terdaftar di sistem. Harap daftarkan akun user terlebih dahulu.'
            ]);
        }

        // Cek duplikasi pendaftaran skema
        $alreadyEnrolled = Participant::where('email', $validated['email'])
            ->where('scheme_id', $validated['scheme_id'])
            ->exists();
        if ($alreadyEnrolled) {
            return back()->withInput()->withErrors([
                'email' => 'Asesi (' . $validated['email'] . ') sudah terdaftar pada skema sertifikasi ini.'
            ]);
        }

        if (empty($userAccount->phone_number) && !empty($validated['phone_number'])) {
            $userAccount->update(['phone_number' => $validated['phone_number']]);
        }
        if (empty($userAccount->address) && !empty($validated['address']) && Schema::hasColumn('users', 'address')) {
            $userAccount->update(['address' => $validated['address']]);
        }
        if (empty($userAccount->nik) && !empty($validated['nik']) && Schema::hasColumn('users', 'nik')) {
            $userAccount->update(['nik' => $validated['nik']]);
        }
        if (empty($userAccount->gender) && !empty($validated['gender']) && Schema::hasColumn('users', 'gender')) {
            $userAccount->update(['gender' => $validated['gender']]);
        }

        // Validasi metrik tugas untuk status Kompeten
        if (str_contains($validated['status'], 'Kompeten') && !str_contains($validated['status'], 'Belum')) {
            $totalTasks = AssessmentTask::where('scheme_id', $validated['scheme_id'])->count();
            if ($totalTasks > 0) {
                return back()->withInput()->withErrors([
                    'status' => "Peserta baru belum dapat langsung dinyatakan Kompeten. Peserta wajib menyelesaikan seluruh ({$totalTasks}) unit tugas ujian terlebih dahulu."
                ]);
            }
        }

        Participant::create($validated);
        return back()->with('success', 'Peserta berhasil didaftarkan ke skema sertifikasi.');
    }

    /**
     * FUNGSI: updateParticipant()
     * UNTUK APA: Mengubah data profil asesi maupun memperbarui status hasil uji kompetensi.
     */
    public function updateParticipant(Request $request, Participant $participant)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'registration_number' => 'required|string|unique:participants,registration_number,' . $participant->id,
            'nik' => [
                'nullable',
                'string',
                'max:16',
                'regex:/^[0-9]+$/',
            ],
            'gender' => [
                'nullable',
                'string',
                'in:Laki-laki,Perempuan',
            ],
            'full_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}\s\'`’\-]+$/u',
            ],
            'email' => 'required|email|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'address' => 'nullable|string',
            'status' => 'required|string',
        ], [
            'nik.max' => 'NIK maksimal 16 digit.',
            'nik.regex' => 'NIK hanya boleh berupa angka (maksimal 16 digit).',
            'gender.in' => 'Pilihan jenis kelamin harus Laki-laki atau Perempuan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal 50 karakter.',
            'full_name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip).',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 karakter.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
        ]);

        // METRIK INDIKATOR SYARAT STATUS KOMPETEN:
        // Asesi hanya bisa dinyatakan Kompeten jika sudah menyelesaikan seluruh tugas yang diberikan (status 'Diterima')
        if (str_contains($validated['status'], 'Kompeten') && !str_contains($validated['status'], 'Belum')) {
            $totalTasks = AssessmentTask::where('scheme_id', $validated['scheme_id'])->count();
            $acceptedTasks = TaskSubmission::where('participant_id', $participant->id)
                ->where('status', 'Diterima')
                ->count();

            if ($totalTasks > 0 && $acceptedTasks < $totalTasks) {
                return back()->withInput()->withErrors([
                    'status' => "Syarat status Kompeten belum terpenuhi! Asesi baru menyelesaikan {$acceptedTasks} dari {$totalTasks} unit tugas yang diberikan. Seluruh tugas wajib berstatus Diterima oleh asesor sebelum opsi Kompeten dapat disetujui."
                ]);
            }
        }

        $participant->update($validated);
        return back()->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * FUNGSI: destroyParticipant()
     * UNTUK APA: Menghapus data peserta/asesi tertentu dari sistem.
     */
    public function destroyParticipant(Participant $participant)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $email = $participant->email;
        $participant->delete();

        // Hapus akun login di tabel users agar tidak bisa login lagi
        User::where('email', $email)->where('role', '!=', 'admin')->delete();

        return back()->with('success', 'Data peserta dan akun pengguna berhasil dihapus dari sistem.');
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
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('lsp.dashboard')->withErrors(['error' => 'Akses laporan berita acara hanya untuk administrator.']);
        }

        $this->ensureUserColumns();

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
        $totalKompeten = $participants->filter(fn($p) => str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum'))->count();
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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

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

    /**
     * FUNGSI: storeTask()
     * UNTUK APA: Menyimpan unit tugas atau soal ujian asesmen baru untuk skema tertentu (Admin).
     */
    public function storeTask(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $this->ensureExamTables();

        $validated = $request->validate([
            'scheme_id' => 'required|exists:certification_schemes,id',
            'task_code' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
        ], [
            'scheme_id.required' => 'Skema sertifikasi wajib dipilih.',
            'task_code.required' => 'Kode unit tugas wajib diisi.',
            'title.required' => 'Judul unit tugas ujian wajib diisi.',
            'description.required' => 'Uraian petunjuk tugas ujian wajib diisi.',
            'due_date.date' => 'Format batas waktu pengumpulan tugas tidak valid.',
        ]);

        AssessmentTask::create($validated);
        return back()->with('success', 'Unit tugas/ujian asesmen berhasil ditambahkan.');
    }

    /**
     * FUNGSI: destroyTask()
     * UNTUK APA: Menghapus unit tugas ujian skema sertifikasi (Admin).
     */
    public function destroyTask(AssessmentTask $task)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $task->delete();
        return back()->with('success', 'Unit tugas/ujian asesmen berhasil dihapus.');
    }

    /**
     * FUNGSI: submitTask()
     * UNTUK APA: Memproses penyerahan bukti pengerjaan tugas ujian asesmen oleh asesi (User).
     */
    public function submitTask(Request $request, AssessmentTask $task)
    {
        $user = Auth::user();
        $participant = Participant::where('email', $user->email)->first();

        if (!$participant) {
            return back()->withErrors(['error' => 'Anda harus terdaftar pada skema sertifikasi terlebih dahulu untuk mengumpulkan tugas.']);
        }

        // Validasi batas waktu pengumpulan tugas ujian
        if ($task->due_date && now()->greaterThan(\Carbon\Carbon::parse($task->due_date))) {
            return back()->withErrors(['error' => 'Batas waktu pengerjaan unit tugas ini telah berakhir pada ' . \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') . ' WIB.']);
        }

        $validated = $request->validate([
            'submission_content' => 'required|string|max:2000',
            'submission_file' => 'nullable|file|max:1024|mimes:png,jpg,jpeg,webp,pdf,doc,docx,zip',
        ], [
            'submission_content.required' => 'Bukti jawaban, uraian pengerjaan, atau tautan tugas/portofolio wajib diisi.',
            'submission_content.max' => 'Konten uraian jawaban maksimal 2000 karakter.',
            'submission_file.max' => 'Ukuran berkas/gambar maksimal 1MB (1024 KB).',
            'submission_file.mimes' => 'Format berkas harus berupa gambar (png, jpg, jpeg, webp) atau dokumen (pdf, doc, docx, zip).',
        ]);

        $this->ensureExamTables();

        $uploadedPath = null;
        $uploadedName = null;
        $uploadedType = null;

        if ($request->hasFile('submission_file') && $request->file('submission_file')->isValid()) {
            $file = $request->file('submission_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $uploadedName = $file->getClientOriginalName();
            $uploadedType = in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']) ? 'image' : 'document';

            $targetDir = public_path('uploads/submissions');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $safeFileName = 'task_' . $task->id . '_part_' . $participant->id . '_' . time() . '.' . $ext;
            $file->move($targetDir, $safeFileName);
            $uploadedPath = 'uploads/submissions/' . $safeFileName;
        }

        $existing = TaskSubmission::where('task_id', $task->id)
            ->where('participant_id', $participant->id)
            ->first();

        if ($existing) {
            // Jika ada unggahan berkas baru dan berkas lama ada, bersihkan berkas lama
            if ($uploadedPath && $existing->file_path && file_exists(public_path($existing->file_path))) {
                @unlink(public_path($existing->file_path));
            }

            // Pembaruan atau revisi tugas dari asesi (memicu notifikasi perubahan isi di dashboard admin)
            $revisionCount = ($existing->revision_count ?? 0) + 1;
            $updateData = [
                'user_id' => $user->id,
                'submission_content' => $validated['submission_content'],
                'status' => 'Pembaruan / Revisi',
                'is_revised' => true,
                'revision_count' => $revisionCount,
                'admin_notes' => null,
            ];

            if ($uploadedPath) {
                $updateData['file_path'] = $uploadedPath;
                $updateData['file_name'] = $uploadedName;
                $updateData['file_type'] = $uploadedType;
            }

            $existing->update($updateData);

            // Jika asesi mengirim pembaruan/revisi tugas, status kelulusan disetel ke Pending (menunggu peninjauan kembali oleh asesor)
            if ($participant) {
                $participant->update(['status' => 'Pending']);
            }

            return back()->with('success', 'Pembaruan tugas ujian berhasil dikirim! Asesor akan menerima notifikasi perubahan isi tugas.');
        } else {
            TaskSubmission::create([
                'task_id' => $task->id,
                'participant_id' => $participant->id,
                'user_id' => $user->id,
                'submission_content' => $validated['submission_content'],
                'file_path' => $uploadedPath,
                'file_name' => $uploadedName,
                'file_type' => $uploadedType,
                'status' => 'Menunggu Peninjauan',
                'is_revised' => false,
                'revision_count' => 0,
                'admin_notes' => null,
            ]);

            // Jika status asesi sebelumnya Belum Kompeten dan mengunggah tugas baru, status disetel ke Pending
            if ($participant && $participant->status === 'Belum Kompeten') {
                $participant->update(['status' => 'Pending']);
            }

            return back()->with('success', 'Tugas ujian berhasil dikumpulkan dan menunggu pengecekan asesor.');
        }
    }

    /**
     * FUNGSI: reviewSubmission()
     * UNTUK APA: Mengecek apakah tugas diterima atau ditolak oleh Administrator/Asesor (Admin).
     * Memberikan informasi kelulusan tugas dan menyinkronkan status kompetensi peserta.
     */
    public function reviewSubmission(Request $request, TaskSubmission $submission)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses terbatas untuk administrator LSP.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Diterima,Ditolak',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'is_revised' => false, // Reset penanda notifikasi revisi setelah diperiksa
        ]);

        // Periksa kelulusan seluruh tugas pada skema peserta
        $participant = $submission->participant;
        if ($participant && $validated['status'] === 'Diterima') {
            $totalTasks = AssessmentTask::where('scheme_id', $participant->scheme_id)->count();
            $acceptedSubmissions = TaskSubmission::where('participant_id', $participant->id)
                ->where('status', 'Diterima')
                ->count();

            if ($totalTasks > 0 && $acceptedSubmissions >= $totalTasks) {
                $participant->update(['status' => 'Kompeten']);
                return back()->with('success', 'Tugas disetujui (Diterima). Seluruh unit tugas telah terpenuhi, status peserta otomatis terverifikasi Kompeten!');
            }

            // Jika belum semua unit tugas terpenuhi namun tidak ada yang ditolak, status tetap Pending
            $hasRejected = TaskSubmission::where('participant_id', $participant->id)
                ->where('status', 'Ditolak')
                ->exists();
            if (!$hasRejected) {
                $participant->update(['status' => 'Pending']);
            }
        } elseif ($participant && $validated['status'] === 'Ditolak') {
            $participant->update(['status' => 'Belum Kompeten']);
        }

        return back()->with('success', 'Hasil pemeriksaan tugas berhasil diperbarui: ' . $validated['status']);
    }

    /**
     * Memastikan tabel 'assessment_tasks' dan 'task_submissions' ada secara dinamis/idempoten.
     */
    protected function ensureExamTables(): void
    {
        try {
            if (!Schema::hasTable('assessment_tasks')) {
                Schema::create('assessment_tasks', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('scheme_id')->constrained('certification_schemes')->onDelete('cascade');
                    $table->string('task_code', 50)->nullable();
                    $table->string('title');
                    $table->text('description')->nullable();
                    $table->dateTime('due_date')->nullable();
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('assessment_tasks', 'due_date')) {
                    Schema::table('assessment_tasks', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->dateTime('due_date')->nullable()->after('description');
                    });
                }
            }

            if (!Schema::hasTable('task_submissions')) {
                Schema::create('task_submissions', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('task_id')->constrained('assessment_tasks')->onDelete('cascade');
                    $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->text('submission_content');
                    $table->string('file_path')->nullable();
                    $table->string('file_name')->nullable();
                    $table->string('file_type', 50)->nullable();
                    $table->string('status', 50)->default('Menunggu Peninjauan');
                    $table->boolean('is_revised')->default(false);
                    $table->integer('revision_count')->default(0);
                    $table->text('admin_notes')->nullable();
                    $table->timestamps();
                });
            } else {
                if (!Schema::hasColumn('task_submissions', 'file_path')) {
                    Schema::table('task_submissions', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('file_path')->nullable()->after('submission_content');
                        $table->string('file_name')->nullable()->after('file_path');
                        $table->string('file_type', 50)->nullable()->after('file_name');
                    });
                }
                if (!Schema::hasColumn('task_submissions', 'is_revised')) {
                    Schema::table('task_submissions', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->boolean('is_revised')->default(false)->after('status');
                        $table->integer('revision_count')->default(0)->after('is_revised');
                    });
                }
            }

            if (AssessmentTask::count() === 0 && CertificationScheme::count() > 0) {
                $s1 = CertificationScheme::where('scheme_code', 'JWD-2026')->first() ?? CertificationScheme::first();
                if ($s1) {
                    AssessmentTask::create([
                        'scheme_id' => $s1->id,
                        'task_code' => 'TSK-JWD-01',
                        'title' => 'Implementasi CRUD & Validasi Input Web',
                        'description' => 'Buat modul entri data berbasis form dengan validasi server-side dan penanganan error responsif sesuai unit kompetensi J.620100.017.02.',
                    ]);
                    AssessmentTask::create([
                        'scheme_id' => $s1->id,
                        'task_code' => 'TSK-JWD-02',
                        'title' => 'Proteksi Keamanan Basis Data & Otentikasi',
                        'description' => 'Implementasikan hashing password aman dan proteksi injeksi SQL / XSS pada sesi login sesuai unit kompetensi J.620100.023.02.',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Skema telah terbentuk
        }
    }

    /**
     * Memastikan kolom 'role' dan 'phone_number' ada pada tabel users secara dinamis/idempoten,
     * serta menjamin akun administrator resmi admin@gmail.com terkonfigurasi.
     */
    protected function ensureUserColumns(): void
    {
        try {
            if (Schema::hasTable('users')) {
                Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!Schema::hasColumn('users', 'role')) {
                        $table->string('role')->default('user')->after('email');
                    }
                    if (!Schema::hasColumn('users', 'nik')) {
                        $table->string('nik', 16)->nullable()->after('name');
                    }
                    if (!Schema::hasColumn('users', 'gender')) {
                        $table->string('gender', 20)->nullable()->after('nik');
                    }
                    if (!Schema::hasColumn('users', 'education_type')) {
                        $table->string('education_type', 50)->default('Siswa/Pelajar')->after('gender');
                    }
                    if (!Schema::hasColumn('users', 'academic_title')) {
                        $table->string('academic_title', 50)->nullable()->after('education_type');
                    }
                    if (!Schema::hasColumn('users', 'phone_number')) {
                        $table->string('phone_number', 20)->nullable()->after('role');
                    }
                    if (!Schema::hasColumn('users', 'address')) {
                        $table->text('address')->nullable()->after('phone_number');
                    }
                    if (!Schema::hasColumn('users', 'id_card_file')) {
                        $table->string('id_card_file', 255)->nullable()->after('address');
                    }
                    if (!Schema::hasColumn('users', 'report_sheet_file')) {
                        $table->string('report_sheet_file', 255)->nullable()->after('id_card_file');
                    }
                    if (!Schema::hasColumn('users', 'certificate_file')) {
                        $table->string('certificate_file', 255)->nullable()->after('report_sheet_file');
                    }
                    if (!Schema::hasColumn('users', 'diploma_file')) {
                        $table->string('diploma_file', 255)->nullable()->after('certificate_file');
                    }
                    if (!Schema::hasColumn('users', 'photo_file')) {
                        $table->string('photo_file', 255)->nullable()->after('diploma_file');
                    }
                    if (!Schema::hasColumn('users', 'portfolio_file')) {
                        $table->string('portfolio_file', 255)->nullable()->after('photo_file');
                    }
                });

                // Hapus akun admin lama yang salah role
                User::where('email', 'admin@lsp.id')->delete();

                // Bersihkan akun sarah/duplikat yang diminta dihapus oleh admin
                User::where(function($q) {
                    $q->where('email', 'like', 'sarah@gma%')
                      ->orWhere('email', 'sarah@gmai.com')
                      ->orWhere('email', 'sarah@gmail.com')
                      ->orWhere('email', 'radata@gmail.com')
                      ->orWhere('email', 'ara@gmail.com');
                })->where('role', '!=', 'admin')->delete();

                // Hapus peserta yatim / orphan yang akun login database-nya sudah tidak ada
                $existingEmails = User::pluck('email')->filter()->toArray();
                Participant::whereNotIn('email', $existingEmails)->delete();

                // Pastikan admin@gmail.com aktif sebagai Administrator
                if (Schema::hasColumn('users', 'role')) {
                    $admin = User::where('email', 'admin@gmail.com')->first();
                    if (!$admin) {
                        User::create([
                            'name' => 'admin',
                            'email' => 'admin@gmail.com',
                            'role' => 'admin',
                            'password' => Hash::make('123456'),
                            'phone_number' => '081299990001',
                        ]);
                    } elseif ($admin->role !== 'admin') {
                        $admin->update([
                            'role' => 'admin',
                            'password' => Hash::make('123456'),
                        ]);
                    }
                }
            }

            if (Schema::hasTable('participants')) {
                Schema::table('participants', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!Schema::hasColumn('participants', 'nik')) {
                        $table->string('nik', 16)->nullable()->after('registration_number');
                    }
                    if (!Schema::hasColumn('participants', 'gender')) {
                        $table->string('gender', 20)->nullable()->after('nik');
                    }
                    if (!Schema::hasColumn('participants', 'education_type')) {
                        $table->string('education_type', 50)->default('Siswa/Pelajar')->after('gender');
                    }
                    if (!Schema::hasColumn('participants', 'academic_title')) {
                        $table->string('academic_title', 50)->nullable()->after('education_type');
                    }
                    if (!Schema::hasColumn('participants', 'id_card_file')) {
                        $table->string('id_card_file', 255)->nullable()->after('address');
                    }
                    if (!Schema::hasColumn('participants', 'report_sheet_file')) {
                        $table->string('report_sheet_file', 255)->nullable()->after('id_card_file');
                    }
                    if (!Schema::hasColumn('participants', 'certificate_file')) {
                        $table->string('certificate_file', 255)->nullable()->after('report_sheet_file');
                    }
                    if (!Schema::hasColumn('participants', 'diploma_file')) {
                        $table->string('diploma_file', 255)->nullable()->after('certificate_file');
                    }
                    if (!Schema::hasColumn('participants', 'photo_file')) {
                        $table->string('photo_file', 255)->nullable()->after('diploma_file');
                    }
                    if (!Schema::hasColumn('participants', 'portfolio_file')) {
                        $table->string('portfolio_file', 255)->nullable()->after('photo_file');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Kolom sudah ada atau driver mengunci skema
        }
    }

    /**
     * Pastikan logo PNG memiliki latar belakang transparan (hapus background hitam)
     */
    protected function ensureTransparentLogo(): void
    {
        $flag = public_path('images/.logo_cleaned_v1');
        $src = public_path('images/logo.png');

        if (file_exists($flag) || !file_exists($src) || !extension_loaded('gd')) {
            return;
        }

        try {
            $img = @imagecreatefrompng($src);
            if (!$img && file_exists(public_path('images/logo.jpeg'))) {
                $img = @imagecreatefromjpeg(public_path('images/logo.jpeg'));
            }

            if ($img) {
                $w = imagesx($img);
                $h = imagesy($img);

                $out = imagecreatetruecolor($w, $h);
                imagealphablending($out, false);
                imagesavealpha($out, true);

                $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
                imagefilledrectangle($out, 0, 0, $w, $h, $transparent);

                for ($x = 0; $x < $w; $x++) {
                    for ($y = 0; $y < $h; $y++) {
                        $rgba = imagecolorat($img, $x, $y);
                        $r = ($rgba >> 16) & 0xFF;
                        $g = ($rgba >> 8) & 0xFF;
                        $b = $rgba & 0xFF;

                        $maxVal = max($r, max($g, $b));
                        if ($maxVal < 35) {
                            imagesetpixel($out, $x, $y, $transparent);
                        } else {
                            if ($maxVal < 65) {
                                $alpha = (int)(127 * (1 - ($maxVal - 35) / 30));
                                $color = imagecolorallocatealpha($out, $r, $g, $b, $alpha);
                            } else {
                                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
                            }
                            imagesetpixel($out, $x, $y, $color);
                        }
                    }
                }

                @copy($src, public_path('images/logo-black-backup.png'));
                imagepng($out, $src);
                imagedestroy($img);
                imagedestroy($out);
            }
        } catch (\Throwable $e) {
            // Abaikan kegagalan manipulasi gambar secara graceful
        }

        @file_put_contents($flag, '1');
    }
}

