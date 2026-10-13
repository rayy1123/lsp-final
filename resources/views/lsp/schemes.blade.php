@extends('layouts.astro')

@section('title', 'Skema Sertifikasi - LSP System')

@section('content')
<!-- Header Modul Skema Sertifikasi -->
<div class="page-header">
    <div>
        <h2 class="page-title">Skema Sertifikasi</h2>
        <p class="page-desc">Master data skema standar kompetensi kerja nasional LSP &amp; validasi relasi data asesi.</p>
    </div>
    <button class="btn btn-primary" onclick="openCreateModal()">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Skema Baru
    </button>
</div>

<!-- Bar Pencarian & Validasi Relasi Data -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <form method="GET" action="{{ route('lsp.schemes') }}" style="display: flex; gap: 0.5rem; width: 100%; max-width: 520px;">
        <div style="position: relative; flex: 1;">
            <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-dim);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" class="form-control" style="padding-left: 2.35rem;" placeholder="Cari kode, nama skema, atau nama/no. reg asesi..." value="{{ $search ?? '' }}">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if(!empty($search))
            <a href="{{ route('lsp.schemes') }}" class="btn btn-secondary" style="color: var(--accent-rose);">Reset</a>
        @endif
    </form>
    <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #4f46e5;"></span>
        <span>Ditemukan: <b>{{ count($schemes) }}</b> Skema Sertifikasi</span>
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
                <th style="width: 150px; text-align: center;">Relasi Peserta (1:N)</th>
                <th style="width: 150px; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schemes as $scheme)
                <tr>
                    <td>
                        <span class="badge badge-code" style="font-weight: 700; color: #4338ca;">{{ $scheme->scheme_code }}</span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--text-primary);">{{ $scheme->scheme_name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim); font-family: var(--font-mono);">PK ID: #{{ $scheme->id }}</div>
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4;">
                        {{ $scheme->description ?: '-' }}
                    </td>
                    <td style="text-align: center;">
                        <!-- Tombol Validasi Hubungan Relasi Data (One-to-Many) -->
                        <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem; border-radius: 9999px; gap: 0.4rem; background: #f8fafc;" onclick='openRelationModal(@json($scheme))' title="Klik untuk validasi data relasi asesi">
                            <svg style="width: 14px; height: 14px; color: #4f46e5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                            </svg>
                            <span><b>{{ $scheme->participants_count }}</b> Asesi</span>
                        </button>
                    </td>
                    <td style="text-align: right;">
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
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem; opacity: 0.5;">📁</div>
                        <div style="font-weight: 600;">Tidak ada skema sertifikasi yang cocok</div>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">Coba ubah kata kunci pencarian atau klik tombol "Tambah Skema Baru".</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ========================================================================= -->
<!-- MODAL: FORM TAMBAH / EDIT SKEMA SERTIFIKASI                               -->
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

<!-- ========================================================================= -->
<!-- MODAL: VALIDASI HUBUNGAN RELASI DATA (ONE-TO-MANY)                        -->
<!-- ========================================================================= -->
<div class="modal-overlay" id="relationModal">
    <div class="modal-box" style="max-width: 680px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div>
                <h3 class="modal-title" style="margin-bottom: 0.25rem;">
                    <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    Validasi Hubungan Relasi Data (One-to-Many)
                </h3>
                <p style="font-size: 0.8rem; color: var(--text-muted);">
                    Integritas relasi antara <code>certification_schemes</code> dan <code>participants</code>.
                </p>
            </div>
            <button type="button" onclick="closeRelationModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <!-- Box Visualisasi Arsitektur Relasi Database -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; font-size: 0.825rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-weight: 700; color: #0f172a;" id="relSchemeTitle">Skema</span>
                <span class="badge badge-code" id="relSchemeCode">KODE</span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 0.775rem; color: #4338ca; background: #eef2ff; padding: 0.4rem 0.6rem; border-radius: 4px; border: 1px solid #c7d2fe;">
                certification_schemes (PK: id = <span id="relSchemeId">1</span>) ───[ 1 : N ]───&gt; participants (FK: scheme_id = <span id="relFkId">1</span>)
            </div>
        </div>

        <!-- Tabel Daftar Asesi yang Terikat -->
        <div style="max-height: 280px; overflow-y: auto; border: 1px solid var(--border-subtle); border-radius: 8px; margin-bottom: 1.25rem;">
            <table class="astro-table" style="font-size: 0.825rem;">
                <thead>
                    <tr>
                        <th style="padding: 0.65rem 0.85rem;">No. Reg</th>
                        <th style="padding: 0.65rem 0.85rem;">Nama Asesi</th>
                        <th style="padding: 0.65rem 0.85rem;">Kontak</th>
                        <th style="padding: 0.65rem 0.85rem; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody id="relParticipantsBody">
                    <!-- Dynamic Rows Rendered by JS -->
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.8rem; color: var(--text-muted);" id="relSummaryText">
                Terverifikasi terikat presisi.
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeRelationModal()">Tutup</button>
                <a href="{{ route('lsp.participants') }}" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.45rem 0.85rem;">
                    Kelola di Data Peserta &rarr;
                </a>
            </div>
        </div>
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

    // ==========================================
    // MODAL VALIDASI RELASI DATA (ONE-TO-MANY)
    // ==========================================
    const relModal = document.getElementById('relationModal');
    const relSchemeTitle = document.getElementById('relSchemeTitle');
    const relSchemeCode = document.getElementById('relSchemeCode');
    const relSchemeId = document.getElementById('relSchemeId');
    const relFkId = document.getElementById('relFkId');
    const relParticipantsBody = document.getElementById('relParticipantsBody');
    const relSummaryText = document.getElementById('relSummaryText');

    function openRelationModal(scheme) {
        relSchemeTitle.innerText = scheme.scheme_name;
        relSchemeCode.innerText = scheme.scheme_code;
        relSchemeId.innerText = scheme.id;
        relFkId.innerText = scheme.id;

        const participants = scheme.participants || [];
        relParticipantsBody.innerHTML = '';

        if (participants.length === 0) {
            relParticipantsBody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        Belum ada peserta yang terikat pada skema ini (0 records).<br>
                        <span style="font-size: 0.75rem; color: var(--text-dim);">Foreign Key (scheme_id = ${scheme.id}) siap menerima pendaftaran data peserta baru.</span>
                    </td>
                </tr>
            `;
            relSummaryText.innerHTML = `Total: <b>0</b> Asesi terikat`;
        } else {
            participants.forEach(p => {
                const isKompeten = p.status.includes('Kompeten') && !p.status.includes('Belum');
                const badgeClass = isKompeten ? 'badge-kompeten' : 'badge-belum';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="badge badge-code">${p.registration_number}</span></td>
                    <td style="font-weight: 700; color: var(--text-primary);">${p.full_name}</td>
                    <td style="font-size: 0.775rem;">${p.email}<br><span style="color: var(--text-muted); font-family: var(--font-mono);">${p.phone_number}</span></td>
                    <td style="text-align: center;"><span class="badge ${badgeClass}">${p.status}</span></td>
                `;
                relParticipantsBody.appendChild(tr);
            });
            relSummaryText.innerHTML = `Validasi Sukses: <b>${participants.length}</b> Data Asesi terikat presisi ke skema ini.`;
        }

        relModal.classList.add('active');
    }

    function closeRelationModal() {
        relModal.classList.remove('active');
    }

    relModal.addEventListener('click', (e) => {
        if (e.target === relModal) closeRelationModal();
    });
</script>
@endpush
@endsection
