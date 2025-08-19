<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bahanbaku.index');
    }

    public function data()
    {
        $bahanBaku = BahanBaku::orderBy('id', 'desc')->get();

        return datatables()
            ->of($bahanBaku)
            ->addIndexColumn()
            ->addColumn('aksi', function ($bahanBaku) {
                return '
                <div class="btn-group">
                    <button onclick="editForm(`' . route('bahan-baku.update', $bahanBaku->id) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`' . route('bahan-baku.destroy', $bahanBaku->id) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'satuan' => 'required|string|max:100',
            'stok'   => 'required|integer',
            'harga'  => 'required|integer',
        ]);

        // Ambil id terakhir dari tabel
        $lastId = BahanBaku::max('id') ?? 0;

        $bahanBaku = new BahanBaku();
        $bahanBaku->kode_bahan = 'BB' . tambah_nol_didepan($lastId + 1, 6);
        $bahanBaku->nama   = $request->nama;
        $bahanBaku->satuan = $request->satuan;
        $bahanBaku->stok   = $request->stok;
        $bahanBaku->harga  = $request->harga;
        $bahanBaku->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bahanBaku = BahanBaku::find($id);

        return response()->json($bahanBaku);
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
        $bahanBaku = BahanBaku::findOrFail($id);
        $bahanBaku->nama   = $request->nama;
        $bahanBaku->satuan = $request->satuan;
        $bahanBaku->stok   = $request->stok;
        $bahanBaku->harga  = $request->harga;
        $bahanBaku->update();

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
        $bahanBaku = BahanBaku::find($id);
        $bahanBaku->delete();

        return response(null, 204);
    }
}
