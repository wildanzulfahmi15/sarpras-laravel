@extends('layouts.guru')

@section('title', 'Riwayat Peminjaman')

@section('content')

<style>
/* ================= PAGE ================= */
.page-title{
    font-weight:800;
    color:#004f9a;
}

/* ================= SEARCH ================= */
.search-box{
    max-width:480px;
}

/* ================= FILTER BUTTON (RESPONSIVE) ================= */
.filter-container{
    display:flex;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:16px;
}

.filter-btn{
    padding:8px 16px;
    border-radius:999px;
    font-weight:600;
    font-size:.85rem;
    text-decoration:none;
    background:#e5edff;
    color:#1e40af;
    transition:.2s ease;
    white-space:nowrap;
}

.filter-btn:hover{
    background:#c7dbff;
    color:#1e3a8a;
}

.filter-btn.active{
    background:#2563eb;
    color:#ffffff;
    box-shadow:0 6px 18px rgba(37,99,235,.35);
}

/* ================= TABLE ================= */
.table-wrapper{
    background:#ffffff;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    padding:16px;
}

.table thead th{
    background:#e8f2ff;
    color:#004f9a;
    font-weight:700;
    font-size:.85rem;
    vertical-align:middle;
}

.table tbody td{
    font-size:.85rem;
    vertical-align:middle;
}

.table-striped > tbody > tr:nth-of-type(odd){
    background-color:#f8fbff;
}

/* ================= BADGE ================= */
.badge{
    font-size:.7rem;
    padding:6px 10px;
    border-radius:999px;
}

/* ================= MOBILE ================= */
@media(max-width:768px){

    .page-title{
        font-size:1.3rem;
    }

    .filter-btn{
        font-size:.75rem;
        padding:6px 12px;
    }

    .table thead th{
        font-size:.7rem;
    }

    .table tbody td{
        font-size:.75rem;
    }
}
</style>

<h2 class="page-title mt-3 text-center">
    Riwayat Peminjaman (Guru)
</h2>

<!-- SEARCH -->
<div class="d-flex justify-content-center mb-3">
    <input type="text" id="searchInput"
           placeholder="Cari nama siswa, barang, tanggal..."
           class="form-control search-box">
</div>

<!-- FILTER (RESPONSIVE & RAPI DI ANDROID) -->
<div class="filter-container">
    <a href="{{ route('guru.riwayat') }}"
       class="filter-btn {{ request('filter')==null ? 'active' : '' }}">
        Semua
    </a>

    <a href="{{ route('guru.riwayat', ['filter' => 'berlangsung']) }}"
       class="filter-btn {{ request('filter')=='berlangsung' ? 'active' : '' }}">
        Sedang Berlangsung
    </a>

    <a href="{{ route('guru.riwayat', ['filter' => 'selesai']) }}"
       class="filter-btn {{ request('filter')=='selesai' ? 'active' : '' }}">
        Sudah Dikembalikan
    </a>
</div>

<!-- TABLE -->
<div class="table-wrapper">
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Barang</th>
                    <th>Status Peminjaman</th>
                    <th>Status Pengembalian</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Pengembalian</th>
                    <th>Status Transaksi</th>
                </tr>
            </thead>

            <tbody id="tableBody">
                @foreach($riwayat as $r)
                    @foreach($r->details as $d)
                    <tr class="search-row">
                        <td>{{ $r->siswa->nama ?? '-' }}</td>

                        <td>
                            <strong>{{ $d->barang->nama_barang }}</strong>
                            <span class="text-muted">({{ $d->jumlah }})</span>
                        </td>

                        <td>
                            @if($d->status_peminjaman == 'Disetujui')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($d->status_peminjaman == 'Menunggu Guru')
                                <span class="badge bg-warning text-dark">Menunggu Guru</span>
                            @elseif($d->status_peminjaman == 'Menunggu Sarpras')
                                <span class="badge bg-info text-dark">Menunggu Sarpras</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>

                        <td>
                            @if($d->status_pengembalian == 'Belum')
                                <span class="badge bg-danger">Belum Dikembalikan</span>
                            @elseif($d->status_pengembalian == 'Menunggu Guru')
                                <span class="badge bg-warning text-dark">Menunggu Guru</span>
                            @elseif($d->status_pengembalian == 'Menunggu Sarpras')
                                <span class="badge bg-info text-dark">Menunggu Sarpras</span>
                            @else
                                <span class="badge bg-success">Sudah Dikembalikan</span>
                            @endif
                        </td>

                        <td>{{ $r->tanggal_pinjam }}</td>
                        <td>{{ $d->tanggal_pengembalian ?? '-' }}</td>

                        <td>
                            @if($r->status == 'Dipinjam')
                                <span class="badge bg-warning text-dark">Sedang Dipinjam</span>
                            @elseif($r->status == 'Dikembalikan')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-secondary">{{ $r->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll(".search-row");

    rows.forEach(row => {
        row.style.display =
            row.innerText.toLowerCase().includes(keyword) ? "" : "none";
    });
});
</script>

@endsection
