<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sawit_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('quantity');            // jumlah sawit yang ditanam dalam batch ini
            $table->unsignedInteger('quantity_harvested')->default(0); // sudah dipanen berapa dari batch ini

            $table->unsignedBigInteger('cost');             // total coin yang dikeluarkan saat tanam
            $table->timestamp('planted_at');                // waktu tanam (untuk hitung 1 menit)
            $table->timestamp('fully_harvested_at')->nullable(); // diisi jika semua sudah dipanen

            $table->enum('status', ['growing', 'ready', 'harvested'])->default('growing');
            // growing    → belum 1 menit
            // ready      → sudah 1 menit, siap panen
            // harvested  → semua sudah dipanen

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sawit_plants');
    }
};