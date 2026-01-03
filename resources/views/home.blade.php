<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventaris Sarpras — SMKN 1 Cibinong</title>


  {{-- CSS HOME --}}
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<style>
      :root {
      --color-smk-purple: #2E3192;
      --color-smk-blue: #29ABE2;
      --color-smk-yellow: #FFD200;
      --color-smk-red: #E94E77;
      --color-smk-gray: #B3B3B3;
      --color-smk-dark: #1E1E50;
    }


    img { max-width: 100%; display: block; }
    a { text-decoration: none; color: inherit; }

    /* === BANNER === */
    .banner {
      position: relative;
      height: 100vh;

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: background-image 1s ease; /* animasi fade */


      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      overflow: hidden;
    }

    .banner::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(30, 30, 80, 0.6);
    }

    .banner-content {
      position: relative;
      z-index: 1;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 1s ease, transform 1s ease;
    }

    .banner-content.show {
      opacity: 1;
      transform: translateY(0);
    }

    .banner h1 {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--color-smk-yellow);
    }

    .banner p {
      font-size: 1.3rem;
      color: #fff;
      margin-top: 10px;
    }

    .banner button {
      margin-top: 20px;
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 30px;
      background-color: var(--color-smk-blue);
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .banner button:hover {
      background-color: var(--color-smk-yellow);
      color: var(--color-smk-dark);
    }

    footer {
  background: #2e3192;
  color: white;
  padding: 40px 10%;
  font-family: Arial, sans-serif;
}

.footer-grid {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 30px;
}

.footer-grid div {
  flex: 1;
  min-width: 230px;
}

.footer-grid h3 {
  margin-bottom: 10px;
  font-size: 18px;
  font-weight: 600;
}

.footer-grid p,
.footer-grid a {
  font-size: 15px;
  line-height: 1.6;
}

.footer-grid a {
  color: white;
  text-decoration: none;
  transition: 0.3s;
}

.footer-grid a:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.footer-bottom {
  text-align: center;
  margin-top: 25px;
  padding-top: 15px;
  border-top: 1px solid rgba(255,255,255,0.3);
  font-size: 14px;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
  footer {
    padding: 40px 6%;
  }

  .footer-grid {
    flex-direction: column;
    text-align: left;
  }

  .footer-grid div {
    max-width: 100%;
  }
}

</style>
<body>

  {{-- NAVBAR COMPONENT --}}
  <x-navbar />

  <!-- BANNER -->
<header class="banner" id="banner"
  data-img1="{{ asset('img/banner1.png') }}"
  data-img2="{{ asset('img/banner2.png') }}"
  data-img3="{{ asset('img/banner3.png') }}"
>

    <div class="banner-content" id="banner-content">
      <h1>Selamat Datang di Inventaris Sarpras</h1>
      <p>SMKN 1 Cibinong</p>
      <a href="{{ route('peminjaman.kategori') }}"><button id="lihat-btn" >Lihat Inventaris</button></a>
    </div>
  </header>
  
<footer>
  <div class="footer-grid">
    <div>
      <h3>SMKN 1 Cibinong</h3>
      <p>Sistem Inventaris Sarpras untuk mempermudah pengelolaan dan pelacakan barang sekolah secara digital.</p>
    </div>
    <div>
      <h3>Kontak</h3>
      <p>📧 sarpras@smkn1cibinong.sch.id</p>
      <p>📞 (021) 87654321</p>
    </div>
    <div>
      <h3>Ikuti Kami</h3>
      <a href="https://www.facebook.com/smknegeri1cibinong/?locale=id_ID" target="_blank">Facebook</a><br>
      <a href="https://www.instagram.com/smkn1cbn_official/" target="_blank">Instagram</a>
    </div>
  </div>
  <div class="footer-bottom">
    © <span id="year"></span> SMKN 1 Cibinong — Inventaris Sarpras.
  </div>
</footer>
  


</body>
</html>
