<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini menyimpan semua history aktivitas user.
     * Digunakan untuk menampilkan Log di halaman Kebun Sawitku.
     */
    public function up(): void
    {
        Schema::create('sawit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sawit_plant_id')->nullable()->constrained()->onDelete('set null');
            // nullable karena jika data plant dihapus, history tetap ada

            $table->enum('type', ['tanam', 'panen']);       // jenis aktivitas

            $table->unsignedInteger('quantity');            // berapa sawit yang ditanam/dipanen
            $table->unsignedBigInteger('amount');           // nilai coin (kurang saat tanam, tambah saat panen)
            $table->enum('coin_flow', ['out', 'in']);       // out = berkurang, in = bertambah

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sawit_transactions');
    }
};