@extends('layouts.astro')

@section('title', 'Print Laporan USK - LSP System')

@section('content')
<!-- Toolbar Kontrol Laporan & CRUD (Hanya tampil di layar, tersembunyi saat cetak) -->
<div class="no-print" style="margin-bottom: 2rem;">
    <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); box-shadow: var(--shadow-card);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-subtle);">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>Laporan Program Uji Sertifikasi Kompetensi (USK)</span>
                    <span class="badge badge-code" style="color: var(--accent-primary); font-size: 0.75rem;">Template: {{ $reportSetting->template_name }}</span>
                </h2>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Format cetak resmi dokumen berita acara dan rekapitulasi asesmen. Isi kop, nomor surat, tanggal, TUK, dan asesor dapat diubah via CRUD.
                </p>
            </div>

            <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
                <!-- Tombol Edit Template Laporan Aktif (CRUD: UPDATE) -->
                <button type="button" class="btn btn-secondary" onclick="openEditSettingModal()" style="border-color: var(--accent-primary-border); color: var(--accent-primary); background: var(--accent-primary-light);">
                    <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Isi Laporan
                </button>

                <!-- Tombol Tambah Template Laporan Baru (CRUD: CREATE) -->
                <button type="button" class="btn btn-secondary" onclick="openCreateSettingModal()">
                    <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Template Baru
                </button>

                <!-- Tombol Cetak / PDF -->
                <button onclick="window.print()" class="btn btn-primary" style="padding: 0.5rem 1.15rem; font-size: 0.85rem;">
                    <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
                    </svg>
                    Cetak Dokumen (Print / PDF)
                </button>
            </div>
        </div>

        <!-- Filter Sub-Bar: Pilihan Template Aktif & Filter Skema Sertifikasi -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.825rem;">
            <!-- Pilih Template Laporan (CRUD: READ Switching) -->
            <form method="GET" action="{{ route('lsp.report') }}" style="display: flex; align-items: center; gap: 0.5rem;">
                @if($schemeId)
                    <input type="hidden" name="scheme_id" value="{{ $schemeId }}">
                @endif
                <label style="font-weight: 600; color: var(--text-muted);">Pilih Template:</label>
                <select name="setting_id" class="form-select" style="width: auto; padding: 0.4rem 0.75rem; font-size: 0.8rem;" onchange="this.form.submit()">
                    @foreach($reportSettingsList as $rs)
                        <option value="{{ $rs->id }}" {{ $reportSetting->id == $rs->id ? 'selected' : '' }}>
                            {{ $rs->template_name }} {{ $rs->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <!-- Tombol Hapus Template (CRUD: DELETE) -->
                @if(count($reportSettingsList) > 1)
                    <button type="button" class="btn btn-danger" style="padding: 0.4rem 0.65rem; font-size: 0.75rem;" onclick="confirmDeleteSetting('{{ route('lsp.report.settings.destroy', $reportSetting->id) }}')" title="Hapus template ini">
                        Hapus Template
                    </button>
                @endif
            </form>

            <!-- Filter Berdasarkan Skema Sertifikasi -->
            <form method="GET" action="{{ route('lsp.report') }}" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="hidden" name="setting_id" value="{{ $reportSetting->id }}">
                <label style="font-weight: 600; color: var(--text-muted);">Filter Skema:</label>
                <select name="scheme_id" class="form-select" style="width: auto; padding: 0.4rem 0.75rem; font-size: 0.8rem;" onchange="this.form.submit()">
                    <option value="">-- Semua Skema --</option>
                    @foreach($schemes as $s)
                        <option value="{{ $s->id }}" {{ $schemeId == $s->id ? 'selected' : '' }}>
                            {{ $s->scheme_code }} - {{ $s->scheme_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- LEMBAR LAPORAN RESMI (PRINTABLE DOCUMENT - A4 FORMAT)                     -->
<!-- ========================================================================= -->
<div class="report-paper">

    <!-- KOP SURAT RESMI LEMBAGA SERTIFIKASI PROFESI (Dinamis dari Database) -->
    <div class="report-header">
        <div class="kop-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 68px; height: 68px; object-fit: contain; background: transparent;">
        </div>
        <div class="kop-text">
            <h2>{{ $reportSetting->institution_name }}</h2>
            <h3>{{ $reportSetting->tuk_name }}</h3>
            <p>Sekretariat: {{ $reportSetting->address }}</p>
            <p>{{ $reportSetting->contact_info }}</p>
        </div>
    </div>

    <div class="kop-divider"></div>

    <!-- JUDUL DOKUMEN & IDENTITAS BERITA ACARA -->
    <div style="text-align: center; margin: 1.75rem 0 1.25rem;">
        <h3 style="font-size: 1.15rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #0f172a;">
            {{ $reportSetting->document_title }}
        </h3>
        <p style="font-size: 0.8rem; font-family: var(--font-mono); color: #475569; margin-top: 0.25rem;">
            Nomor Dokumen: <b>{{ $reportSetting->document_number }}</b>
        </p>
    </div>

    <!-- INFORMASI UMUM ASESMEN -->
    <div style="margin-bottom: 1.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem 1.25rem;">
        <table style="width: 100%; font-size: 0.825rem; border-collapse: collapse;">
            <tr>
                <td style="width: 180px; padding: 0.25rem 0; color: #64748b; font-weight: 600;">Tempat Uji Kompetensi (TUK)</td>
                <td style="width: 15px; padding: 0.25rem 0;">:</td>
                <td style="padding: 0.25rem 0; font-weight: 700; color: #0f172a;">{{ $reportSetting->tuk_location }}</td>
                <td style="width: 160px; padding: 0.25rem 0; color: #64748b; font-weight: 600;">Tanggal Pelaksanaan</td>
                <td style="width: 15px; padding: 0.25rem 0;">:</td>
                <td style="padding: 0.25rem 0; font-weight: 700; color: #0f172a;">
                    {{ $reportSetting->assessment_date ? $reportSetting->assessment_date->format('d F Y') : date('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 0.25rem 0; color: #64748b; font-weight: 600;">Skema Sertifikasi</td>
                <td style="padding: 0.25rem 0;">:</td>
                <td style="padding: 0.25rem 0; font-weight: 700; color: #0f172a;">
                    {{ $selectedScheme ? $selectedScheme->scheme_code . ' - ' . $selectedScheme->scheme_name : 'Semua Skema Sertifikasi LSP' }}
                </td>
                <td style="padding: 0.25rem 0; color: #64748b; font-weight: 600;">Metode Asesmen</td>
                <td style="padding: 0.25rem 0;">:</td>
                <td style="padding: 0.25rem 0; font-weight: 700; color: #0f172a;">{{ $reportSetting->assessment_method }}</td>
            </tr>
        </table>
    </div>

    <!-- 1. RINGKASAN EKSEKUTIF / STATISTIK CAPAIAN -->
    <div style="margin-bottom: 1.75rem;">
        <h4 style="font-size: 0.925rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem; text-transform: uppercase; border-left: 3px solid var(--accent-primary); padding-left: 0.5rem;">
            I. Rekapitulasi Capaian Kelulusan Asesi
        </h4>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
            <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.85rem; text-align: center; background: #ffffff;">
                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Asesi Terdaftar</div>
                <div style="font-size: 1.65rem; font-weight: 800; font-family: var(--font-mono); color: #0f172a; margin-top: 0.25rem;">{{ $totalParticipants }}</div>
                <div style="font-size: 0.7rem; color: #64748b;">Peserta Terdata</div>
            </div>

            <div style="border: 1px solid #86efac; border-radius: 6px; padding: 0.85rem; text-align: center; background: #f0fdf4;">
                <div style="font-size: 0.75rem; font-weight: 600; color: #15803d; text-transform: uppercase;">Rekomendasi Kompeten (K)</div>
                <div style="font-size: 1.65rem; font-weight: 800; font-family: var(--font-mono); color: #15803d; margin-top: 0.25rem;">{{ $totalKompeten }}</div>
                <div style="font-size: 0.7rem; color: #16a34a;">Memenuhi Standar Uji</div>
            </div>

            <div style="border: 1px solid #fde68a; border-radius: 6px; padding: 0.85rem; text-align: center; background: #fffbeb;">
                <div style="font-size: 0.75rem; font-weight: 600; color: #b45309; text-transform: uppercase;">Belum Kompeten (BK)</div>
                <div style="font-size: 1.65rem; font-weight: 800; font-family: var(--font-mono); color: #b45309; margin-top: 0.25rem;">{{ $totalBelum }}</div>
                <div style="font-size: 0.7rem; color: #d97706;">Perlu Tindak Lanjut / Remidi</div>
            </div>

            <div style="border: 1px solid var(--accent-primary-border); border-radius: 6px; padding: 0.85rem; text-align: center; background: var(--accent-primary-light);">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--accent-primary); text-transform: uppercase;">Tingkat Kelulusan</div>
                <div style="font-size: 1.65rem; font-weight: 800; font-family: var(--font-mono); color: var(--accent-primary); margin-top: 0.25rem;">{{ $passRate }}%</div>
                <div style="font-size: 0.7rem; color: var(--text-muted);">Persentase Kompeten</div>
            </div>
        </div>
    </div>

    <!-- 2. REKAPITULASI PER SKEMA SERTIFIKASI -->
    @if(!$selectedScheme)
    <div style="margin-bottom: 1.75rem;">
        <h4 style="font-size: 0.925rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem; text-transform: uppercase; border-left: 3px solid var(--accent-primary); padding-left: 0.5rem;">
            II. Ringkasan Hasil Uji Tiap Skema Sertifikasi
        </h4>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th style="width: 120px;">Kode Skema</th>
                    <th>Nama Skema Sertifikasi</th>
                    <th style="width: 90px; text-align: center;">Total Asesi</th>
                    <th style="width: 90px; text-align: center;">Kompeten</th>
                    <th style="width: 90px; text-align: center;">Belum Komp.</th>
                    <th style="width: 100px; text-align: center;">% Capaian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schemes as $idx => $s)
                    <tr>
                        <td style="text-align: center; font-family: var(--font-mono);">{{ $idx + 1 }}</td>
                        <td style="font-family: var(--font-mono); font-weight: 700;">{{ $s->scheme_code }}</td>
                        <td style="font-weight: 600;">{{ $s->scheme_name }}</td>
                        <td style="text-align: center; font-family: var(--font-mono); font-weight: 700;">{{ $s->total }}</td>
                        <td style="text-align: center; font-family: var(--font-mono); color: #15803d; font-weight: 700;">{{ $s->kompeten }}</td>
                        <td style="text-align: center; font-family: var(--font-mono); color: #b45309; font-weight: 700;">{{ $s->belum }}</td>
                        <td style="text-align: center; font-family: var(--font-mono); font-weight: 700;">
                            <span style="display: inline-block; padding: 0.15rem 0.45rem; border-radius: 4px; background: {{ $s->rate >= 70 ? '#dcfce7; color: #166534;' : '#fee2e2; color: #991b1b;' }}">
                                {{ $s->rate }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- 3. RINCIAN TABEL SELURUH ASESI / PESERTA -->
    <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.925rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem; text-transform: uppercase; border-left: 3px solid var(--accent-primary); padding-left: 0.5rem;">
            {{ $selectedScheme ? 'II.' : 'III.' }} Daftar Rincian Penilaian Hasil Uji Peserta
        </h4>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th style="width: 120px;">No. Registrasi / NIK</th>
                    <th>Nama Lengkap Asesi</th>
                    <th style="width: 170px;">Kontak (Email / No. HP)</th>
                    <th style="width: 160px;">Skema Sertifikasi</th>
                    <th style="width: 130px; text-align: center;">Hasil Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $idx => $p)
                    <tr>
                        <td style="text-align: center; font-family: var(--font-mono);">{{ $idx + 1 }}</td>
                        <td style="font-family: var(--font-mono); font-weight: 700;">{{ $p->registration_number }}</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">{{ $p->full_name }}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">{{ $p->address ?: '-' }}</div>
                        </td>
                        <td style="font-size: 0.775rem;">
                            <div>{{ $p->email }}</div>
                            <div style="font-family: var(--font-mono); color: #64748b;">{{ $p->phone_number }}</div>
                        </td>
                        <td style="font-size: 0.8rem; font-weight: 500;">
                            {{ $p->scheme->scheme_name ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            @if(str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum'))
                                <span class="print-badge print-badge-k">KOMPETEN (K)</span>
                            @elseif(str_contains($p->status, 'Pending'))
                                <span class="print-badge" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;">PENDING (PROSES)</span>
                            @else
                                <span class="print-badge print-badge-bk">BELUM KOMPETEN (BK)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 2rem;">
                            Tidak ada data peserta untuk ditampilkan pada laporan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 4. LEMBAR PENGESAHAN & TANDA TANGAN ASESOR (Dinamis dari Database) -->
    <div class="signature-section" style="page-break-inside: avoid; margin-top: 2.5rem;">
        <p style="font-size: 0.825rem; color: #334155; margin-bottom: 1.5rem; line-height: 1.6;">
            {{ $reportSetting->closing_statement }}
        </p>

        <div style="display: flex; justify-content: space-between; align-items: flex-start; text-align: center;">
            <div style="width: 280px;">
                <p style="font-size: 0.825rem; color: #64748b; margin-bottom: 0.25rem;">Mengetahui,</p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #0f172a;">{{ $reportSetting->signer1_role }}</p>
                <div style="height: 70px;"></div>
                <p style="font-size: 0.875rem; font-weight: 700; text-decoration: underline; color: #0f172a;">{{ $reportSetting->signer1_name }}</p>
                <p style="font-size: 0.775rem; color: #64748b; font-family: var(--font-mono);">{{ $reportSetting->signer1_reg_number ?: '-' }}</p>
            </div>

            <div style="width: 280px;">
                <p style="font-size: 0.825rem; color: #64748b; margin-bottom: 0.25rem;">
                    {{ $reportSetting->signer2_city }}, {{ $reportSetting->assessment_date ? $reportSetting->assessment_date->format('d F Y') : date('d F Y') }}
                </p>
                <p style="font-size: 0.85rem; font-weight: 700; color: #0f172a;">{{ $reportSetting->signer2_role }}</p>
                <div style="height: 70px;"></div>
                <p style="font-size: 0.875rem; font-weight: 700; text-decoration: underline; color: #0f172a;">{{ $reportSetting->signer2_name }}</p>
                <p style="font-size: 0.775rem; color: #64748b; font-family: var(--font-mono);">{{ $reportSetting->signer2_reg_number ?: '-' }}</p>
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL: FORM CRUD PENGATURAN KONTEN CETAK LAPORAN (CREATE & UPDATE)        -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="settingModal">
    <div class="modal-box" style="max-width: 720px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 class="modal-title" id="settingModalTitle" style="margin-bottom: 0;">Edit Isi Laporan Cetak</h3>
            <button type="button" onclick="closeSettingModal()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form id="settingForm" method="POST" action="">
            @csrf
            <div id="settingMethodContainer"></div>

            <!-- Nama Template -->
            <div class="form-group">
                <label class="form-label">Nama Template Konfigurasi</label>
                <input type="text" name="template_name" id="inp_template_name" class="form-control" placeholder="Misal: Standar LSP USK 2026" required>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-subtle); margin: 1.25rem 0;">

            <!-- Bagian 1: KOP SURAT -->
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.75rem;">1. IDENTITAS KOP SURAT RESMI</h4>
            <div class="form-group">
                <label class="form-label">Nama Lembaga (Header 1)</label>
                <input type="text" name="institution_name" id="inp_institution_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama TUK (Header 2)</label>
                <input type="text" name="tuk_name" id="inp_tuk_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat / Sekretariat</label>
                <textarea name="address" id="inp_address" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Kontak (Email, Web, Telp)</label>
                <input type="text" name="contact_info" id="inp_contact_info" class="form-control" required>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-subtle); margin: 1.25rem 0;">

            <!-- Bagian 2: INFORMASI DOKUMEN & ASESMEN -->
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.75rem;">2. DATA BERITA ACARA & UJI KOMPETENSI</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Judul Dokumen</label>
                    <input type="text" name="document_title" id="inp_document_title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Dokumen Berita Acara</label>
                    <input type="text" name="document_number" id="inp_document_number" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Tempat Uji Kompetensi (TUK)</label>
                    <input type="text" name="tuk_location" id="inp_tuk_location" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Pelaksanaan Uji</label>
                    <input type="date" name="assessment_date" id="inp_assessment_date" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Metode Asesmen</label>
                <input type="text" name="assessment_method" id="inp_assessment_method" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Pernyataan Penutup Berita Acara</label>
                <textarea name="closing_statement" id="inp_closing_statement" class="form-control" rows="2" required></textarea>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-subtle); margin: 1.25rem 0;">

            <!-- Bagian 3: LEMBAR PENGESAHAN & TANDA TANGAN -->
            <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.75rem;">3. PENGESAHAN & PENANDATANGAN</h4>

            <!-- Penandatangan 1 (Pimpinan) -->
            <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
                <div style="font-weight: 700; font-size: 0.8rem; color: #0f172a; margin-bottom: 0.5rem;">Pihak Mengetahui (Kiri / Pimpinan)</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="signer1_role" id="inp_signer1_role" class="form-control" placeholder="Ketua / Direktur LSP" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label">Nama Lengkap &amp; Gelar</label>
                        <input type="text" name="signer1_name" id="inp_signer1_name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">No. Registrasi / MET</label>
                    <input type="text" name="signer1_reg_number" id="inp_signer1_reg_number" class="form-control" placeholder="MET.000.xxxxxx.20xx">
                </div>
            </div>

            <!-- Penandatangan 2 (Asesor) -->
            <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 1rem; margin-bottom: 1rem;">
                <div style="font-weight: 700; font-size: 0.8rem; color: #0f172a; margin-bottom: 0.5rem;">Pihak Asesor Penguji (Kanan)</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label">Kota Tempat TTD</label>
                        <input type="text" name="signer2_city" id="inp_signer2_city" class="form-control" placeholder="Banda Aceh" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label">Jabatan Penguji</label>
                        <input type="text" name="signer2_role" id="inp_signer2_role" class="form-control" placeholder="Asesor Penguji Kompetensi" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Lengkap &amp; Gelar Asesor</label>
                        <input type="text" name="signer2_name" id="inp_signer2_name" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">No. Registrasi / No. MET Asesor</label>
                        <input type="text" name="signer2_reg_number" id="inp_signer2_reg_number" class="form-control" placeholder="MET.000.xxxxxx.20xx">
                    </div>
                </div>
            </div>

            <!-- Set as Active Checkbox -->
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" name="set_active" id="inp_set_active" value="1" checked>
                <label for="inp_set_active" style="font-size: 0.825rem; font-weight: 600; color: var(--text-primary); cursor: pointer;">
                    Jadikan template ini sebagai template aktif utama
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeSettingModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitSetting">Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Tersembunyi untuk Hapus Template (CRUD: DELETE) -->
<form id="deleteSettingForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Inisialisasi Elemen Modal Konfigurasi Dokumen
    const settingModal = document.getElementById('settingModal');
    const settingForm = document.getElementById('settingForm');
    const settingModalTitle = document.getElementById('settingModalTitle');
    const settingMethodContainer = document.getElementById('settingMethodContainer');
    const btnSubmitSetting = document.getElementById('btnSubmitSetting');

    // Data konfigurasi saat ini dari server
    const currentSetting = @json($reportSetting);

    /**
     * FUNGSI: openEditSettingModal()
     * UNTUK APA: Membuka modal popup untuk mengedit teks isi dokumen cetak laporan yang sedang aktif (CRUD: UPDATE).
     */
    function openEditSettingModal() {
        settingModalTitle.innerText = 'Edit Konfigurasi Dokumen Cetak';
        settingForm.action = `/lsp/report/settings/${currentSetting.id}`;
        settingMethodContainer.innerHTML = '@method("PUT")';
        btnSubmitSetting.innerText = 'Perbarui Isi Dokumen';

        // Isi form dengan data terkini
        document.getElementById('inp_template_name').value = currentSetting.template_name || '';
        document.getElementById('inp_institution_name').value = currentSetting.institution_name || '';
        document.getElementById('inp_tuk_name').value = currentSetting.tuk_name || '';
        document.getElementById('inp_address').value = currentSetting.address || '';
        document.getElementById('inp_contact_info').value = currentSetting.contact_info || '';
        document.getElementById('inp_document_title').value = currentSetting.document_title || '';
        document.getElementById('inp_document_number').value = currentSetting.document_number || '';
        document.getElementById('inp_tuk_location').value = currentSetting.tuk_location || '';
        document.getElementById('inp_assessment_date').value = currentSetting.assessment_date ? currentSetting.assessment_date.substring(0, 10) : '';
        document.getElementById('inp_assessment_method').value = currentSetting.assessment_method || '';
        document.getElementById('inp_closing_statement').value = currentSetting.closing_statement || '';
        document.getElementById('inp_signer1_role').value = currentSetting.signer1_role || '';
        document.getElementById('inp_signer1_name').value = currentSetting.signer1_name || '';
        document.getElementById('inp_signer1_reg_number').value = currentSetting.signer1_reg_number || '';
        document.getElementById('inp_signer2_city').value = currentSetting.signer2_city || '';
        document.getElementById('inp_signer2_role').value = currentSetting.signer2_role || '';
        document.getElementById('inp_signer2_name').value = currentSetting.signer2_name || '';
        document.getElementById('inp_signer2_reg_number').value = currentSetting.signer2_reg_number || '';
        document.getElementById('inp_set_active').checked = true;

        settingModal.classList.add('active');
    }

    /**
     * FUNGSI: openCreateSettingModal()
     * UNTUK APA: Membuka modal formulir untuk membuat profil/template isi dokumen cetak baru (CRUD: CREATE).
     */
    function openCreateSettingModal() {
        settingModalTitle.innerText = 'Buat Template Dokumen Baru';
        settingForm.action = "{{ route('lsp.report.settings.store') }}";
        settingMethodContainer.innerHTML = '';
        btnSubmitSetting.innerText = 'Simpan Template Baru';

        // Set nilai template baru dengan default bersih
        document.getElementById('inp_template_name').value = 'Template Baru ' + new Date().getFullYear();
        document.getElementById('inp_institution_name').value = currentSetting.institution_name || '';
        document.getElementById('inp_tuk_name').value = currentSetting.tuk_name || '';
        document.getElementById('inp_address').value = currentSetting.address || '';
        document.getElementById('inp_contact_info').value = currentSetting.contact_info || '';
        document.getElementById('inp_document_title').value = currentSetting.document_title || '';
        document.getElementById('inp_document_number').value = 'BA-USK/' + new Date().getFullYear() + '/LSP-TI/002';
        document.getElementById('inp_tuk_location').value = currentSetting.tuk_location || '';
        document.getElementById('inp_assessment_date').value = new Date().toISOString().substring(0, 10);
        document.getElementById('inp_assessment_method').value = 'Demonstrasi Praktik & Tes Tertulis';
        document.getElementById('inp_closing_statement').value = currentSetting.closing_statement || '';
        document.getElementById('inp_signer1_role').value = currentSetting.signer1_role || '';
        document.getElementById('inp_signer1_name').value = currentSetting.signer1_name || '';
        document.getElementById('inp_signer1_reg_number').value = currentSetting.signer1_reg_number || '';
        document.getElementById('inp_signer2_city').value = currentSetting.signer2_city || 'Banda Aceh';
        document.getElementById('inp_signer2_role').value = currentSetting.signer2_role || 'Asesor Penguji Kompetensi';
        document.getElementById('inp_signer2_name').value = '';
        document.getElementById('inp_signer2_reg_number').value = '';
        document.getElementById('inp_set_active').checked = true;

        settingModal.classList.add('active');
    }

    /**
     * FUNGSI: closeSettingModal()
     * UNTUK APA: Menutup jendela modal konfigurasi dokumen.
     */
    function closeSettingModal() {
        settingModal.classList.remove('active');
    }

    settingModal.addEventListener('click', (e) => {
        if (e.target === settingModal) closeSettingModal();
    });

    /**
     * FUNGSI: confirmDeleteSetting()
     * UNTUK APA: Menampilkan dialog konfirmasi sebelum menghapus template laporan tertentu (CRUD: DELETE).
     */
    function confirmDeleteSetting(deleteUrl) {
        if (confirm('Apakah Anda yakin ingin menghapus template laporan ini?')) {
            const delForm = document.getElementById('deleteSettingForm');
            delForm.action = deleteUrl;
            delForm.submit();
        }
    }
</script>
@endpush

<!-- STYLING KHUSUS DOKUMEN CETAK (PRINT CSS) -->
<style>
    .report-paper {
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        padding: 3rem 3.5rem;
        box-shadow: var(--shadow-card);
        color: #0f172a;
        max-width: 1050px;
        margin: 0 auto;
    }

    .report-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .kop-text h2 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .kop-text h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #334155;
        margin-top: 0.15rem;
    }

    .kop-text p {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.15rem;
        line-height: 1.3;
    }

    .kop-divider {
        height: 3px;
        background: #0f172a;
        border-bottom: 1px solid #0f172a;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    /* Tabel Cetak */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.825rem;
    }

    .report-table th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 0.65rem 0.75rem;
        border: 1px solid #cbd5e1;
        font-size: 0.775rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .report-table td {
        padding: 0.65rem 0.75rem;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
        color: #1e293b;
    }

    .report-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .print-badge {
        display: inline-block;
        font-size: 0.725rem;
        font-weight: 800;
        padding: 0.2rem 0.55rem;
        border-radius: 4px;
        font-family: var(--font-mono);
    }

    .print-badge-k {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .print-badge-bk {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }

    /* ======================================================= */
    /* CSS MEDIA PRINT: AKTIF SAAT USER KLIK CETAK / PRINT PDF */
    /* ======================================================= */
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt;
        }

        /* Sembunyikan semua elemen navigasi aplikasi */
        .sidebar,
        .top-header,
        .no-print,
        aside,
        button {
            display: none !important;
        }

        .app-container {
            display: block !important;
            min-height: auto !important;
        }

        .main-content {
            padding: 0 !important;
            max-width: 100% !important;
            overflow: visible !important;
        }

        .report-paper {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            max-width: 100% !important;
            background: transparent !important;
        }

        .report-table th {
            background: #e2e8f0 !important;
            color: #000000 !important;
            border: 1px solid #64748b !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .report-table td {
            border: 1px solid #94a3b8 !important;
        }

        .print-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection
