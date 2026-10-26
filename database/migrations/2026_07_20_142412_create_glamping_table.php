<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Jalankan migrasi tabel glamping
    public function up(): void
    {
        Schema::create('glamping', function (Blueprint $table) {
            $table->id('id_glamping');
            $table->unsignedBigInteger('id_tipe');
            $table->string('nama_glamping', 100);
            $table->decimal('harga', 10, 2);
            $table->integer('kapasitas');
            $table->integer('jumlah_unit')->default(1);
            $table->text('deskripsi');
            $table->string('gambar');
            $table->enum('status', ['tersedia', 'tidak tersedia']);
            $table->timestamps();

            $table->foreign('id_tipe')
                  ->references('id_tipe')
                  ->on('tipe_glamping')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('glamping');
    }
};
