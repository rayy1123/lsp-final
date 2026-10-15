@extends('layouts.astro')

@section('title', 'Data Peserta - LSP System')

@section('content')
<!-- Header Modul Peserta Sertifikasi -->
<div class="page-header">
    <div>
        <h2 class="page-title">Peserta Sertifikasi</h2>
        <p class="page-desc">Kelola administrasi data asesi dan status rekomendasi hasil uji.</p>
    </div>
</div>

<!-- Bar Pencarian & Filter Efisien -->
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <form method="GET" action="{{ route('lsp.participants') }}" style="display: flex; gap: 0.5rem; width: 100%; max-width: 480px;">
        <div style="position: relative; flex: 1;">
            <svg style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-dim);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" class="form-control" style="padding-left: 2.35rem;" placeholder="Cari nama, no. reg, skema..." value="{{ $search }}">
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if($search)
            <a href="{{ route('lsp.participants') }}" class="btn btn-secondary" style="color: var(--accent-rose);">Reset</a>
        @endif
    </form>
    <div style="font-size: 0.8rem; color: var(--text-muted);">
        Total: <b>{{ $participants->total() }}</b> Peserta Terdaftar
    </div>
</div>

<!-- Tabel Daftar Peserta -->
<div class="table-container">
    <table class="astro-table">
        <thead>
            <tr>
                <th style="width: 140px;">No. Registrasi</th>
                <th>Nama Peserta &amp; Alamat</th>
                <th>Kontak</th>
                <th>Skema Pilihan</th>
                <th style="width: 220px;">Status &amp; Metrik Tugas</th>
                <th style="width: 150px; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $p)
                <tr>
                    <td>
                        <span class="badge badge-code" style="font-weight: 700; color: var(--accent-primary);">{{ $p->registration_number }}</span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: var(--bg-surface-elevated); color: var(--text-primary); border: 1px solid var(--border-strong); display: grid; place-items: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; font-family: var(--font-mono);">
                                {{ strtoupper(substr($p->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                    <span>{{ $p->full_name }}</span>
                                    @if($p->gender)
                                        <span class="badge" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; background: var(--bg-surface-elevated); color: var(--text-muted); border: 1px solid var(--border-subtle);">
                                            {{ $p->gender }}
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">
                                    @if($p->nik) NIK: {{ $p->nik }} &bull; @endif {{ Str::limit($p->address ?: 'Alamat belum diisi', 35) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; color: var(--text-primary);">{{ $p->email }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">{{ $p->phone_number }}</div>
                    </td>
                    <td>
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                            {{ $p->scheme->scheme_name ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <div style="margin-bottom: 0.35rem;">
                            <span class="badge {{ str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum') ? 'badge-kompeten' : (str_contains($p->status, 'Pending') ? 'badge-pending' : 'badge-belum') }}">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                {{ $p->status }}
                            </span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.2rem;">
                            <span style="font-weight: 700; color: {{ $p->isEligibleForCompetence() ? 'var(--accent-emerald)' : '#64748b' }};">
                                {{ $p->acceptedTasksCount() }}/{{ $p->totalTasksCount() }}
                            </span>
                            <span>Tugas Selesai ({{ $p->taskProgressPercentage() }}%)</span>
                        </div>
                        @if($p->totalTasksCount() === 0)
                            <span style="display: inline-block; font-size: 0.675rem; color: #64748b; font-weight: 600;">
                                - Belum ada unit soal
                            </span>
                        @elseif($p->isEligibleForCompetence())
                            <span style="display: inline-block; font-size: 0.675rem; color: var(--accent-emerald); font-weight: 700;">
                                ✓ Syarat Kompeten Terpenuhi
                            </span>
                        @else
                            <span style="display: inline-block; font-size: 0.675rem; color: #b45309; font-weight: 600;">
                                ○ Sisa {{ $p->totalTasksCount() - $p->acceptedTasksCount() }} tugas belum diterima
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <button class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick='openEditModal(@json($p), {{ $p->totalTasksCount() }}, {{ $p->acceptedTasksCount() }}, {{ $p->isEligibleForCompetence() ? 1 : 0 }})'>
                            Edit
                        </button>
                        <form action="{{ route('lsp.participants.destroy', $p) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data peserta ini?')">
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
                    <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem; opacity: 0.5;">👥</div>
                        <div style="font-weight: 600;">Tidak ada data peserta yang ditemukan</div>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">Coba ubah kata kunci pencarian atau tunggu asesi mendaftar melalui portal.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Footer -->
    @if($participants->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-subtle); display: flex; justify-content: flex-end;">
            {{ $participants->links() }}
        </div>
    @endif
</div>

<!-- Modal Form Pendaftaran / Edit Peserta -->
<div class="modal-overlay" id="participantModal">
    <div class="modal-box" style="max-width: 580px;">
        <h3 class="modal-title" id="modalTitle">Daftarkan Peserta Baru</h3>
        <form id="participantForm" method="POST" action="{{ route('lsp.participants.store') }}">
            @csrf
            <div id="methodContainer"></div>

            <!-- Pemanggilan Data Akun User Terdaftar (Syarat Pendaftaran Skema) -->
            <div class="form-group" id="userSelectContainer" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label class="form-label" style="margin-bottom: 0; font-weight: 700; color: #1e293b;">
                        Pilih Akun User Terdaftar (Panggil Data Otomatis)
                    </label>
                    <span class="badge" style="background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border); font-size: 0.675rem;">Syarat Wajib</span>
                </div>
                <select id="user_select" class="form-select" onchange="autoFillFromUser(this)">
                    <option value="">-- Pilih Akun User untuk Isi Otomatis --</option>
                    @foreach($registeredUsers as $ru)
                        <option value="{{ $ru->id }}"
                                data-name="{{ $ru->name }}"
                                data-nik="{{ $ru->nik }}"
                                data-gender="{{ $ru->gender }}"
                                data-email="{{ $ru->email }}"
                                data-phone="{{ $ru->phone_number }}"
                                data-address="{{ $ru->address }}">
                            {{ $ru->name }} ({{ $ru->email }})
                        </option>
                    @endforeach
                </select>
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; line-height: 1.4;">
                    Syarat pendaftaran skema: Asesi wajib memiliki akun user terdaftar. Memilih akun akan mengisi otomatis Nama, NIK, Jenis Kelamin, Email, Nomor HP, dan Domisili.
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Skema Sertifikasi</label>
                    <select name="scheme_id" id="scheme_id" class="form-select" required>
                        <option value="">-- Pilih Skema --</option>
                        @foreach($schemes as $s)
                            <option value="{{ $s->id }}">{{ $s->scheme_code }} - {{ $s->scheme_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">No. Registrasi</label>
                    <input type="text" name="registration_number" id="registration_number" class="form-control" placeholder="MISAL: REG-2026-001" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap Asesi</label>
                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Nama lengkap peserta..." required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between;">
                        <span>NIK (KTP)</span>
                        <span style="font-size: 0.725rem; color: var(--text-muted);">Maks. 16 digit</span>
                    </label>
                    <input type="text" name="nik" id="nik" class="form-control" maxlength="16" placeholder="16 digit NIK...">
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-select">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Email Aktif</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="asesi@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>

            <!-- Indikator Metrik Tugas Ujian (Hanya Muncul di Edit Modal) -->
            <div id="taskMetricContainer" style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span style="font-size: 0.775rem; font-weight: 700; color: #1e293b;">Indikator Penyelesaian Tugas Ujian:</span>
                    <span id="taskMetricBadge" class="badge" style="font-size: 0.7rem;"></span>
                </div>
                <div style="background: #e2e8f0; border-radius: 999px; height: 6px; width: 100%; overflow: hidden; margin-bottom: 0.4rem;">
                    <div id="taskMetricBar" style="height: 100%; width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <div id="taskMetricNotice" style="font-size: 0.75rem; line-height: 1.4;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Status Hasil Asesmen</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="Pending" id="optPending">Pending (Menunggu Penilaian Asesor)</option>
                    <option value="Belum Kompeten" id="optBelum">Belum Kompeten (BK)</option>
                    <option value="Kompeten" id="optKompeten">Kompeten (K)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Domisili</label>
                <textarea name="address" id="address" class="form-control" rows="2" placeholder="Alamat domisili lengkap..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Peserta</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('participantModal');
    const form = document.getElementById('participantForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodContainer = document.getElementById('methodContainer');
    const userSelectContainer = document.getElementById('userSelectContainer');
    const userSelect = document.getElementById('user_select');

    function autoFillFromUser(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if (!opt || !opt.value) return;

        const name = opt.getAttribute('data-name');
        const nik = opt.getAttribute('data-nik');
        const gender = opt.getAttribute('data-gender');
        const email = opt.getAttribute('data-email');
        const phone = opt.getAttribute('data-phone');
        const address = opt.getAttribute('data-address');

        if (name) document.getElementById('full_name').value = name;
        if (nik) document.getElementById('nik').value = nik;
        if (gender) document.getElementById('gender').value = gender;
        if (email) document.getElementById('email').value = email;
        if (phone) document.getElementById('phone_number').value = phone;
        if (address) document.getElementById('address').value = address;
    }

    function openCreateModal() {
        modalTitle.innerText = 'Daftarkan Peserta Baru';
        form.action = "{{ route('lsp.participants.store') }}";
        methodContainer.innerHTML = '';
        if (userSelectContainer) userSelectContainer.style.display = 'block';
        if (userSelect) userSelect.value = '';
        document.getElementById('scheme_id').value = '';
        document.getElementById('registration_number').value = '{{ $nextRegNumber ?? "" }}';
        document.getElementById('full_name').value = '';
        document.getElementById('nik').value = '';
        document.getElementById('gender').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone_number').value = '';
        document.getElementById('status').value = 'Pending';
        document.getElementById('address').value = '';

        const taskMetricContainer = document.getElementById('taskMetricContainer');
        if (taskMetricContainer) taskMetricContainer.style.display = 'none';

        const optKompeten = document.getElementById('optKompeten');
        if (optKompeten) {
            optKompeten.disabled = true;
            optKompeten.text = 'Kompeten (Terkunci: Asesi Baru)';
        }

        modal.classList.add('active');
    }

    function openEditModal(p, totalTasks, acceptedTasks, isEligible) {
        modalTitle.innerText = 'Edit Data Peserta';
        form.action = `/lsp/participants/${p.id}`;
        methodContainer.innerHTML = '@method("PUT")';
        if (userSelectContainer) userSelectContainer.style.display = 'none';
        document.getElementById('scheme_id').value = p.scheme_id;
        document.getElementById('registration_number').value = p.registration_number;
        document.getElementById('full_name').value = p.full_name;
        document.getElementById('nik').value = p.nik || '';
        document.getElementById('gender').value = p.gender || '';
        document.getElementById('email').value = p.email;
        document.getElementById('phone_number').value = p.phone_number;
        document.getElementById('address').value = p.address || '';

        const taskMetricContainer = document.getElementById('taskMetricContainer');
        const metricBadge = document.getElementById('taskMetricBadge');
        const metricBar = document.getElementById('taskMetricBar');
        const metricNotice = document.getElementById('taskMetricNotice');
        const optKompeten = document.getElementById('optKompeten');
        const statusSelect = document.getElementById('status');

        if (taskMetricContainer) taskMetricContainer.style.display = 'block';

        const total = totalTasks !== undefined ? totalTasks : 0;
        const accepted = acceptedTasks !== undefined ? acceptedTasks : 0;
        const eligible = isEligible === 1 || isEligible === true;
        const pct = total > 0 ? Math.round((accepted / total) * 100) : 0;

        if (metricBar) metricBar.style.width = pct + '%';

        if (total === 0) {
            if (metricBadge) {
                metricBadge.className = 'badge';
                metricBadge.style.background = '#f1f5f9';
                metricBadge.style.color = '#64748b';
                metricBadge.innerText = '0 Soal Diberikan';
            }
            if (metricBar) metricBar.style.background = '#94a3b8';
            if (metricNotice) {
                metricNotice.style.color = '#64748b';
                metricNotice.innerHTML = 'Skema ini belum memiliki unit tugas ujian. Opsi Kompeten dapat dipilih.';
            }
            if (optKompeten) {
                optKompeten.disabled = false;
                optKompeten.text = 'Kompeten';
            }
            statusSelect.value = p.status;
        } else if (eligible) {
            if (metricBadge) {
                metricBadge.className = 'badge badge-kompeten';
                metricBadge.style.background = '';
                metricBadge.style.color = '';
                metricBadge.innerText = `${accepted}/${total} (100% Selesai)`;
            }
            if (metricBar) metricBar.style.background = '#059669';
            if (metricNotice) {
                metricNotice.style.color = '#059669';
                metricNotice.innerHTML = '<b>✓ Syarat Terpenuhi:</b> Seluruh unit tugas ujian telah berstatus Diterima oleh asesor. <b>Opsi Kompeten kini aktif dan dapat disetujui.</b>';
            }
            if (optKompeten) {
                optKompeten.disabled = false;
                optKompeten.text = 'Kompeten (Terbuka)';
            }
            statusSelect.value = p.status;
        } else {
            if (metricBadge) {
                metricBadge.className = 'badge badge-belum';
                metricBadge.style.background = '';
                metricBadge.style.color = '';
                metricBadge.innerText = `${accepted}/${total} Diterima (${pct}%)`;
            }
            if (metricBar) metricBar.style.background = '#f59e0b';
            if (metricNotice) {
                metricNotice.style.color = '#b45309';
                metricNotice.innerHTML = `<b>⚠ Opsi Kompeten Terkunci:</b> Asesi baru menyelesaikan ${accepted} dari ${total} unit tugas yang diberikan. Asesi wajib menyelesaikan dan menerima seluruh unit tugas sebelum opsi Kompeten dapat disetujui.`;
            }
            if (optKompeten) {
                optKompeten.disabled = true;
                optKompeten.text = 'Kompeten (Terkunci: Tugas Belum Lengkap)';
            }
            // Karena belum memenuhi syarat kompeten, jika status sebelumnya Kompeten ubah ke Pending; selain itu pertahankan status saat ini (Pending atau Belum Kompeten)
            statusSelect.value = (p.status === 'Kompeten') ? 'Pending' : p.status;
        }

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
@endsection
