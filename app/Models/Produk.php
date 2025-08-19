<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];
    public $timestamps = true;

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_produk', 'id_produk');
    }

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'bahan_baku_produk_bom', 'produk_id', 'bahan_baku_id')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }
}
