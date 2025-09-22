<?php

namespace App\Http\Controllers;

use App\Models\Eoq;
use App\Models\BahanBaku;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EOQController extends Controller
{
    public function index()
    {
        // Ambil semua hasil EOQ + data bahan baku
        $data = Eoq::with('bahanBaku')->latest()->paginate(10);
        $bahanBaku = BahanBaku::all();

        $labels = $data->pluck('bahanBaku.nama');
        $eoqs   = $data->pluck('eoq_result');
        $rops   = $data->pluck('rop');

        return view('eoq.index', compact('data', 'bahanBaku', 'labels', 'eoqs', 'rops'));
    }

    public function generate($bahan_baku_id)
    {
        $bahan = BahanBaku::findOrFail($bahan_baku_id);
        $tahun = Carbon::now()->year;

        //1. Hitung Permintaan Tahunan (D)
        //Dari total penjualan produk, dikonversi ke kebutuhan bahan baku
        $total_pemakaian = PenjualanDetail::whereYear('penjualan_detail.created_at', $tahun)
            ->with('produk.bahanBaku')
            ->get()
            ->flatMap(function ($detail) {
                return $detail->produk->bahanBaku->map(function ($bb) use ($detail) {
                    return $bb->pivot->jumlah * $detail->jumlah;
                });
            })
            ->sum();

        $D = $total_pemakaian;

        //2. Biaya Pemesanan (S)
        //Hardcore fixed Rp 20.000 per order
        $S = 25000;

        //3. Biaya Penyimpanan (H)
        //Misalnya 10% dari harga/unit
        $H = $bahan->harga * 0.25;

        //4. Lead Time (rata-rata selisih pesan - datang)
        $lead_time = DB::table('pembelian_detail')
            ->join('pembelian', 'pembelian.id_pembelian', '=', 'pembelian_detail.id_pembelian')
            ->where('pembelian_detail.id_bahan_baku', $bahan->id)
            ->whereYear('pembelian.created_at', $tahun)
            ->avg(DB::raw('DATEDIFF(pembelian.updated_at, pembelian.created_at)')) ?? 7;

        $pemakaian_harian = $D > 0 ? ($D / 300) : 0;

        //5. Rumus EOQ
        $EOQ = $H > 0 ? sqrt((2 * $D * $S) / $H) : 0;

        //6. Reorder Point (ROP)
        $ROP = $lead_time * $pemakaian_harian;

        //7. Simpan hasil ke DB
        Eoq::create([
            'bahan_baku_id'     => $bahan->id,
            'permintaan_tahunan' => $D,
            'biaya_pemesanan'   => $S,
            'biaya_penyimpanan' => $H,
            'eoq_result'        => round($EOQ),
            'rop'               => round($ROP),
            'tanggal_hitung'    => now(),
        ]);

        return redirect()->back()->with('success', "EOQ untuk $bahan->nama berhasil dihitung!");
    }

    public function generateAll()
    {
        $bahanList = BahanBaku::all();
        foreach ($bahanList as $bahan) {
            $this->generate($bahan->id);
        }
        return redirect()->back()->with('success', 'EOQ untuk semua bahan berhasil dihitung ulang!');
    }

    public function reset()
    {
        Eoq::truncate(); // hapus semua data hasil perhitungan
        return redirect()->route('eoq.index')->with('success', 'Perhitungan EOQ berhasil direset!');
    }
}
