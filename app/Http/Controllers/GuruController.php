<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengembalian;
use App\Models\Barang;
use App\Http\Controllers\WhatsappController;
use App\Models\DetailPeminjaman;
class GuruController extends Controller
{
public function dashboard()
{
    // 1. Menunggu guru menyetujui peminjaman
    $menungguPeminjaman = DetailPeminjaman::where('status_peminjaman', 'Menunggu Guru')->count();

    // 2. Menunggu guru menyetujui pengembalian
    $menungguPengembalian = DetailPeminjaman::where('status_pengembalian', 'Menunggu Guru')->count();

    // 3. Barang sedang dipinjam (sudah disetujui sarpras + belum selesai)
    $barangDipinjam = DetailPeminjaman::where('status_peminjaman', 'Disetujui Sarpras')
        ->where('status_pengembalian', '!=', 'Selesai')
        ->count();

    // 4. Barang selesai dipinjam
    $barangSelesai = DetailPeminjaman::where('status_pengembalian', 'Selesai')->count();

    return view('guru.dashboard', compact(
        'menungguPeminjaman',
        'menungguPengembalian',
        'barangDipinjam',
        'barangSelesai'
    ));
}



public function guruIndex()
{
    $guruId = Auth::user()->id_user;

    // Ambil semua detail barang yang menunggu guru
    $detail = DetailPeminjaman::with([
            'barang',
            'peminjaman.siswa',
            'peminjaman.guru',
            'peminjaman.mapel'
        ])
        ->where('status_peminjaman', 'Menunggu Guru')
        ->whereHas('peminjaman', function($q) use ($guruId) {
            $q->where('id_guru', $guruId);
        })
        ->get();

    return view('guru.konfirmasi', compact('detail'));
}



public function guruSetuju($id)
{
    $detail = DetailPeminjaman::findOrFail($id);

    if ($detail->peminjaman->id_guru != Auth::user()->id_user) {
        return back()->with('info', 'Tidak berwenang.');
    }

    $barang = $detail->barang;
    $peminjaman = $detail->peminjaman;

    // ❌ STOK HABIS
    if ($barang->stok < $detail->jumlah) {
        $detail->status_peminjaman = 'Stok Habis';
        $detail->save();
        
        // 🔥 CEK SEMUA DETAIL
        $adaYangLolos = DetailPeminjaman::where('id_peminjaman', $peminjaman->id_peminjaman)
            ->whereIn('status_peminjaman', ['Menunggu Sarpras', 'Disetujui'])
            ->exists();

        if (!$adaYangLolos) {
            $peminjaman->status = 'Ditolak';
            $peminjaman->save();
        }
        WhatsappController::guruStokHabis($detail);
        return back()->with(
            'error',
            'Stok '.$barang->nama_barang.' habis. Item ini otomatis ditolak.'
        );
    }

    // ✅ STOK CUKUP
    $barang->decrement('stok', $detail->jumlah);

    $detail->status_peminjaman = 'Menunggu Sarpras';
    $detail->save();

    // 🔥 UPDATE STATUS HEADER
    $peminjaman->status = 'Diajukan';
    $peminjaman->save();
    WhatsappController::guruSetujuPeminjaman($detail);
    return back()->with('success', 'Peminjaman disetujui guru.');
}




public function guruTolak($id)
{
    $detail = DetailPeminjaman::findOrFail($id);

    if ($detail->peminjaman->id_guru != Auth::user()->id_user) {
        return back()->with('info', 'Tidak berwenang.');
    }

    // Tandai detail sebagai ditolak
    $detail->status_peminjaman = 'Ditolak Guru';
    $detail->save();

    // Cek: apakah SEMUA barang di peminjaman ini ditolak?
    $peminjaman = $detail->peminjaman;

    $adaYangTidakDitolak = DetailPeminjaman::where('id_peminjaman', $peminjaman->id_peminjaman)
        ->where('status_peminjaman', '!=', 'Ditolak Guru')
        ->exists();

    if (!$adaYangTidakDitolak) {
        // Semua detail ditolak → ubah status header
        $peminjaman->status = 'Ditolak';
        $peminjaman->save();
    }
    WhatsappController::guruTolakPeminjaman($detail);
    return back()->with('error', 'Peminjaman ditolak guru.');
}

public function pengembalian()
{
    $guruId = Auth::user()->id_user;

    // Ambil semua detail barang yang diajukan pengembalian untuk guru ini
    $detail = DetailPeminjaman::with([
        'barang',
        'peminjaman.siswa',
        'peminjaman.guru',
        'peminjaman.mapel'
    ])
    ->where('status_pengembalian', 'Menunggu Guru')
    ->whereHas('peminjaman', function($q) use ($guruId) {
        $q->where('id_guru', $guruId);
    })
    ->get();

    return view('guru.pengembalian', compact('detail'));
}


public function setujuPengembalian($id)
{
    $detail = DetailPeminjaman::findOrFail($id);

    // hanya guru yg bersangkutan
    if ($detail->peminjaman->id_guru != Auth::user()->id_user) {
        return back()->with('info', 'Tidak berwenang.');
    }

    // set status detail
    $detail->status_pengembalian = 'Menunggu Sarpras';
    $detail->save();
    WhatsappController::guruSetujuPengembalian($detail);
    return back()->with('success', 'Pengembalian disetujui guru.');
}

public function tolakPengembalian($id)
{
    $detail = DetailPeminjaman::findOrFail($id);

    if ($detail->peminjaman->id_guru != Auth::user()->id_user) {
        return back()->with('info', 'Tidak berwenang.');
    }

    $detail->status_pengembalian = 'Ditolak Guru';
    $detail->save();
    WhatsappController::guruTolakPengembalian($detail);
    return back()->with('error', 'Pengembalian rang ditolak guru.');
}

}
