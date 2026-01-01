@extends('layouts.guru')

@section('title', 'Konfirmasi Pengembalian')

@section('content')

<style>

/* ===== TITLE ===== */
.page-title {
    font-weight: 800;
    font-size:clamp(1.4rem,4vw,1.9rem);
    text-align: center;
    color: #004f9a;
    margin-bottom: 18px;
}

/* ===== SEARCH BAR ===== */
.search-box {
    max-width: 400px;
    margin: 10px auto 25px;
}

.search-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 2px solid #1c84ff;
    outline: none;
    font-size: .95rem;
}

.search-input:focus {
    border-color: #004f9a;
    box-shadow: 0 0 8px rgba(0,79,154,0.3);
}

/* ===== TABLE WRAPPER ===== */
/* ===== TABLE WRAPPER (SAMAKAN DENGAN PEMINJAMAN) ===== */
.table-wrapper{
    background:#ffffff;
    padding:16px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

/* SCROLLBAR (SAMA) */
.table-wrapper::-webkit-scrollbar{
    height:8px;
}
.table-wrapper::-webkit-scrollbar-thumb{
    background:#c7dbff;
    border-radius:10px;
}
.table-wrapper::-webkit-scrollbar-track{
    background:transparent;
}


/* ===== TABLE ===== */
.custom-table{
    border-collapse:collapse;
    width:100%;
    min-width:1100px; /* biar scroll muncul */
}

.custom-table th,
.custom-table td{
    white-space:nowrap;
}


.custom-table thead {
    background: #e8f2ff;
}

.custom-table th {
    padding: 12px;
    font-size: .9rem;
    color: #004f9a;
    font-weight: 700;
    border-bottom: 2px solid #bcd9ff;
}

.custom-table td {
    padding: 10px;
    font-size: .88rem;
    border-bottom: 1px solid #e4efff;
}

.custom-table tr:hover {
    background: #f5f9ff;
}

/* ===== BADGES ===== */
.badge-blue {
    background: #1c84ff;
    padding: 4px 8px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    font-size: .75rem;
}

.status-badge {
    background: #bde3ff;
    color: #084d7f;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: .8rem;
}

/* ===== ACTION BUTTONS ===== */
.action-btns {
    display: flex;
    gap: 8px;
}

.btn-approve {
    background: #0b9b33;
    border: none;
    padding: 6px 10px;
    color: white;
    border-radius: 6px;
    font-size: .8rem;
    font-weight: 600;
}
.btn-approve:hover {
    background: #0a7f2b;
}

.btn-reject {
    background: #da1c1c;
    border: none;
    padding: 6px 10px;
    color: white;
    border-radius: 6px;
    font-size: .8rem;
    font-weight: 600;
}
.btn-reject:hover {
    background: #b01616;
}
/* ================= MOBILE FIX (SAMA DENGAN PEMINJAMAN) ================= */
@media (max-width:768px){

    /* Judul */
    .page-title{
        font-size:1.25rem;
        margin-bottom:16px;
    }

    /* Search */
    .search-input{
        font-size:.85rem;
        padding:10px 14px;
    }

    /* Wrapper */
    .table-wrapper{
        padding:10px;
    }

    /* Header tabel */
    .custom-table th{
        font-size:.7rem;
        padding:8px 10px;
    }

    /* Isi tabel */
    .custom-table td{
        font-size:.75rem;
        padding:8px 10px;
    }

    /* Badge */
    .badge-blue,
    .status-badge{
        font-size:.65rem;
        padding:4px 8px;
    }

    /* Tombol aksi */
    .action-btns{
        gap:4px;
    }

    .btn-approve,
    .btn-reject{
        font-size:.65rem;
        padding:5px 8px;
        border-radius:6px;
    }
}

/* ================= NOTIFICATION ================= */
.notif{
    position:fixed;
    top:24px;
    right:24px;
    min-width:260px;
    max-width:90vw;
    padding:14px 16px;
    border-radius:12px;
    font-weight:600;
    font-size:14px;
    color:white;
    box-shadow:0 12px 28px rgba(0,0,0,.15);
    opacity:0;
    pointer-events:none;
    transform:translateY(-10px);
    transition:.3s ease;
    z-index:9999;
}

.notif.show{
    opacity:1;
    transform:translateY(0);
}

.notif.success{background:linear-gradient(135deg,#22c55e,#16a34a);}
.notif.error{background:linear-gradient(135deg,#ef4444,#dc2626);}
.notif.info{background:linear-gradient(135deg,#3b82f6,#2563eb);}

@media(max-width:768px){
    .notif{
        left:12px;
        right:12px;
        top:16px;
    }
}

</style>


<div class="container mt-3">

    <h1 class="page-title">Konfirmasi Pengembalian Barang</h1>



    @if($detail->count())

    <!-- SEARCH BOX -->
    <div class="search-box">
        <input 
            type="text" 
            id="searchInput" 
            class="search-input"
            placeholder="Cari nama, barang, kelas..."
        >
    </div>

    <!-- TABLE WRAPPER -->
    <div class="table-wrapper">

        <table class="custom-table" id="returnTable">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Status</th>
                    <th>No WA</th>
                    <th>Ruangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @foreach($detail as $d)
                @php $p = $d->peminjaman; @endphp
                <tr class="search-item">

                    <td>{{ $p->siswa->nama }}</td>
                    <td>{{ $p->siswa->nis }}</td>
                    <td>{{ $p->siswa->kelas }}</td>

                    <td>{{ $d->barang->nama_barang }}</td>

                    <td>
                        <span class="badge-blue">{{ $d->jumlah }}</span>
                    </td>

                    <td>{{ $p->tanggal_pinjam }}</td>

                    <td>
                        <span class="status-badge">{{ $d->status_pengembalian }}</span>
                    </td>

                    <td>{{ $p->no_wa }}</td>
                    <td>{{ $p->ruangan }}</td>

                    <td>
                        <div class="action-btns">

                            <form action="{{ route('guru.pengembalian.setuju', $d->id_detail) }}" method="POST">
                                @csrf
                                <button class="btn-approve">Setujui</button>
                            </form>

                            <form action="{{ route('guru.pengembalian.tolak', $d->id_detail) }}" method="POST">
                                @csrf
                                <button class="btn-reject">Tolak</button>
                            </form>

                        </div>
                    </td>

                </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    @else

        <div class="alert alert-secondary text-center">
            Tidak ada barang menunggu konfirmasi pengembalian guru.
        </div>

    @endif

</div>


<div id="notif" class="notif"></div>


<!-- SEARCH SCRIPT -->
<script>
    function showNotif(type, message){
    const notif = document.getElementById('notif');
    if (!notif) return;

    notif.className = `notif ${type} show`;
    notif.textContent = message;

    setTimeout(() => {
        notif.classList.remove('show');
    }, 3000);}
document.getElementById("searchInput").addEventListener("input", function () {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#returnTable tbody .search-item");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
    });
});
</script>
@if(session('success'))
<script>
window.addEventListener('load', () => {
    showNotif('success', "{{ session('success') }}");
});
</script>
@endif

@if(session('error'))
<script>
window.addEventListener('load', () => {
    showNotif('error', "{{ session('error') }}");
});
</script>
@endif

@if(session('info'))
<script>
window.addEventListener('load', () => {
    showNotif('info', "{{ session('info') }}");
});
</script>
@endif

@endsection
