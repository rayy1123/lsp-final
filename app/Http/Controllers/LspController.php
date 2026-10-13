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
        $this->ensureUserColumns();
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
        if (Auth::check()) {
            return redirect()->route('lsp.dashboard');
        }
        $schemes = CertificationScheme::orderBy('scheme_name')->get();
        return view('lsp.register', compact('schemes'));
    }

    /**
     * FUNGSI: register()
     * UNTUK APA: Memvalidasi dan menyimpan registrasi akun asesi (role user).
     * ATURAN VALIDASI:
     * - Nama: maksimal 50 karakter, tidak boleh mengandung angka, boleh tanda kutip (misal Ma'ruf).
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
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'scheme_id' => 'nullable|exists:certification_schemes,id',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 50 karakter.',
            'name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip yang diizinkan).',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 digit.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $this->ensureUserColumns();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if (Schema::hasColumn('users', 'role')) {
            $userData['role'] = 'user';
        }
        if (Schema::hasColumn('users', 'phone_number')) {
            $userData['phone_number'] = $validated['phone_number'];
        }
        if (Schema::hasColumn('users', 'address')) {
            $userData['address'] = $validated['address'] ?? null;
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

        DB::transaction(function () use ($validated, $userData, $existingUser) {
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
                        'full_name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone_number' => $validated['phone_number'],
                        'address' => $validated['address'] ?? null,
                        'status' => 'Belum Kompeten',
                    ]);
                } else {
                    $participant->update([
                        'scheme_id' => $validated['scheme_id'],
                        'full_name' => $validated['name'],
                        'phone_number' => $validated['phone_number'],
                        'address' => $validated['address'] ?? $participant->address,
                    ]);
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

        if ($participant) {
            $participant->update([
                'scheme_id' => $validated['scheme_id'],
                'address' => $address ?: $participant->address,
            ]);
        } else {
            $regNumber = $this->generateRegistrationNumber();
            Participant::create([
                'scheme_id' => $validated['scheme_id'],
                'registration_number' => $regNumber,
                'full_name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'address' => $address,
                'status' => 'Belum Kompeten',
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
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^[0-9+]+$/',
            ],
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 50 karakter.',
            'name.regex' => 'Nama tidak boleh mengandung angka (hanya huruf, spasi, dan tanda kutip yang diizinkan).',
            'phone_number.required' => 'Nomor handphone wajib diisi.',
            'phone_number.max' => 'Nomor handphone maksimal 13 karakter.',
            'phone_number.regex' => 'Nomor handphone hanya boleh berisi angka.',
            'password.min' => 'Password baru minimal terdiri dari 6 karakter.',
        ]);

        $this->ensureUserColumns();

        $userData = [
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'],
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        if (Schema::hasColumn('users', 'address')) {
            $userData['address'] = $validated['address'] ?? null;
        }

        $user->update($userData);

        // Sinkronkan ke record participants jika asesi sudah terdaftar pada skema
        $participant = Participant::where('email', $user->email)->first();
        if ($participant) {
            $participant->update([
                'full_name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'] ?? $participant->address,
            ]);
        }

        return back()->with('success', 'Master data & biodata akun asesi berhasil diperbarui.');
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
        $totalKompeten = Participant::where('status', 'like', '%Kompeten%')->count();
        $totalBelum = Participant::where('status', 'not like', '%Kompeten%')->count();
        $recentParticipants = Participant::with('scheme')->latest()->take(5)->get();

        // Data Penyelesaian Tugas Ujian untuk Verifikasi di Dashboard Admin
        $taskSubmissions = TaskSubmission::with(['task.scheme', 'participant', 'user'])->latest()->get();
        $assessmentTasks = AssessmentTask::with('scheme')->latest()->get();

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

        // Kalkulasi Kelengkapan Master Data Akun User (100% jika nama, email, hp, dan domisili terisi)
        $profileFields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'phone_number' => !empty($user->phone_number),
            'address' => !empty($user->address ?? $myParticipant?->address),
        ];
        $completedCount = count(array_filter($profileFields));
        $profileCompleteness = round(($completedCount / count($profileFields)) * 100);

        return view('lsp.dashboard', compact(
            'totalParticipants',
            'totalSchemes',
            'totalKompeten',
            'totalBelum',
            'recentParticipants',
            'taskSubmissions',
            'assessmentTasks',
            'myParticipant',
            'allSchemes',
            'schemeTasks',
            'profileCompleteness',
            'profileFields'
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
            // Role User / Asesi: Privasi ketat, jangan pernah load data peserta lain
            $query = CertificationScheme::latest();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('scheme_code', 'like', "%{$search}%")
                      ->orWhere('scheme_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
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
        ], [
            'scheme_id.required' => 'Skema sertifikasi wajib dipilih.',
            'task_code.required' => 'Kode unit tugas wajib diisi.',
            'title.required' => 'Judul unit tugas ujian wajib diisi.',
            'description.required' => 'Uraian petunjuk tugas ujian wajib diisi.',
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

        $validated = $request->validate([
            'submission_content' => 'required|string|max:2000',
        ], [
            'submission_content.required' => 'Bukti jawaban, tautan tugas/portofolio wajib diisi.',
            'submission_content.max' => 'Konten jawaban maksimal 2000 karakter.',
        ]);

        $this->ensureExamTables();

        TaskSubmission::updateOrCreate(
            [
                'task_id' => $task->id,
                'participant_id' => $participant->id,
            ],
            [
                'user_id' => $user->id,
                'submission_content' => $validated['submission_content'],
                'status' => 'Menunggu Peninjauan',
                'admin_notes' => null,
            ]
        );

        return back()->with('success', 'Tugas ujian berhasil dikumpulkan dan menunggu pengecekan asesor.');
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

        $submission->update($validated);

        // Periksa kelulusan seluruh tugas pada skema peserta
        $participant = $submission->participant;
        if ($participant && $validated['status'] === 'Diterima') {
            $totalTasks = AssessmentTask::where('scheme_id', $participant->scheme_id)->count();
            $acceptedSubmissions = TaskSubmission::where('participant_id', $participant->id)
                ->where('status', 'Diterima')
                ->count();

            if ($totalTasks > 0 && $acceptedSubmissions >= $totalTasks) {
                $participant->update(['status' => 'Kompensasi / Kompeten']);
                return back()->with('success', 'Tugas disetujui (Diterima). Seluruh unit tugas telah terpenuhi, status peserta otomatis terverifikasi Kompeten!');
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
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('task_submissions')) {
                Schema::create('task_submissions', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('task_id')->constrained('assessment_tasks')->onDelete('cascade');
                    $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                    $table->text('submission_content');
                    $table->string('status', 50)->default('Menunggu Peninjauan');
                    $table->text('admin_notes')->nullable();
                    $table->timestamps();
                });
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
                    if (!Schema::hasColumn('users', 'phone_number')) {
                        $table->string('phone_number', 20)->nullable()->after('role');
                    }
                    if (!Schema::hasColumn('users', 'address')) {
                        $table->text('address')->nullable()->after('phone_number');
                    }
                });

                // Hapus akun admin lama yang salah role
                User::where('email', 'admin@lsp.id')->delete();

                // Bersihkan akun sarah yang tertinggal di tabel users setelah data peserta dihapus
                User::where(function($q) {
                    $q->where('email', 'like', 'sarah@gma%')
                      ->orWhere('email', 'sarah@gmai.com')
                      ->orWhere('email', 'sarah@gmail.com');
                })->where('role', '!=', 'admin')->delete();

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
        } catch (\Throwable $e) {
            // Kolom sudah ada atau driver mengunci skema
        }
    }
}

