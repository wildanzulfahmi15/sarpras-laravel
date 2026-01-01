<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') - Dashboard Guru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
/* ================= GLOBAL ================= */
body{
    margin:0;
    background:#eef5ff;
    font-family:"Segoe UI",sans-serif;
    overflow-x:auto;
}
.content{
    flex:1;
    padding:30px;
    transition:.3s;

    min-width:0;          /* 🔥 KUNCI FLEX */
    overflow-x:auto;      /* 🔥 IZINKAN SCROLL KE KANAN */
}

/* ================= LAYOUT ================= */
.layout{
    display:flex;
    align-items:stretch; /* 🔑 sidebar ikut konten */
    min-height:100vh;
}

/* ================= SIDEBAR DESKTOP ================= */
.sidebar{
    width:240px;
    background:linear-gradient(180deg,#3b82f6,#2563eb);
    color:white;
    padding-top:20px;
    box-shadow:4px 0 20px rgba(0,0,0,.18);
    transition:.3s ease;
    flex-shrink:0;
}

.sidebar.collapsed{
    width:80px;
}

.sidebar-header{
    padding:0 20px 25px;
    font-size:20px;
    font-weight:700;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.sidebar-menu a{
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px 20px;
    margin:4px 12px;
    border-radius:10px;
    color:#f3f4f6;
    text-decoration:none;
    transition:.2s;
}

.sidebar-menu a:hover{
    background:rgba(255,255,255,.25);
}

.sidebar-menu a.active{
    background:rgba(255,255,255,.32);
    box-shadow:0 0 12px rgba(255,255,255,.45);
}

/* Hide text when collapsed */
.sidebar.collapsed .menu-text,
.sidebar.collapsed .sidebar-header h5,
.sidebar.collapsed .logout-text{
    display:none!important;
}

/* ================= CONTENT ================= */
.content{
    flex:1;
    padding:30px;
    transition:.3s;
}

/* ================= TOGGLE ================= */
.toggle-btn{
    background:rgba(255,255,255,.25);
    border:none;
    border-radius:6px;
    padding:6px 10px;
    color:white;
    cursor:pointer;
}

/* ================= BOTTOM BAR (MOBILE) ================= */
.guru-bottom{
    display:none;
}

/* ================= RESPONSIVE ================= */
@media(max-width:768px){

    .layout{
        display:block;
    }

    .sidebar{
        display:none;
    }

    .content{
        padding-bottom:120px;
    }

    .guru-bottom{
        display:flex;
        position:fixed;
        left:50%;
        bottom:14px;
        transform:translateX(-50%);
        width:calc(100% - 28px);
        max-width:420px;
        height:68px;
        background:#fff;
        border-radius:26px;
        box-shadow:0 18px 40px rgba(0,0,0,.25);
        z-index:2000;
        padding:0 8px;
    }

    .guru-bottom a{
        flex:1;
        text-decoration:none;
        color:#64748b;
        font-size:11px;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        transition:.25s;
        position:relative;
    }

    .guru-bottom a i{
        font-size:22px;
    }

    .guru-bottom a.active{
        color:#2563eb;
        font-weight:700;
    }

    .guru-bottom a.active i{
        transform:translateY(-6px) scale(1.15);
    }

    .guru-bottom a.active::after{
        content:"";
        position:absolute;
        bottom:8px;
        width:6px;
        height:6px;
        background:#2563eb;
        border-radius:50%;
    }
}

/* ================= MOBILE LOGOUT POPUP ================= */
.mobile-logout{
    position:fixed;
    bottom:100px;
    right:20px;
    background:#fff;
    border-radius:18px;
    box-shadow:0 18px 40px rgba(0,0,0,.25);
    z-index:3000;
    min-width:160px;
    display:none;
}

.mobile-logout.show{
    display:block;
}

.mobile-logout button{
    width:100%;
    background:none;
    border:none;
    padding:14px 18px;
    color:#ef4444;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:10px;
}
.sidebar.collapsed{
    width:80px;
}

.content.collapsed{
    margin-left:80px;
}

</style>
</head>

<body>

<div class="layout">

<!-- ================= SIDEBAR DESKTOP ================= -->
<div class="sidebar d-none d-md-block" id="sidebar">

    <div class="sidebar-header">
        <h5>Guru Panel</h5>
        <button class="toggle-btn d-none d-md-block" id="desktopToggle">
            <i class="fa fa-bars"></i>
        </button>
    </div>

    <div class="sidebar-menu">

        <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard')?'active':'' }}">
            <i class="fa fa-home"></i>
            <span class="menu-text">Home</span>
        </a>

        <a href="{{ route('guru.peminjaman') }}" class="{{ request()->routeIs('guru.peminjaman')?'active':'' }}">
            <i class="fa fa-box"></i>
            <span class="menu-text">Peminjaman</span>
        </a>

        <a href="{{ route('guru.pengembalian') }}" class="{{ request()->routeIs('guru.pengembalian')?'active':'' }}">
            <i class="fa fa-rotate-left"></i>
            <span class="menu-text">Pengembalian</span>
        </a>

        <a href="{{ route('guru.riwayat') }}" class="{{ request()->routeIs('guru.riwayat')?'active':'' }}">
            <i class="fa fa-clock-rotate-left"></i>
            <span class="menu-text">Riwayat</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-3 px-3">
            @csrf
            <button class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fa fa-sign-out-alt"></i>
                <span class="logout-text">Logout</span>
            </button>
        </form>

    </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="content">
    @yield('content')
</div>

</div>

<!-- ================= BOTTOM BAR MOBILE ================= -->
<div class="guru-bottom d-md-none">

    <a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard')?'active':'' }}">
        <i class="fa fa-home"></i>Home
    </a>

    <a href="{{ route('guru.peminjaman') }}" class="{{ request()->routeIs('guru.peminjaman')?'active':'' }}">
        <i class="fa fa-box"></i>Pinjam
    </a>

    <a href="{{ route('guru.pengembalian') }}" class="{{ request()->routeIs('guru.pengembalian')?'active':'' }}">
        <i class="fa fa-rotate-left"></i>Kembali
    </a>

    <a href="{{ route('guru.riwayat') }}" class="{{ request()->routeIs('guru.riwayat')?'active':'' }}">
        <i class="fa fa-clock-rotate-left"></i>Riwayat
    </a>
    <a href="#" id="openLogout">
        <i class="fa fa-ellipsis"></i>Lainnya
    </a>
    
    
</div>
<div class="mobile-logout" id="mobileLogout">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">
            <i class="fa fa-sign-out-alt"></i> Logout
        </button>
    </form>
</div>
<script>
const sidebar = document.getElementById("sidebar");
const desktopToggle = document.getElementById("desktopToggle");

desktopToggle?.addEventListener("click",()=>{
    sidebar.classList.toggle("collapsed");
});

const openLogout = document.getElementById("openLogout");
const mobileLogout = document.getElementById("mobileLogout");

/* toggle popup */
openLogout?.addEventListener("click",(e)=>{
    e.preventDefault();
    e.stopPropagation();
    mobileLogout.classList.toggle("show");
});

/* 🔥 penting: klik di popup jangan nutup */
mobileLogout.addEventListener("click",(e)=>{
    e.stopPropagation();
});

/* klik di luar = tutup */
document.addEventListener("click",(e)=>{
    if(
        !mobileLogout.contains(e.target) &&
        !openLogout.contains(e.target)
    ){
        mobileLogout.classList.remove("show");
    }
});

/* klik menu bottom lain = tutup */
document.querySelectorAll(".guru-bottom a").forEach(link=>{
    if(link.id !== "openLogout"){
        link.addEventListener("click",()=>{
            mobileLogout.classList.remove("show");
        });
    }
});
</script>


</body>
</html>
