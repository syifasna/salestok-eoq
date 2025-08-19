<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EOQ extends Model
{
    protected $table = 'eoq'; // karena nama tabelnya bukan jamak
    protected $fillable = [
        'bahan_baku_id',
        'permintaan_tahunan',
        'biaya_pemesanan',
        'biaya_penyimpanan',
        'eoq_result',
        'rop',
        'tanggal_hitung',
    ];

    protected $casts = [
        'tanggal_hitung' => 'date',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    /* --- optional helper untuk view --- */
    public function getFrekuensiAttribute()
    {
        if ($this->eoq_result <= 0) return 0;
        return (int) ceil($this->permintaan_tahunan / $this->eoq_result);
    }

    public function getJedaHariAttribute()
    {
        return $this->frekuensi > 0 ? (int) ceil(365 / $this->frekuensi) : 0;
    }
}
