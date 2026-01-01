@extends('layouts.sarpras')

@section('title', 'Data Siswa')

@section('content')

<style>
/* ================= GLOBAL ================= */
.page-title {
    font-weight: 800;
    font-size: 1.5rem;
    margin-bottom: 16px;
}

.card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 28px rgba(0,0,0,.06);
}

.card-header {
    background: #f8fafc;
    font-weight: 700;
    padding: 14px 18px;
    border-bottom: 1px solid #eee;
}

.form-control,
.form-select {
    border-radius: 10px;
}



/* ================= TABLE RESPONSIVE ================= */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 14px;
}

.table {
    min-width: 750px;
    margin-bottom: 0;
}

.table th {
    background: #f3f6fb;
    font-weight: 700;
    white-space: nowrap;
    text-align: center;
}

.table td {
    vertical-align: middle;
}

.table td,
.table th {
    padding: 12px;
}

/* tombol aksi */
.action-btns {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* ================= SEARCH ================= */
.search-box {
    max-width: 320px;
}

/* ================= MOBILE ================= */
@media (max-width: 576px) {
    .page-title {
        font-size: 1.25rem;
        text-align: center;
    }

    .search-box {
        max-width: 100%;
    }

    .card-body {
        padding: 14px;
    }

    .btn {
        font-size: .85rem;
        padding: 6px 10px;
    }
}

/* ================= MODAL ================= */
.modal-content {
    border-radius: 16px;
}

.modal-header {
    border-bottom: 1px solid #eee;
}

.modal-footer {
    border-top: 1px solid #eee;
}
/* ===== FONT TABLE RESPONSIVE ===== */
.table {
    font-size: 0.95rem; /* desktop */
}

/* tablet */
@media (max-width: 992px) {
    .table {
        font-size: 0.9rem;
    }
}

/* hp besar */
@media (max-width: 768px) {
    .table {
        font-size: 0.85rem;
    }

    .table th,
    .table td {
        padding: 8px 10px;
    }
}

/* hp kecil */
@media (max-width: 480px) {
    .table {
        font-size: 0.8rem;
    }

    .table th,
    .table td {
        padding: 6px 8px;
    }

    .btn {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
}

</style>

<h4 class="page-title">Manajemen Siswa</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- FORM TAMBAH --}}
<div class="card mb-4">
    <div class="card-header">Tambah Siswa</div>
    <div class="card-body">
        <form method="POST" action="{{ route('sarpras.siswa.store') }}">
    @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <input name="nis" class="form-control" placeholder="NIS" required>
                </div>
                <div class="col-md-4">
                    <input name="nama" class="form-control" placeholder="Nama Siswa" required>
                </div>
                <div class="col-md-4">
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary mt-2">
                        + Tambah Siswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header">Import Siswa</div>
    <div class="card-body">


            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="file" name="file" id="fileExcel" class="form-control" accept=".xlsx,.xls">

                </div>

                <div class="col-md-6 d-flex gap-2">
  <button type="button" class="btn btn-primary" onclick="importSiswa()">
    Import
</button>

                            <button type="button" class="btn btn-primary" onclick="previewSiswa()">
            Preview
        </button>

                    <a href="{{ route('sarpras.siswa.template') }}"
                       class="btn btn-outline-secondary">
                        Download Template
                    </a>
                </div>
            </div>
        
    </div>
</div>

<div id="previewBox" class="mt-3 d-none">
    <h5>Preview Data</h5>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody id="previewBody"></tbody>
</table>



</div>

{{-- SEARCH --}}
<div class="row mb-3">
    <div class="col-md-4 search-box">
        <input id="searchInput" class="form-control"
               placeholder="Cari NIS / Nama / Kelas">
    </div>
</div>

{{-- TABLE --}}
<div class="table-wrapper">
<table class="table table-bordered align-middle" id="siswaTable">
    <thead>
        <tr>
            <th width="60">No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th width="160">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($siswa as $i => $s)
        <tr>
            <td class="text-center">{{ $siswa->firstItem() + $i }}</td>
            <td>{{ $s->nis }}</td>
            <td>{{ $s->nama }}</td>
            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
            <td>
                <div class="action-btns">
                    <button
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="{{ $s->id_siswa }}"
                        data-nis="{{ $s->nis }}"
                        data-nama="{{ $s->nama }}"
                        data-kelas="{{ $s->id_kelas }}"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditSiswa">
                        Edit
                    </button>

                    <form action="{{ route('sarpras.siswa.destroy',$s->id_siswa) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus siswa?')">
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-3">
    {{ $siswa->links('vendor.pagination.bootstrap-5pagination') }}

</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formEditSiswa"
              data-action-template="{{ route('sarpras.siswa.update', ':id') }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input id="edit_nis" name="nis" class="form-control mb-2" required>
                    <input id="edit_nama" name="nama" class="form-control mb-2" required>

                    <select id="edit_kelas" name="id_kelas" class="form-select">
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let confirmCallback = null;
let isConfirm = false;

/* ===== SHOW CONFIRM ===== */
function showConfirm(message, onYes) {
    isConfirm = true;
    confirmCallback = onYes;

    const overlay = document.getElementById('uiOverlay');

    document.getElementById('uiTitle').textContent = 'Konfirmasi';
    document.getElementById('uiMessage').textContent = message;

    document.getElementById('uiCancel').style.display = 'inline-block';
    document.getElementById('uiConfirm').textContent = 'Ya';

    overlay.classList.add('show');
}

/* ===== SHOW ALERT ===== */
function showAlert(message, type = 'success') {
    isConfirm = false;
    confirmCallback = null;

    const overlay = document.getElementById('uiOverlay');

    document.getElementById('uiTitle').textContent =
        type === 'error' ? 'Gagal' : 'Berhasil';

    document.getElementById('uiMessage').textContent = message;

    document.getElementById('uiCancel').style.display = 'none';
    document.getElementById('uiConfirm').textContent = 'OK';

    overlay.classList.add('show');
}

/* ===== CLOSE MODAL ===== */
function closeModal() {
    const overlay = document.getElementById('uiOverlay');
    overlay.classList.remove('show');

    setTimeout(() => {
        isConfirm = false;
        confirmCallback = null;
    }, 150);
}

/* tombol batal */
document.getElementById('uiCancel').onclick = closeModal;

/* tombol OK / YA */
document.getElementById('uiConfirm').onclick = function () {
    if (isConfirm && typeof confirmCallback === 'function') {
        confirmCallback();
    }
    closeModal();
};
</script>

{{-- SCRIPT --}}
<script>
let typingTimer;

document.getElementById('searchInput').addEventListener('keyup', function () {
    clearTimeout(typingTimer);
    const keyword = this.value;

    typingTimer = setTimeout(() => {
        fetch(`{{ route('sarpras.siswa.search') }}?q=${encodeURIComponent(keyword)}`)
            .then(res => res.json())
            .then(data => renderTable(data));
    }, 400);
});

function renderTable(data) {
    let html = '';

    if (data.length === 0) {
        html = `
        <tr>
            <td colspan="5" class="text-center text-muted">
                Data tidak ditemukan
            </td>
        </tr>`;
    }

    data.forEach((s, i) => {
        html += `
        <tr>
            <td class="text-center">${i + 1}</td>
            <td>${s.nis}</td>
            <td>${s.nama}</td>
            <td>${s.kelas ? s.kelas.nama_kelas : '-'}</td>
            <td>
                <div class="action-btns">
                    <button 
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="${s.id_siswa}"
                        data-nis="${s.nis}"
                        data-nama="${s.nama}"
                        data-kelas="${s.id_kelas}"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditSiswa">
                        Edit
                    </button>

                    <form action="{{ url('/sarpras/siswa') }}/${s.id_siswa}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus siswa?')">
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        `;
    });

    document.querySelector('#siswaTable tbody').innerHTML = html;
}
let previewData = [];

function previewSiswa() {
    const file = document.getElementById('fileExcel').files[0];
    if (!file) return alert('Pilih file terlebih dahulu');

    const formData = new FormData();
    formData.append('file', file);

    fetch("{{ route('sarpras.siswa.preview') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        previewData = data;

        const tbody = document.getElementById('previewBody');
        tbody.innerHTML = '';

        data.forEach(row => {
            const badge = row.status === 'ok'
                ? `<span class="badge bg-success">OK</span>`
                : `<span class="badge bg-danger">ERROR</span>`;

            tbody.innerHTML += `
                <tr>
                    <td>${row.nis}</td>
                    <td>${row.nama}</td>
                    <td>${row.kelas}</td>
                    <td>${badge}</td>
                    <td>${row.pesan}</td>
                </tr>
            `;
        });

        document.getElementById('previewBox').classList.remove('d-none');
    });
}



function importSiswa() {
    const file = document.getElementById('fileExcel').files[0];

    if (!file) {
        alert('Pilih file dulu');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    fetch("{{ route('sarpras.siswa.import') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(`Import selesai\nBerhasil: ${data.inserted}\nGagal: ${data.failed}`);

        // optional reload
        if (data.inserted > 0) {
            location.reload();
        }
    })
    .catch(err => {
        alert('Gagal import');
        console.error(err);
    });
}

</script>
<script>
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-edit')) {

        const id = e.target.dataset.id;
        const nis = e.target.dataset.nis;
        const nama = e.target.dataset.nama;
        const kelas = e.target.dataset.kelas;

        // isi field
        document.getElementById('edit_nis').value = nis;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_kelas').value = kelas;

        // SET ACTION FORM ✅
        const form = document.getElementById('formEditSiswa');
        form.action = `/sarpras/siswa/${id}`;
    }
});
</script>

@endsection
