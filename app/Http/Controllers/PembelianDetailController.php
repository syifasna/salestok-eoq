<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PembelianDetailController extends Controller
{
    public function index()
    {
        $id_pembelian = session('id_pembelian');
        $bahanBaku = BahanBaku::orderBy('nama')->get();
        $supplier = Supplier::find(session('id_supplier'));
        $diskon = Pembelian::find($id_pembelian)->diskon ?? 0;

        if (! $supplier) {
            abort(404);
        }

        return view('pembelian_detail.index', compact('id_pembelian', 'bahanBaku', 'supplier', 'diskon'));
    }

    public function data($id)
    {
        $detail = PembelianDetail::with('bahanBaku')
            ->where('id_pembelian', $id)
            ->get();

        $data = [];
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = [];
            $row['kode_bahan']  = '<span class="label label-success">' . $item->bahanBaku->kode_bahan . '</span>';
            $row['nama']        = $item->bahanBaku->nama;
            $row['harga']       = 'Rp. ' . format_uang($item->harga);
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="' . $item->id_pembelian_detail . '" value="' . $item->jumlah . '">';
            $row['subtotal']    = 'Rp. ' . format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                <button onclick="deleteData(`' . route('pembelian_detail.destroy', $item->id_pembelian_detail) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                            </div>';
            $data[] = $row;

            $total += $item->subtotal;
            $total_item += $item->jumlah;
        }

        $data[] = [
            'kode_bahan' => '
            <div class="total hide">' . $total . '</div>
            <div class="total_item hide">' . $total_item . '</div>',
            'nama' => '',
            'harga'  => '',
            'jumlah' => '',
            'subtotal' => '',
            'aksi'    => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_bahan', 'jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian'   => 'required|integer',
            'id_bahan_baku'  => 'required|integer|exists:bahan_bakus,id',
        ]);

        $bahanBaku = BahanBaku::findOrFail($request->id_bahan_baku);

        $detail = new PembelianDetail();
        $detail->id_pembelian   = $request->id_pembelian;
        $detail->id_bahan_baku  = $request->id_bahan_baku;
        $detail->harga          = $bahanBaku->harga;
        $detail->jumlah         = 1; // default awal
        $detail->subtotal       = $bahanBaku->harga * 1;
        $detail->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = PembelianDetail::findOrFail($id);
        $detail->jumlah = $request->jumlah;
        $detail->subtotal = $detail->harga * $request->jumlah;
        $detail->save();

        return response()->json('Data berhasil diupdate', 200);
    }

    public function destroy($id)
    {
        $detail = PembelianDetail::findOrFail($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon, $total)
    {
        $bayar = $total - ($diskon / 100 * $total);
        $data  = [
            'totalrp'   => format_uang($total),
            'bayar'     => $bayar,
            'bayarrp'   => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar) . ' Rupiah')
        ];

        return response()->json($data);
    }
}
