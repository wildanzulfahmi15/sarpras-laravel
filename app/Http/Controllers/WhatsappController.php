<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    /**
     * Kirim WA
     */
public static function kirim($no, $pesan)
{
    try {
        $no = preg_replace('/^0/', '62', $no);

        $response = Http::timeout(10)
            ->post(config('services.whatsapp.url') . '/send-wa', [
                'to' => $no,
                'message' => $pesan
            ]);

        if (!$response->successful()) {
            Log::error("WA gagal", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

    } catch (\Throwable $e) {
        Log::error("WA ERROR: " . $e->getMessage());
    }
}


    /* =====================================================
     | GURU → SETUJU PEMINJAMAN (PER BARANG)
     ===================================================== */
    public static function guruSetujuPeminjaman($detail)
    {
        $peminjaman = $detail->peminjaman;
        $siswa = $peminjaman->siswa;

        $pesan =
"✅ *PEMINJAMAN DISETUJUI GURU*

Halo {$siswa->nama},

Barang berikut telah *disetujui oleh guru*:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Status selanjutnya:
⏳ Menunggu persetujuan petugas sarpras.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | GURU → TOLAK PEMINJAMAN (PER BARANG)
     ===================================================== */
    public static function guruTolakPeminjaman($detail)
    {
        $peminjaman = $detail->peminjaman;
        $siswa = $peminjaman->siswa;

        $pesan =
"❌ *PEMINJAMAN DITOLAK GURU*

Halo {$siswa->nama},

Barang berikut ditolak oleh guru:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan hubungi guru terkait atau ajukan ulang.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | GURU → SETUJU PENGEMBALIAN
     ===================================================== */
    public static function guruSetujuPengembalian($detail)
    {
        $peminjaman = $detail->peminjaman;
        $siswa = $peminjaman->siswa;

        $pesan =
"✅ *PENGEMBALIAN DISETUJUI GURU*

Halo {$siswa->nama},

Pengembalian barang berikut telah disetujui:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan lanjut ke petugas sarpras.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | GURU → TOLAK PENGEMBALIAN
     ===================================================== */
    public static function guruTolakPengembalian($detail)
    {
        $peminjaman = $detail->peminjaman;
        $siswa = $peminjaman->siswa;

        $pesan =
"❌ *PENGEMBALIAN DITOLAK GURU*

Halo {$siswa->nama},

Pengembalian barang berikut ditolak:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan periksa dan ajukan kembali.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | SARPRAS → SETUJU PEMINJAMAN
     ===================================================== */
    public static function sarprasSetujuPeminjaman($detail)
    {
        $peminjaman = $detail->peminjaman;

        $pesan =
"✅ *PEMINJAMAN DISETUJUI SARPRAS*

Halo {$peminjaman->siswa->nama},

Barang berikut telah disetujui oleh petugas sarpras:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan ambil barang sesuai prosedur.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | SARPRAS → TOLAK PEMINJAMAN
     ===================================================== */
    public static function sarprasTolakPeminjaman($detail)
    {
        $peminjaman = $detail->peminjaman;

        $pesan =
"❌ *PEMINJAMAN DITOLAK SARPRAS*

Halo {$peminjaman->siswa->nama},

Barang berikut ditolak oleh petugas sarpras:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan ajukan ulang peminjaman.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | SARPRAS → SETUJU PENGEMBALIAN
     ===================================================== */
    public static function sarprasSetujuPengembalian($detail)
    {
        $peminjaman = $detail->peminjaman;

        $pesan =
"✅ *PENGEMBALIAN SELESAI*

Halo {$peminjaman->siswa->nama},

Barang berikut telah dikonfirmasi kembali:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Terima kasih sudah mengembalikan barang tepat waktu 🙏";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | SARPRAS → TOLAK PENGEMBALIAN
     ===================================================== */
    public static function sarprasTolakPengembalian($detail)
    {
        $peminjaman = $detail->peminjaman;

        $pesan =
"❌ *PENGEMBALIAN DITOLAK SARPRAS*

Halo {$peminjaman->siswa->nama},

Pengembalian barang berikut ditolak:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan ajukan ulang atau hubungi petugas sarpras.";

        self::kirim($peminjaman->no_wa, $pesan);
    }

    /* =====================================================
     | STOK HABIS
     ===================================================== */
    public static function guruStokHabis($detail)
    {
        $peminjaman = $detail->peminjaman;
        $siswa = $peminjaman->siswa;

        $pesan =
"⚠️ *STOK BARANG HABIS*

Halo {$siswa->nama},

Pengajuan peminjaman tidak dapat diproses karena stok berikut habis:

- {$detail->barang->nama_barang} ({$detail->jumlah})

Silakan ajukan ulang atau hubungi guru.";

        self::kirim($peminjaman->no_wa, $pesan);
    }
    /* =====================================================
 | SISWA → AJUKAN PEMINJAMAN (DENGAN DAFTAR BARANG)
 ===================================================== */
public static function ajukanPeminjaman($peminjaman)
{
    $siswa = $peminjaman->siswa;

    // Ambil daftar barang
    $daftarBarang = "";
    foreach ($peminjaman->detail as $detail) {
        $daftarBarang .= "- {$detail->barang->nama_barang} ({$detail->jumlah})\n";
    }

    $pesan =
"📩 *PENGAJUAN PEMINJAMAN DIKIRIM*

Halo {$siswa->nama},

Pengajuan peminjaman kamu berhasil dikirim dengan rincian berikut:

📦 *Daftar Barang:*
{$daftarBarang}

Status saat ini:
⏳ Menunggu persetujuan guru.

Mohon menunggu notifikasi berikutnya 🙏";

    self::kirim($peminjaman->no_wa, $pesan);
}

}
