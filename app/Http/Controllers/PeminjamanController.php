<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\Siswa;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Mapel;
use App\Models\DetailPeminjaman;
use App\Http\Controllers\WhatsappController;

class PeminjamanController extends Controller

{
    // Ambil guru dengan search
    public function getGuru(Request $request)
    {
        $search = $request->input('q');

        $guru = User::where('role', 'guru')
            ->when($search, function ($query) use ($search) {
                $query->where('nama', 'LIKE', '%' . $search . '%');
            })
            ->get(['id_user', 'nama']);

        return response()->json($guru);
    }


public function pilihBarang(Request $request, $idKategori)
{
    if ($request->from !== 'form') {
        session()->forget('barangDipilih');
    }

    $barangDipilih = session('barangDipilih', []);

    $kategori = Kategori::findOrFail($idKategori); // 🔥 AMBIL DATA KATEGORI

    $barang = Barang::where('id_kategori', $idKategori)->get();

    return view('pilih_barang', [
        'kategori' => $kategori,          
        'barang' => $barang,
        'barangDipilih' => $barangDipilih
    ]);
}




public function riwayat(Request $request)
{
    $filter = $request->filter;
    $search = strtolower($request->search);

    $query = Peminjaman::with(['siswa', 'guru', 'details.barang'])
        ->orderBy('created_at', 'desc');

    // FILTER STATUS
    if ($filter == 'berlangsung') {
        $query->where('status', 'Dipinjam');
    } elseif ($filter == 'selesai') {
        $query->where('status', 'Dikembalikan');
    }


    $riwayat = $query->get();

    return view('sarpras.riwayat', compact('riwayat', 'filter', 'search'));
}

public function riwayatGuru(Request $request)
{
    $filter = $request->filter;
    $search = strtolower($request->search);

    $guruId = auth()->user()->id_user;

    $query = Peminjaman::with(['siswa', 'details.barang'])
        ->where('id_guru', $guruId)
        ->orderBy('created_at', 'desc');

    // FILTER STATUS
    if ($filter == 'berlangsung') {
        $query->where('status', 'Dipinjam');
    } elseif ($filter == 'selesai') {
        $query->where('status', 'Dikembalikan');
    }



    $riwayat = $query->get();

    return view('guru.riwayat', compact('riwayat', 'filter', 'search'));
}

public function hapusRiwayat($id)
{
    $peminjaman = Peminjaman::with('details.barang')->findOrFail($id);

    foreach ($peminjaman->details as $detail) {

        // Jika barang BELUM dikembalikan, kembalikan stoknya
        if ($detail->status_pengembalian == 'Belum') {

            $barang = $detail->barang;
            $barang->stok += $detail->jumlah; 
            $barang->save();
        }

        // Jika sedang "Menunggu Guru" dalam pengembalian → juga dianggap belum kembali
        if ($detail->status_pengembalian == 'Menunggu Guru' ||
            $detail->status_pengembalian == 'Menunggu Sarpras') {

            $barang = $detail->barang;
            $barang->stok += $detail->jumlah;
            $barang->save();
        }
    }

    // Hapus detail peminjaman
    $peminjaman->details()->delete();

    // Hapus transaksi utama
    $peminjaman->delete();

    return redirect()->back()->with('success', 'Riwayat berhasil dihapus dan stok dikembalikan.');
}




// 2. Terima barang dan masuk ke form peminjaman
public function formBarang(Request $request)
{
    session(['barangDipilih' => $request->barang]);

    return redirect()->route('peminjaman.form');
}



    public function getMapel(Request $request)
    {
        $q = $request->input('q');

        $query = Mapel::query();

        if ($q && trim($q) !== '') {
            $query->where('nama_mapel', 'like', '%' . $q . '%');
        }

        $mapel = $query->select('id_mapel', 'nama_mapel as nama')
                      ->orderBy('nama_mapel', 'asc')
                      ->get();

        return response()->json($mapel);
    }

public function form(Request $request)
{
    $barangDipilih = session('barangDipilih');

    if (!$barangDipilih || count($barangDipilih) === 0) {
        return redirect()
            ->route('peminjaman.kategori')
            ->with('error', 'Silakan pilih barang terlebih dahulu.');
    }

    return view('peminjaman_form', [
        'barangDipilih' => $barangDipilih
    ]);
}


// API SISWA + JURUSAN
public function getSiswa(Request $request)
{
    $siswa = Siswa::with('kelasRelasi.jurusan')
        ->where('nis', $request->nis)
        ->first();

    if (!$siswa) {
        return response()->json(null, 404);
    }

    return response()->json([
        'nama' => $siswa->nama,
        'kelas' => $siswa->kelasRelasi->nama_kelas ?? $siswa->kelas,
        'id_jurusan' => $siswa->kelasRelasi->id_jurusan ?? null,
    ]);
}

// API GURU
public function getGuruByJenis(Request $request)
{
    try {
        if ($request->jenis === 'jurusan') {
            $data = User::where('role','guru')
                ->where('id_jurusan', $request->id_jurusan)
                ->get(['id_user','nama']);
        } else {
            $data = User::where('role','guru')->get(['id_user','nama']);
        }

        return response()->json($data);

    } catch (\Throwable $e) {
        return response()->json([], 200); // ⬅️ PENTING
    }
}


// API MAPEL
public function getMapelByJenis(Request $request)
{
    try {
        if ($request->jenis === 'jurusan') {
            $data = Mapel::where('jenis_mapel','jurusan')
                ->where('id_jurusan', $request->id_jurusan)
                ->get(['id_mapel','nama_mapel as nama']);
        } elseif ($request->jenis === 'umum') {
            $data = Mapel::where('jenis_mapel','umum')
                ->get(['id_mapel','nama_mapel as nama']);
        } else {
            $data = Mapel::where('jenis_mapel','ekskul')
                ->get(['id_mapel','nama_mapel as nama']);
        }

        return response()->json($data);

    } catch (\Throwable $e) {
        return response()->json([], 200); // ⬅️ PENTING
    }
}




public function kategori()
{
    session()->forget('barangDipilih');

    $kategori = Kategori::all();

    return view('kategori', compact('kategori'));
}




    // Search barang berdasarkan kategori + input ketik
    public function getBarang(Request $request)
    {
        $query = Barang::where('kategori', $request->kategori);

        if ($request->q) {
            $query->where('nama_barang', 'like', '%' . $request->q . '%');
        }

        return $query->get(['id_barang', 'nama_barang', 'stok']);
    }

    // Store peminjaman
public function store(Request $request)
{

// di dalam method store(Request $request) paling atas
Log::info('peminjaman.store called', [
    'method' => request()->method(),
    'url'    => request()->fullUrl(),
    'inputs' => $request->all(),
]);

$request->validate([
    'nis' => 'required',
    'id_mapel' => 'required',
    'namaGuru' => 'required',
    'barang' => 'required|array',
    'barang.*.nama_barang' => 'required',
    'barang.*.jumlah' => 'required|integer|min:1',
]);


    $siswa = Siswa::where('nis', $request->nis)->first();
    $guru = User::where('nama', $request->namaGuru)
                 ->where('role', 'guru')
                 ->first();

    if (!$siswa || !$guru) {
        return back()->with('error', 'Data siswa atau guru tidak ditemukan!');
    }
$jurusanSiswa = $siswa->kelasRelasi->id_jurusan ?? null;

$mapel = Mapel::find($request->id_mapel);

// HANYA CEK JURUSAN JIKA MAPEL JURUSAN
if ($mapel->jenis_mapel === 'jurusan') {
    if ($guru->id_jurusan != $jurusanSiswa) {
        return back()->with('error', 'Guru tidak sesuai dengan jurusan siswa');
    }

    if ($mapel->id_jurusan != $jurusanSiswa) {
        return back()->with('error', 'Mapel tidak sesuai dengan jurusan siswa');
    }
}
// ===============================
// VALIDASI NOMOR WHATSAPP
// ===============================
$noWa = preg_replace('/^0/', '62', $request->no_siswa);

try {
    $cek = Http::post(config('services.whatsapp.url') . '/check-wa', [
    'number' => $noWa
]);


    if (!isset($cek['valid']) || $cek['valid'] !== true) {
        return back()->withInput()->with(
            'error',
            'Nomor WhatsApp tidak terdaftar atau tidak aktif.'
        );
    }
} catch (\Throwable $e) {
    return back()->withInput()->with(
        'error',
        'Gagal memverifikasi nomor WhatsApp. Coba lagi.'
    );
}


    DB::beginTransaction();
    try {
        // 1) Buat header peminjaman
        $peminjaman = Peminjaman::create([
            'id_siswa' => $siswa->id_siswa,
            'id_guru' => $guru->id_user,
            'id_mapel' => $request->id_mapel,
            'tanggal_pinjam' => now(),
            'ruangan' => $request->ruangan,
            'no_wa' => $request->no_siswa,
            'alasan' => $request->deskripsi,
            'status' => 'Diajukan', 
        ]);

        // 2) Loop barang -> buat detail
        foreach ($request->barang as $item) {

            $barang = Barang::where('nama_barang', $item['nama_barang'])->first();

            if (!$barang) continue;

            // Detail peminjaman harus punya status!
            DetailPeminjaman::create([
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'id_barang' => $barang->id_barang,
                'jumlah' => $item['jumlah'],
                'status_peminjaman' => 'Menunggu Guru',     // WAJIB
                'status_pengembalian' => 'Belum',           // WAJIB
                'tanggal_pengembalian' => null,
            ]);
        }
DB::commit();

try {
    WhatsappController::ajukanPeminjaman($peminjaman);
} catch (\Throwable $e) {
    Log::warning('WA gagal dikirim: ' . $e->getMessage());
}

// Hapus session agar tidak kembali ke form lagi
session()->forget('barangDipilih');

return redirect()->route('peminjaman.kategori')
        ->with('success', 'Peminjaman berhasil dibuat. Menunggu persetujuan guru.');

        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error create peminjaman: '.$e->getMessage());
        return back()->with('error', 'Gagal menyimpan peminjaman: '.$e->getMessage());
    }
}

}
