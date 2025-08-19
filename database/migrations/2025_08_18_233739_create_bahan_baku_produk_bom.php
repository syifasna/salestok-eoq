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
        Schema::create('bahan_baku_produk_bom', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('produk_id');
            $table->foreign('produk_id')
                ->references('id_produk')   
                ->on('produk')              
                ->cascadeOnDelete();
            $table->foreignId('bahan_baku_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah'); // kebutuhan bahan baku per 1 produk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku_produk_bom');
    }
};
