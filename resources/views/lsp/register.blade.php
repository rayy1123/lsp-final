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
            background: radial-gradient(circle at 50% 0%, #eef2ff, #f8fafc 60%);
        }
        .register-card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
        }
    </style>
</head>
<body>
    <div class="register-wrap">
        <div class="register-card">
            <!-- Header Brand Registrasi -->
            <div style="text-align: center; margin-bottom: 1.75rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-md); box-shadow: 0 6px 16px rgba(0,0,0,0.2); background: #000; margin: 0 auto 0.75rem;">
                <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">REGISTRASI ASESI LSP</h1>
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

            <!-- Form Pendaftaran -->
            <form action="{{ route('lsp.register.post') }}" method="POST" id="registerForm">
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

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">Email Aktif <span style="color: var(--accent-rose);">*</span></label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="asesi@email.com"
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

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Password <span style="color: var(--accent-rose);">*</span></label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Min. 6 karakter"
                            required
                        >
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label class="form-label">Ulangi Password <span style="color: var(--accent-rose);">*</span></label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password"
                            required
                        >
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
        nameInput.addEventListener('input', function() {
            // Hapus karakter angka jika diketik/dipaste
            this.value = this.value.replace(/[0-9]/g, '');
        });

        // Real-time sanitization: nomor handphone hanya angka
        const phoneInput = document.getElementById('reg_phone');
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
