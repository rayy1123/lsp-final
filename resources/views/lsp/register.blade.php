<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Asesi - LSP Certification Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/astro-dark.css') }}">
    <style>
        .register-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2.5rem 1.5rem;
            background: var(--bg-canvas);
        }
        .register-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
        }
        .password-toggle-btn {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }
        .password-toggle-btn:hover {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="register-wrap">
        <div class="register-card">
            <!-- Header Brand Registrasi -->
            <div style="text-align: center; margin-bottom: 1.75rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 60px; height: 60px; object-fit: contain; margin: 0 auto 0.75rem; background: transparent;">
                <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">REGISTRASI LSP</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Daftarkan akun peserta untuk sertifikasi kompetensi</p>
            </div>

            <!-- Pesan Error Validasi -->
            @if($errors->any())
                <div style="background: var(--accent-rose-bg); border: 1px solid var(--accent-rose-border); color: var(--accent-rose); padding: 0.85rem 1rem; border-radius: var(--radius-sm); font-size: 0.825rem; margin-bottom: 1.25rem;">
                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Mohon periksa data formulir:</div>
                    @foreach($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Sesi Aktif Dari Tab Sebelumnya -->
            @if(isset($currentUser) && $currentUser)
                <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem;">Sesi aktif saat ini:</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ $currentUser->name }}</span>
                        <span class="badge badge-code" style="font-size: 0.7rem;">{{ strtoupper($currentUser->role ?? 'USER') }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                        <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary" style="flex: 1; text-align: center; font-size: 0.8rem; padding: 0.4rem;">
                            Buka Dashboard &rarr;
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; color: var(--accent-rose);" title="Keluar dari sesi akun ini">
                            Ganti Akun
                        </a>
                    </div>
                </div>

                <div style="text-align: center; margin: 1rem 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="flex: 1; height: 1px; background: var(--border-subtle);"></span>
                    <span>Atau Daftarkan Akun Asesi Baru</span>
                    <span style="flex: 1; height: 1px; background: var(--border-subtle);"></span>
                </div>
            @endif

            <!-- Form Pendaftaran -->
            <form action="{{ route('lsp.register.post') }}" method="POST" enctype="multipart/form-data" id="registerForm">
                @csrf

                <!-- Nama Lengkap (Maks 50, No Numbers, Allows Quotes) -->
                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between;">
                        <span>Nama Lengkap Asesi <span style="color: var(--accent-rose);">*</span></span>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">Maks. 50 karakter</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="reg_name"
                        class="form-control"
                        maxlength="50"
                        placeholder="Contoh: Muhammad Ma'ruf"
                        value="{{ old('name') }}"
                        required
                        autofocus
                    >
                    <span style="font-size: 0.725rem; color: var(--text-dim); margin-top: 0.25rem; display: block;">
                        Tanpa angka. Tanda kutip diperbolehkan (contoh: Ma'ruf, O'Connor).
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <!-- NIK (Nomor Induk Kependudukan Maks 16 Karakter) -->
                    <div class="form-group">
                        <label class="form-label" style="display: flex; justify-content: space-between;">
                            <span>NIK (KTP) <span style="color: var(--accent-rose);">*</span></span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">Maks. 16 digit</span>
                        </label>
                        <input
                            type="text"
                            name="nik"
                            id="reg_nik"
                            class="form-control"
                            maxlength="16"
                            inputmode="numeric"
                            placeholder="16 digit NIK sesuai KTP"
                            value="{{ old('nik') }}"
                            required
                        >
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="form-group">
                        <label class="form-label">
                            Jenis Kelamin <span style="color: var(--accent-rose);">*</span>
                        </label>
                        <select name="gender" id="reg_gender" class="form-select" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Kategori Asesi & Gelar Akademik Khusus Mahasiswa -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label">
                            Kategori Asesi <span style="color: var(--accent-rose);">*</span>
                        </label>
                        <select name="education_type" id="reg_education_type" class="form-select" required onchange="toggleAcademicTitle()">
                            <option value="Siswa/Pelajar" {{ old('education_type', 'Siswa/Pelajar') === 'Siswa/Pelajar' ? 'selected' : '' }}>Siswa / Pelajar</option>
                            <option value="Mahasiswa" {{ old('education_type') === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="Umum" {{ old('education_type') === 'Umum' ? 'selected' : '' }}>Umum / Profesional</option>
                        </select>
                    </div>

                    <div class="form-group" id="academicTitleGroup" style="display: {{ in_array(old('education_type'), ['Mahasiswa', 'Umum']) ? 'block' : 'none' }};">
                        <label class="form-label" style="display: flex; justify-content: space-between;">
                            <span id="academicTitleLabel">{{ old('education_type') === 'Umum' ? 'Gelar Profesi / Akademik' : 'Gelar Mahasiswa' }} (Opsional)</span>
                            <span id="academicTitleHint" style="font-size: 0.725rem; color: var(--text-muted); font-weight: 500;">Opsional</span>
                        </label>
                        <input
                            type="text"
                            name="academic_title"
                            id="reg_academic_title"
                            class="form-control"
                            maxlength="50"
                            placeholder="Contoh: S.Kom, S.T., A.Md., M.Kom"
                            value="{{ old('academic_title') }}"
                        >
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <!-- Nomor Handphone (Maks 13) -->
                    <div class="form-group">
                        <label class="form-label" style="display: flex; justify-content: space-between;">
                            <span>No. Handphone <span style="color: var(--accent-rose);">*</span></span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">Maks. 13</span>
                        </label>
                        <input
                            type="tel"
                            name="phone_number"
                            id="reg_phone"
                            class="form-control"
                            maxlength="13"
                            placeholder="Contoh: 081234567890"
                            value="{{ old('phone_number') }}"
                            required
                        >
                    </div>

                    <!-- Alamat Gmail (@gmail.com) -->
                    <div class="form-group">
                        <label class="form-label" style="display: flex; justify-content: space-between;">
                            <span>Alamat Gmail <span style="color: var(--accent-rose);">*</span></span>
                            <span style="font-size: 0.75rem; color: var(--accent-primary); font-weight: 600;">@gmail.com</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="reg_email"
                            class="form-control"
                            placeholder="namaanda@gmail.com"
                            pattern="^[a-zA-Z0-9._%+\-]+@gmail\.com$"
                            title="Format harus menggunakan akun Gmail (@gmail.com)"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>
                </div>

                <!-- Pilihan Skema Sertifikasi -->
                <div class="form-group">
                    <label class="form-label">Pilihan Skema Sertifikasi (Opsional)</label>
                    <select name="scheme_id" class="form-select">
                        <option value="">-- Pilih Skema yang Ingin Diikuti --</option>
                        @foreach($schemes as $s)
                            <option value="{{ $s->id }}" {{ old('scheme_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->scheme_code }} - {{ $s->scheme_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Alamat Domisili -->
                <div class="form-group">
                    <label class="form-label">Alamat Domisili</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Kota / Kabupaten domisili saat ini...">{{ old('address') }}</textarea>
                </div>

                <!-- Persyaratan Berkas Dokumen Asesi (KTP, Rapor, Sertifikat) -->
                <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    <div style="font-size: 0.825rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                        <svg style="width: 16px; height: 16px; color: var(--accent-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <span>Dokumen Persyaratan Asesi (Maks. 2MB per berkas)</span>
                    </div>
                    <p style="font-size: 0.725rem; color: var(--text-muted); margin-bottom: 0.85rem; line-height: 1.35;">
                        Unggah berkas untuk kelengkapan portofolio uji kompetensi (opsional saat registrasi awal).
                    </p>

                    <!-- Foto KTP / Kartu Pelajar -->
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Foto KTP / Kartu Pelajar</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (JPG/PNG/PDF)</span>
                        </label>
                        <input
                            type="file"
                            name="id_card_file"
                            id="reg_id_card"
                            class="form-control file-input-check"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>

                    <!-- Rapor Terakhir -->
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Rapor Terakhir</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (PDF/DOC/Gambar)</span>
                        </label>
                        <input
                            type="file"
                            name="report_sheet_file"
                            id="reg_report_sheet"
                            class="form-control file-input-check"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>

                    <!-- Sertifikat Terkait -->
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Sertifikat Terkait (Pelatihan / Prestasi)</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (PDF/DOC/Gambar)</span>
                        </label>
                        <input
                            type="file"
                            name="certificate_file"
                            id="reg_certificate"
                            class="form-control file-input-check"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>

                    <!-- Ijazah Terakhir / Surat Keterangan Aktif -->
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Ijazah Terakhir / Surat Keterangan Aktif</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (PDF/DOC/Gambar)</span>
                        </label>
                        <input
                            type="file"
                            name="diploma_file"
                            id="reg_diploma"
                            class="form-control file-input-check"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>

                    <!-- Pas Foto Resmi (3x4 / 4x6) -->
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Pas Foto Formal (3x4 / 4x6)</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (JPG/PNG)</span>
                        </label>
                        <input
                            type="file"
                            name="photo_file"
                            id="reg_photo"
                            class="form-control file-input-check"
                            accept=".jpg,.jpeg,.png,.webp"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>

                    <!-- Portofolio / Berkas Pendukung Tambahan -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="font-size: 0.775rem; margin-bottom: 0.25rem; display: flex; justify-content: space-between;">
                            <span>Portofolio / Dokumen Pendukung Tambahan</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Maks. 2MB (PDF/DOC/ZIP/Gambar)</span>
                        </label>
                        <input
                            type="file"
                            name="portfolio_file"
                            id="reg_portfolio"
                            class="form-control file-input-check"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.zip"
                            style="font-size: 0.775rem; padding: 0.35rem 0.5rem;"
                        >
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="reg_password">Password <span style="color: var(--accent-rose);">*</span></label>
                        <div style="position: relative;">
                            <input
                                type="password"
                                name="password"
                                id="reg_password"
                                class="form-control"
                                placeholder="Min. 6 karakter"
                                style="padding-right: 2.5rem;"
                                required
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('reg_password', this)" aria-label="Lihat password" title="Lihat password">
                                <svg class="eye-open" style="width: 17px; height: 17px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg class="eye-closed" style="width: 17px; height: 17px; display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label class="form-label" for="reg_password_confirmation">Ulangi Password <span style="color: var(--accent-rose);">*</span></label>
                        <div style="position: relative;">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="reg_password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                                style="padding-right: 2.5rem;"
                                required
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('reg_password_confirmation', this)" aria-label="Lihat password" title="Lihat password">
                                <svg class="eye-open" style="width: 17px; height: 17px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg class="eye-closed" style="width: 17px; height: 17px; display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.925rem; margin-top: 0.5rem;">
                    Daftar Sekarang &rarr;
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.825rem; color: var(--text-muted); border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                Sudah memiliki akun?
                <a href="{{ route('lsp.login') }}" style="color: var(--accent-primary); font-weight: 600; text-decoration: none;">
                    Masuk di sini
                </a>
                <span style="margin: 0 0.5rem;">•</span>
                <a href="{{ route('lsp.landing') }}" style="color: var(--text-muted); text-decoration: none;">
                    Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        // Real-time sanitization: cegah input angka pada nama
        const nameInput = document.getElementById('reg_name');
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                // Hapus karakter angka jika diketik/dipaste
                this.value = this.value.replace(/[0-9]/g, '');
            });
        }

        // Real-time sanitization: NIK hanya angka & maksimal 16 digit
        const nikInput = document.getElementById('reg_nik');
        if (nikInput) {
            nikInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
            });
        }

        // Real-time sanitization: nomor handphone hanya angka
        const phoneInput = document.getElementById('reg_phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
            });
        }

        // Toggle field gelar akademik/profesi (Mahasiswa / Umum)
        function toggleAcademicTitle() {
            const eduSelect = document.getElementById('reg_education_type');
            const group = document.getElementById('academicTitleGroup');
            const input = document.getElementById('reg_academic_title');
            const label = document.getElementById('academicTitleLabel');
            const hint = document.getElementById('academicTitleHint');
            if (!eduSelect || !group) return;

            if (eduSelect.value === 'Mahasiswa' || eduSelect.value === 'Umum') {
                group.style.display = 'block';
                if (input) input.required = false;
                if (label) {
                    label.innerText = (eduSelect.value === 'Umum' ? 'Gelar Profesi / Akademik' : 'Gelar Mahasiswa') + ' (Opsional)';
                }
                if (hint) {
                    hint.innerText = 'Opsional';
                }
            } else {
                group.style.display = 'none';
                if (input) {
                    input.required = false;
                    input.value = '';
                }
            }
        }

        // Validasi ukuran berkas pendaftaran (maksimal 2MB / 2048 KB)
        document.querySelectorAll('.file-input-check').forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const maxBytes = 2 * 1024 * 1024; // 2MB
                    if (this.files[0].size > maxBytes) {
                        alert('Ukuran berkas melebihi batas maksimal 2MB (2048 KB). File Anda berukuran: ' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB. Silakan pilih berkas yang lebih kecil.');
                        this.value = '';
                    }
                }
            });
        });

        // Jalankan sinkronisasi tampilan gelar saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            toggleAcademicTitle();
        });

        function togglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';

            var eyeOpen = btn.querySelector('.eye-open');
            var eyeClosed = btn.querySelector('.eye-closed');
            if (eyeOpen && eyeClosed) {
                eyeOpen.style.display = isPass ? 'none' : 'block';
                eyeClosed.style.display = isPass ? 'block' : 'none';
            }
            btn.setAttribute('aria-label', isPass ? 'Sembunyikan password' : 'Lihat password');
            btn.setAttribute('title', isPass ? 'Sembunyikan password' : 'Lihat password');
        }

        // Auto-transparansi logo
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('img[src*="logo.png"], img[src*="logo.jpeg"]').forEach(function(img) {
                img.style.background = 'transparent';
                var temp = new Image();
                temp.crossOrigin = 'anonymous';
                temp.src = img.src;
                temp.onload = function() {
                    try {
                        var c = document.createElement('canvas');
                        c.width = temp.naturalWidth || temp.width;
                        c.height = temp.naturalHeight || temp.height;
                        var ctx = c.getContext('2d');
                        ctx.drawImage(temp, 0, 0);
                        var imgData = ctx.getImageData(0, 0, c.width, c.height);
                        var d = imgData.data;
                        var modified = false;
                        for (var i = 0; i < d.length; i += 4) {
                            var maxV = Math.max(d[i], d[i+1], d[i+2]);
                            if (maxV < 35) {
                                d[i+3] = 0;
                                modified = true;
                            } else if (maxV < 65) {
                                d[i+3] = Math.round(255 * ((maxV - 35) / 30));
                                modified = true;
                            }
                        }
                        if (modified) {
                            ctx.putImageData(imgData, 0, 0);
                            img.src = c.toDataURL('image/png');
                        }
                    } catch(e) {}
                };
            });
        });
    </script>
</body>
</html>
