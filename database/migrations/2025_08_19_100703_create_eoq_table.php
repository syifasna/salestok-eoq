<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eoq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->integer('permintaan_tahunan');
            $table->decimal('biaya_pemesanan', 10, 2);
            $table->decimal('biaya_penyimpanan', 10, 2);
            $table->integer('eoq_result');
            $table->integer('rop');
            $table->date('tanggal_hitung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eoq');
    }
};
