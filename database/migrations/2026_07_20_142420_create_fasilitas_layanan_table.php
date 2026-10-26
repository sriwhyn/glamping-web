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
        Schema::create('fasilitas_layanan', function (Blueprint $table) {

            $table->id('id_fasilitas');

            $table->unsignedBigInteger('id_glamping');

            $table->string('nama',100);

            $table->text('deskripsi')->nullable();

            $table->string('gambar')->nullable();

            $table->timestamps();

            $table->foreign('id_glamping')
                  ->references('id_glamping')
                  ->on('glamping')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_layanan');
    }
};
