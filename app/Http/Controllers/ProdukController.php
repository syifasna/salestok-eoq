<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\BahanBaku;
use PDF;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $produkHampirHabis = Produk::where('stok', '<=', 5)->get();
        $bahanBaku = BahanBaku::all();

        return view('produk.index', compact('kategori', 'produkHampirHabis', 'bahanBaku'));
    }


    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            // ->orderBy('kode_produk', 'asc')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="' . $produk->id_produk . '">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">' . $produk->kode_produk . '</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('stok', function ($produk) {
                return $produk->stok;
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('produk.update', $produk->id_produk) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`' . route('produk.destroy', $produk->id_produk) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $bahanBaku = BahanBaku::all();
        return view('produk.form', compact('kategori', 'bahanBaku'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // 1. Validasi input
    $request->validate([
        'nama_produk'  => 'required|unique:produk,nama_produk',
        'id_kategori'  => 'required|exists:kategori,id_kategori', // fix disini
        'merk'         => 'nullable|string',
        'harga_beli'   => 'required|integer',
        'harga_jual'   => 'required|integer',
        'diskon'       => 'nullable|integer',
        'stok'         => 'required|integer',
        'bahan_baku'   => 'nullable|array',
        'bahan_baku.*.id'     => 'nullable|exists:bahan_bakus,id',
        'bahan_baku.*.jumlah' => 'nullable|integer|min:1',
    ]);

    // 2. Generate kode produk otomatis
    $lastProduk   = Produk::latest('id_produk')->first();
    $kodeProduk   = 'P' . str_pad(($lastProduk ? $lastProduk->id_produk + 1 : 1), 6, '0', STR_PAD_LEFT);

    // 3. Simpan produk
    $produk = Produk::create([
        'nama_produk' => $request->nama_produk,
        'id_kategori' => $request->id_kategori,
        'merk'        => $request->merk,
        'harga_beli'  => $request->harga_beli,
        'harga_jual'  => $request->harga_jual,
        'diskon'      => $request->diskon ?? 0,
        'stok'        => $request->stok,
        'kode_produk' => $kodeProduk,
    ]);

    // 4. Simpan bahan baku ke pivot (jika ada)
    if ($request->has('bahan_baku')) {
        foreach ($request->bahan_baku as $item) {
            if (!empty($item['id']) && !empty($item['jumlah'])) {
                $produk->bahanBaku()->attach($item['id'], [
                    'jumlah' => $item['jumlah'],
                ]);
            }
        }
    }

    return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        return response()->json($produk);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('produk.pdf');
    }
}
