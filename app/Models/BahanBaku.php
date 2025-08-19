<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_bakus';
    public $timestamps = true;

    protected $guarded = ['id'];

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class, 'id_bahan_baku', 'id');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'bahan_baku_produk_bom', 'bahan_baku_id', 'produk_id')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }

    public function eoqs()
    {
        return $this->hasMany(\App\Models\Eoq::class, 'bahan_baku_id');
    }

}
