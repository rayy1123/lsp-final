@extends('layouts.astro')

@section('title', 'Skema Sertifikasi - LSP System')

@section('content')
<!-- Header Modul Skema Sertifikasi -->
<div class="page-header">
    <div>
        <h2 class="page-title">Skema Sertifikasi</h2>
        <p class="page-desc">{{ Auth::user()->isAdmin() ? 'Master data skema standar kompetensi kerja nasional LSP & validasi relasi data asesi.' : 'Katalog skema sertifikasi standar kompetensi kerja nasional LSP.' }}</p>
    </div>
    @if(Auth::user()->isAdmin())
        <button class="btn btn-primary" onclick="openCreateModal()">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Skema Baru
        </button>
    @endif
</div>

@if(!Auth::user()->isAdmin() && $currentScheme)
    <!-- Banner Informasi Skema Aktif yang Sedang Dikerjakan Asesi -->
    <div style="background: var(--bg-surface); border: 1px solid var(--accent-primary-border); border-left: 4px solid var(--accent-primary); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-card);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: var(--accent-primary-light); color: var(--accent-primary); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; border: 1px solid var(--accent-primary-border);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--accent-primary);"></span>
                    SKEMA AKTIF YANG SEDANG DIKERJAKAN
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.25rem;">
                    {{ $currentScheme->scheme_name }}
                </h3>
                <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                    <span>Kode Skema: <b style="font-family: var(--font-mono); color: var(--accent-primary);">{{ $currentScheme->scheme_code }}</b></span>
                    <span>&bull;</span>
                    <span>No. Registrasi: <b style="font-family: var(--font-mono); color: var(--text-primary);">{{ $myParticipant->registration_number ?? '-' }}</b></span>
                    <span>&bull;</span>
                    <span>Status: <b style="color: {{ str_contains($myParticipant->status, 'Kompeten') && !str_contains($myParticipant->status, 'Belum') ? 'var(--accent-emerald)' : (str_contains($myParticipant->status, 'Pending') ? 'var(--accent-sky)' : '#b45309') }};">{{ $myParticipant->status }}</b></span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; max-width: 680px; margin: 0;">
                    {{ $currentScheme->description ?: 'Standar kompetensi kerja nasional resmi.' }}
                </p>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('lsp.dashboard') }}#tasksSection" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.45rem 0.85rem;">
                    Kerjakan Tugas Ujian &rarr;
                </a>
                <a href="{{ route('lsp.assessment.result') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.45rem 0.85rem;">
                    Hasil Asesmen
                </a>
            </div>
        </div>
    </div>

    <!-- Header Section Pilihan Skema Lainnya -->
    <div style="margin-bottom: 1.25rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
            Pilihan Skema Sertifikasi Lainnya yang Tersedia
        </h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
            Daftar di bawah ini mengecualikan skema <b>{{ $currentScheme->scheme_name }}</b> yang sedang aktif Anda ikuti.
        </p>
    </div>
@elseif(!Auth::user()->isAdmin() && !$currentScheme)
    <!-- Info Asesi Belum Memilih Skema -->
    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--text-secondary);">
        <b style="color: var(--text-primary);">Pemberitahuan:</b> Anda belum terdaftar pada skema sertifikasi apapun. Silakan tentukan dan pilih salah satu skema di bawah ini untuk memulai pendaftaran uji kompetensi.
    </div>
@endif

<!-- Bar Pencarian -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <form method="GET" action="{{ route('lsp.schemes') }}" style="display: flex; gap: 0.5rem; width: 100%; max-width: 520px;">
        <div style="position: relative; flex: 1;">
            <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-dim);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" class="form-control" style="padding-left: 2.35rem;" placeholder="{{ Auth::user()->isAdmin() ? 'Cari kode, nama skema, atau nama/no. reg asesi...' : 'Cari kode atau nama skema sertifikasi...' }}" value="{{ $search ?? '' }}">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if(!empty($search))
            <a href="{{ route('lsp.schemes') }}" class="btn btn-secondary" style="color: var(--accent-rose);">Reset</a>
        @endif
    </form>
    <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent-primary);"></span>
        <span>{{ (!Auth::user()->isAdmin() && $currentScheme) ? 'Tersedia: ' : 'Ditemukan: ' }}<b>{{ count($schemes) }}</b> {{ (!Auth::user()->isAdmin() && $currentScheme) ? 'Skema Lainnya' : 'Skema Sertifikasi' }}</span>
    </div>
</div>

<!-- Tabel Daftar Skema -->
<div class="table-container">
    <table class="astro-table">
        <thead>
            <tr>
                <th style="width: 140px;">Kode Skema</th>
                <th style="width: 250px;">Nama Skema Sertifikasi</th>
                <th>Deskripsi Standar</th>
                @if(Auth::user()->isAdmin())
                    <th style="width: 150px; text-align: center;">Jumlah Peserta</th>
                @endif
                <th style="width: 150px; text-align: right;">{{ Auth::user()->isAdmin() ? 'Aksi Admin' : 'Status / Aksi' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schemes as $scheme)
                <tr>
                    <td>
                        <span class="badge badge-code" style="font-weight: 700; color: var(--accent-primary);">{{ $scheme->scheme_code }}</span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text-primary);">{{ $scheme->scheme_name }}</div>
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4;">
                        {{ $scheme->description ?: '-' }}
                    </td>
                    @if(Auth::user()->isAdmin())
                        <td style="text-align: center;">
                            <span class="badge" style="background: #f1f5f9; color: var(--text-primary); border: 1px solid var(--border-subtle); font-size: 0.8rem; padding: 0.35rem 0.75rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.4rem;" title="{{ $scheme->participants_count ?? 0 }} asesi terdaftar pada skema ini">
                                <svg style="width: 14px; height: 14px; color: var(--accent-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span><b>{{ $scheme->participants_count ?? 0 }}</b> Asesi</span>
                            </span>
                        </td>
                    @endif
                    <td style="text-align: right;">
                        @if(Auth::user()->isAdmin())
                            <button class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick='openEditModal(@json($scheme))'>
                                Edit
                            </button>
                            <form action="{{ route('lsp.schemes.destroy', $scheme) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skema sertifikasi ini? Data peserta terkait akan ikut terhapus (Cascade).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                    Hapus
                                </button>
                            </form>
                        @else
                            <form action="{{ route('lsp.apply.scheme') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('{{ $currentScheme ? 'Perhatian: Anda saat ini terdaftar pada skema ' . $currentScheme->scheme_name . '. Apakah Anda yakin ingin beralih ke skema ' . $scheme->scheme_name . '? Data pendaftaran uji kompetensi Anda akan dialihkan.' : 'Konfirmasi pendaftaran ke skema ' . $scheme->scheme_name . '?' }}')">
                                @csrf
                                <input type="hidden" name="scheme_id" value="{{ $scheme->id }}">
                                <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                    {{ $currentScheme ? 'Ganti ke Skema Ini' : 'Pilih Skema' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Auth::user()->isAdmin() ? 5 : 4 }}" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem; opacity: 0.5;">📁</div>
                        <div style="font-weight: 600;">{{ (!Auth::user()->isAdmin() && $currentScheme) ? 'Tidak ada skema sertifikasi lain yang tersedia' : 'Tidak ada skema sertifikasi yang cocok' }}</div>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">
                            {{ Auth::user()->isAdmin() ? 'Coba ubah kata kunci pencarian atau klik tombol "Tambah Skema Baru".' : 'Seluruh skema sertifikasi telah Anda ikuti atau sesuai filter pencarian.' }}
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(Auth::user()->isAdmin())
<!-- ========================================================================= -->
<!-- MODAL: FORM TAMBAH / EDIT SKEMA SERTIFIKASI (ADMIN ONLY)                  -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="schemeModal">
    <div class="modal-box">
        <h3 class="modal-title" id="modalTitle">Tambah Skema Baru</h3>
        <form id="schemeForm" method="POST" action="{{ route('lsp.schemes.store') }}">
            @csrf
            <div id="methodContainer"></div>

            <div class="form-group">
                <label class="form-label">Kode Skema (Unik)</label>
                <input type="text" name="scheme_code" id="scheme_code" class="form-control" placeholder="Contoh: JWD-2026" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Skema Sertifikasi</label>
                <input type="text" name="scheme_name" id="scheme_name" class="form-control" placeholder="Contoh: Junior Web Developer" required>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Skema</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Uraian ringkas okupasi atau unit kompetensi..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Skema</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ==========================================
    // MODAL FORM SKEMA (CREATE & EDIT)
    // ==========================================
    const modal = document.getElementById('schemeModal');
    const form = document.getElementById('schemeForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodContainer = document.getElementById('methodContainer');

    function openCreateModal() {
        modalTitle.innerText = 'Tambah Skema Baru';
        form.action = "{{ route('lsp.schemes.store') }}";
        methodContainer.innerHTML = '';
        document.getElementById('scheme_code').value = '';
        document.getElementById('scheme_name').value = '';
        document.getElementById('description').value = '';
        modal.classList.add('active');
    }

    function openEditModal(scheme) {
        modalTitle.innerText = 'Edit Skema Sertifikasi';
        form.action = `/lsp/schemes/${scheme.id}`;
        methodContainer.innerHTML = '@method("PUT")';
        document.getElementById('scheme_code').value = scheme.scheme_code;
        document.getElementById('scheme_name').value = scheme.scheme_name;
        document.getElementById('description').value = scheme.description || '';
        modal.classList.add('active');
    }

    function closeModal() {
        modal.classList.remove('active');
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
</script>
@endpush
@endif
@endsection
