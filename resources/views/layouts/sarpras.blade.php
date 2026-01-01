<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') | Sarpras Panel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
/* ================= GLOBAL ================= */
body{
    margin:0;
    min-height:100vh;
    background:#f3f6fb;
    font-family:"Segoe UI",sans-serif;
    overflow-x:hidden;
}

/* ================= WRAPPER ================= */
.nav-app-wrap{
    display:flex;
    min-height:100vh;
}

/* ================= SIDEBAR DESKTOP ================= */
.nav-sidebar{
    width:240px;
    height:100vh;
    background:#ffffff;
    border-right:1px solid #e3e7ef;
    position:fixed;
    left:0;
    top:0;
    z-index:1000;
    display:flex;
    flex-direction:column;
    transition:.3s ease;
}

.nav-sidebar.nav-collapsed{ width:80px; }

.nav-sidebar-header{
    padding:18px 20px;
    font-weight:700;
    font-size:18px;
    color:#1e3a8a;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:1px solid #eef2f7;
}

.nav-toggle-btn{
    background:#e0e7ff;
    border:none;
    border-radius:6px;
    padding:6px 10px;
    cursor:pointer;
}

/* MENU */
.nav-sidebar-menu{
    flex:1;
    overflow-y:auto;
    padding:10px 0;
}

.nav-sidebar-menu::-webkit-scrollbar{
    width:6px;
}
.nav-sidebar-menu::-webkit-scrollbar-thumb{
    background:#c7d2fe;
    border-radius:10px;
}

.nav-sidebar a{
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px 18px;
    margin:4px 12px;
    border-radius:10px;
    color:#475569;
    text-decoration:none;
    transition:.2s;
}

.nav-sidebar a:hover,
.nav-sidebar a.active{
    background:#dbeafe;
    color:#1e3a8a;
    font-weight:600;
}

.nav-sidebar.nav-collapsed .nav-text,
.nav-sidebar.nav-collapsed .nav-sidebar-header span,
.nav-sidebar.nav-collapsed .nav-logout-text{
    display:none;
}

/* LOGOUT DESKTOP */
.nav-logout-wrap{
    border-top:1px solid #e5e7eb;
    padding:12px;
}

.nav-logout-btn{
    width:100%;
    border-radius:12px;
}

/* ================= CONTENT ================= */
.nav-content{
    margin-left:240px;
    padding:24px;
    width:100%;
    max-width:100%;
    min-width:0;
    overflow-x:auto;
    transition:.3s ease;
}

.nav-content.nav-collapsed{
    margin-left:80px;
}


.nav-content.nav-collapsed{
    margin-left:80px;
}

/* ================= MOBILE ================= */
.nav-bottom{
    display:none;
}

/* ================= MOBILE STYLE ================= */
@media (max-width:768px){

    .nav-sidebar{ display:none; }

    .nav-content,
    .nav-content.nav-collapsed{
        margin-left:0;
        padding-bottom:130px;
    }

    .nav-bottom{
        display:flex;
        position:fixed;
        left:50%;
        bottom:14px;
        transform:translateX(-50%);
        width:calc(100% - 28px);
        max-width:420px;
        height:70px;
        background:#fff;
        border-radius:26px;
        box-shadow:0 18px 40px rgba(0,0,0,.25);
        z-index:2000;
        padding:0 6px;
    }

    .nav-bottom a{
        flex:1;
        text-align:center;
        font-size:11px;
        color:#64748b;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:4px;
        text-decoration:none;
    }

    .nav-bottom a i{ font-size:22px; }

    .nav-bottom a.active{
        color:#1e3a8a;
        font-weight:700;
    }
}

/* ================= KELOLA PANEL ================= */
.nav-manage-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.4);
    z-index:2500;
    display:none;
}

.nav-manage-overlay.show{ display:block; }

.nav-manage-panel{
    position:fixed;
    bottom:100px;
    left:50%;
    transform:translateX(-50%);
    width:calc(100% - 32px);
    max-width:420px;
    background:#fff;
    border-radius:22px;
    padding:16px;
    z-index:3000;
    display:none;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.nav-manage-panel.show{ display:block; }

.nav-manage-title{
    text-align:center;
    font-weight:700;
    margin-bottom:12px;
    color:#1e3a8a;
}

.nav-manage-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}

.nav-manage-item{
    background:#f1f5f9;
    border-radius:16px;
    padding:14px 8px;
    text-align:center;
    font-size:12px;
    font-weight:600;
    text-decoration:none;
    color:#334155;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:6px;
}

.nav-manage-item i{
    font-size:20px;
    color:#1e3a8a;
}

/* logout popup */
.nav-logout-panel{
    position:fixed;
    bottom:120px;
    left:50%;
    transform:translateX(-50%);
    width:calc(100% - 40px);
    max-width:360px;
    background:#fff;
    border-radius:22px;
    padding:18px;
    z-index:3000;
    display:none;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}
.nav-logout-panel.show{ display:block; }
/* ===== FIX LAYOUT MOBILE ===== */
@media (max-width: 768px) {
    .nav-content,
    .nav-content.nav-collapsed {
        margin-left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
}


</style>
</head>

<body>

<div class="nav-app-wrap">

<!-- SIDEBAR DESKTOP -->
<div class="nav-sidebar d-none d-md-flex" id="navSidebar">

    <div class="nav-sidebar-header">
        <span>Sarpras</span>
        <button class="nav-toggle-btn" id="navToggle">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <div class="nav-sidebar-menu">
        <a href="{{ route('sarpras.dashboard') }}" class="{{ request()->routeIs('sarpras.dashboard')?'active':'' }}"><i class="fa fa-home"></i><span class="nav-text">Dashboard</span></a>
        <a href="{{ route('sarpras.peminjaman') }}" class="{{ request()->routeIs('sarpras.peminjaman')?'active':'' }}"><i class="fa fa-box"></i><span class="nav-text">Peminjaman</span></a>
        <a href="{{ route('sarpras.pengembalian') }}" class="{{ request()->routeIs('sarpras.pengembalian')?'active':'' }}"><i class="fa fa-rotate-left"></i><span class="nav-text">Pengembalian</span></a>
        <a href="{{ route('sarpras.riwayat') }}" class="{{ request()->routeIs('sarpras.riwayat')?'active':'' }}"><i class="fa fa-clock-rotate-left"></i><span class="nav-text">Riwayat</span></a>
        <a href="{{ route('sarpras.whatsapp') }}" class="{{ request()->routeIs('sarpras.whatsapp')?'active':'' }}"><i class="fa fa-robot"></i><span class="nav-text">Bot</span></a>
        <a href="{{ route('sarpras.barang.index') }}" class="{{ request()->routeIs('sarpras.barang.index')?'active':'' }}"><i class="fa fa-boxes-stacked"></i><span class="nav-text">Barang</span></a>
        <a href="{{ route('sarpras.jurusan.kelas') }}" class="{{ request()->routeIs('sarpras.jurusan.kelas')?'active':'' }}"><i class="fa fa-layer-group"></i><span class="nav-text">Jurusan & Kelas</span></a>
        <a href="{{ route('sarpras.siswa.index') }}" class="{{ request()->routeIs('sarpras.siswa.index')?'active':'' }}"><i class="fa fa-user-graduate"></i><span class="nav-text">Siswa</span></a>
        <a href="{{ route('sarpras.mapel.index') }}" class="{{ request()->routeIs('sarpras.mapel.index')?'active':'' }}"><i class="fa fa-book"></i><span class="nav-text">Mapel</span></a>
        <a href="{{ route('sarpras.user.index') }}" class="{{ request()->routeIs('sarpras.user.index')?'active':'' }}"><i class="fa fa-users"></i><span class="nav-text">Akun</span></a>
    </div>

    <form action="{{ route('logout') }}" method="POST" class="nav-logout-wrap">
        @csrf
        <button class="btn btn-primary nav-logout-btn">
            <i class="fa fa-sign-out-alt"></i>
            <span class="nav-logout-text">Logout</span>
        </button>
    </form>
</div>

<!-- CONTENT -->
<div class="nav-content" id="navContent">
    @yield('content')
</div>

</div>

<!-- ================= BOTTOM NAV ================= -->
<div class="nav-bottom d-md-none">

    <a href="{{ route('sarpras.dashboard') }}" class="{{ request()->routeIs('sarpras.dashboard')?'active':'' }}">
        <i class="fa fa-home"></i><span>Home</span>
    </a>

    <a href="{{ route('sarpras.peminjaman') }}" class="{{ request()->routeIs('sarpras.peminjaman')?'active':'' }}">
        <i class="fa fa-box"></i><span>Pinjam</span>
    </a>

    <a href="{{ route('sarpras.pengembalian') }}" class="{{ request()->routeIs('sarpras.pengembalian')?'active':'' }}">
        <i class="fa fa-rotate-left"></i><span>Kembali</span>
    </a>

    <a href="javascript:void(0)" id="btnKelola">
        <i class="fa fa-gear"></i><span>Kelola</span>
    </a>

    <a href="javascript:void(0)" id="btnLogout">
        <i class="fa fa-right-from-bracket"></i><span>Logout</span>
    </a>
</div>

<!-- PANEL KELOLA -->
<div class="nav-manage-overlay" id="kelolaOverlay"></div>

<div class="nav-manage-panel" id="kelolaPanel">
    <div class="nav-manage-title">Menu Kelola</div>

    <div class="nav-manage-grid">
        <a href="{{ route('sarpras.barang.index') }}" class="nav-manage-item"><i class="fa fa-boxes-stacked"></i>Barang</a>
        <a href="{{ route('sarpras.jurusan.kelas') }}" class="nav-manage-item"><i class="fa fa-layer-group"></i>Jurusan</a>
        <a href="{{ route('sarpras.siswa.index') }}" class="nav-manage-item"><i class="fa fa-user-graduate"></i>Siswa</a>
        <a href="{{ route('sarpras.mapel.index') }}" class="nav-manage-item"><i class="fa fa-book"></i>Mapel</a>
        <a href="{{ route('sarpras.user.index') }}" class="nav-manage-item"><i class="fa fa-users"></i>Akun</a>
        <a href="{{ route('sarpras.riwayat') }}" class="nav-manage-item"><i class="fa fa-clock-rotate-left"></i>Riwayat</a>
        <a href="{{ route('sarpras.whatsapp') }}" class="nav-manage-item"><i class="fa fa-robot"></i>Bot</a>
    </div>
</div>

<!-- LOGOUT POPUP -->
<div class="nav-manage-overlay" id="logoutOverlay"></div>

<div class="nav-logout-panel" id="logoutPanel">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger w-100 py-3 fw-bold rounded-4">
            <i class="fa fa-right-from-bracket me-2"></i>
            Keluar dari Akun
        </button>
    </form>
</div>

<script>
const navSidebar = document.getElementById("navSidebar");
const navContent = document.getElementById("navContent");
const navToggle = document.getElementById("navToggle");

navToggle?.addEventListener("click", () => {
    navSidebar.classList.toggle("nav-collapsed");
    navContent.classList.toggle("nav-collapsed");
});

// kelola
const btnKelola = document.getElementById("btnKelola");
const kelolaPanel = document.getElementById("kelolaPanel");
const kelolaOverlay = document.getElementById("kelolaOverlay");

btnKelola?.addEventListener("click", () => {
    kelolaPanel.classList.add("show");
    kelolaOverlay.classList.add("show");
});

kelolaOverlay?.addEventListener("click", () => {
    kelolaPanel.classList.remove("show");
    kelolaOverlay.classList.remove("show");
});

// logout
const btnLogout = document.getElementById("btnLogout");
const logoutPanel = document.getElementById("logoutPanel");
const logoutOverlay = document.getElementById("logoutOverlay");

btnLogout?.addEventListener("click", () => {
    logoutPanel.classList.add("show");
    logoutOverlay.classList.add("show");
});

logoutOverlay?.addEventListener("click", () => {
    logoutPanel.classList.remove("show");
    logoutOverlay.classList.remove("show");
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
