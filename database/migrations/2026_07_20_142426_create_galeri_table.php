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
        Schema::create('galeri', function (Blueprint $table) {

            $table->id('id_galeri');

            $table->unsignedBigInteger('id_glamping')->nullable();

            $table->string('foto');

            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->foreign('id_glamping')
                  ->references('id_glamping')
                  ->on('glamping')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};
