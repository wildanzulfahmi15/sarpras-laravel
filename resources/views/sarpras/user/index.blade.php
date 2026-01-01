@extends('layouts.sarpras')

@section('title','Manajemen Akun')

@section('content')

<style>
/* ================= AKSI TABEL (AMAN, TIDAK GANGGU LAYOUT) ================= */
.table .aksi-wrap {
    display: inline-flex;
    gap: 6px;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

.table .aksi-wrap form {
    margin: 0;
}

.table .aksi-wrap button {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 6px;
    line-height: 1;
}

/* Mobile: sembunyikan teks, icon saja */
@media (max-width: 576px) {
    .table .aksi-wrap button span {
        display: none;
    }
}


</style>
<h4 class="mb-3">Manajemen Akun Guru & Sarpras</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- FORM TAMBAH --}}
<div class="card mb-4">
    <div class="card-header fw-bold">Tambah Akun</div>
    <div class="card-body">
        @if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

        <form method="POST" action="{{ route('sarpras.user.store') }}">
            @csrf
            <div class="row g-3">

                <div class="col-md-4">
                    <label>Nama</label>
                    <input name="nama" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Role</label>
                    <select name="role" id="roleSelect" class="form-select" required>
                        <option value="">-- pilih --</option>
                        <option value="guru">Guru</option>
                        <option value="sarpras">Sarpras</option>
                    </select>
                </div>

                <div class="col-md-3" id="jurusanBox" style="display:none;">
                    <label>Jurusan</label>
<select name="id_jurusan" class="form-select">
    <option value="">Guru Umum (tanpa jurusan)</option>

    @foreach($jurusan as $j)
        <option value="{{ $j->id_jurusan }}">
            {{ $j->nama_jurusan }}
        </option>
    @endforeach
</select>

                </div>

<div class="col-md-2">
    <label>Password</label>
    <div class="input-group">
        <input type="password" name="password" id="passwordInput" class="form-control" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
            👁
        </button>
    </div>
</div>


                <div class="col-12">
                    <button class="btn btn-primary mt-2">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card mb-4">
    <div class="card-header fw-bold">Import Akun</div>
    <div class="card-body">
        <form method="POST" action="{{ route('sarpras.user.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="file" name="file" class="form-control" required>
                </div>

                <div class="col-md-6 d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="previewUser()">
                        Preview
                    </button>
                    <button class="btn btn-warning">Import</button>
                    <a href="{{ route('sarpras.user.template') }}" class="btn btn-outline-secondary">
                        Download Template
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="previewUserBox" class="mt-3 d-none">
    <h5>Preview Akun</h5>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Role</th>
            <th>Jurusan</th>
            <th>Status</th>
            <th>Password</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody id="previewUserBody"></tbody>
    </table>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               class="form-control"
               placeholder="Cari nama / role...">
    </div>

    
    <div class="col-md-2">
        <button class="btn btn-primary w-100">Cari</button>
    </div>

    @if(request('q'))
    <div class="col-md-2">
        <a href="{{ route('sarpras.user.index') }}" class="btn btn-secondary w-100">
            Reset
        </a>
    </div>
    @endif
</form>


{{-- TABLE --}}
<div class="card">
    <div class="card-header fw-bold">Daftar Akun</div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $users->firstItem() + $loop->index }}</td>
                <td>{{ $u->nama }}</td>
                <td>
                    <span class="badge bg-{{ $u->role === 'sarpras' ? 'primary' : 'success' }}">
                        {{ ucfirst($u->role) }}
                    </span>
                </td>
                <td>
                    @if($u->role === 'sarpras')
                        <span class="badge bg-primary">Sarpras</span>
                    @elseif($u->role === 'guru' && $u->jurusan)
                        <span class="badge bg-success">Guru Jurusan</span>
                    @else
                        <span class="badge bg-secondary">Guru Umum</span>
                    @endif
                </td>

<td>
    <div class="aksi-wrap">

        {{-- RESET --}}
        <form method="POST"
              action="{{ route('sarpras.user.reset', $u->id_user) }}"
              onsubmit="return confirm('Reset password akun ini ke 123456 ?')">
            @csrf
            <button type="submit" class="btn btn-warning btn-sm">
                Reset<i class="fa fa-rotate"></i>
            </button>
        </form>

        {{-- HAPUS --}}
        <form method="POST"
              action="{{ route('sarpras.user.destroy', $u->id_user) }}"
              onsubmit="return confirm('Hapus akun ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                Hapus<i class="fa fa-trash"></i>
            </button>
        </form>

    </div>
</td>

            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $users->links('vendor.pagination.akun') }}
    </div>
</div>

<script>
document.getElementById('roleSelect')?.addEventListener('change', function () {
    document.getElementById('jurusanBox').style.display =
        this.value === 'guru' ? 'block' : 'none';
});

function togglePassword() {
    const input = document.getElementById('passwordInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function previewUser() {
    const file = document.querySelector('input[name="file"]').files[0];
    if (!file) {
        alert('Pilih file terlebih dahulu');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    fetch("{{ route('sarpras.user.preview') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('previewUserBody');
        tbody.innerHTML = '';

        data.forEach(row => {
            const badge = row.status === 'ok'
                ? `<span class="badge bg-success">OK</span>`
                : `<span class="badge bg-danger">ERROR</span>`;

            tbody.innerHTML += `
                <tr>
                    <td>${row.nama}</td>
                    <td>${row.role}</td>
                    <td>${row.jurusan}</td>
                    <td>${badge}</td>
                    <td><code>${row.password ?? '-'}</code></td>
                    <td>${row.pesan}</td>
                </tr>
            `;
        });

        document.getElementById('previewUserBox').classList.remove('d-none');
    });
}



</script>

@endsection
