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
        Schema::create('review', function (Blueprint $table) {
            $table->id('id_review');

            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_glamping')->nullable();
            $table->unsignedBigInteger('id_pemesanan')->nullable();

            $table->string('subjek')->nullable();
            $table->tinyInteger('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->text('balasan_admin')->nullable();
            $table->text('balasan_user')->nullable();

            $table->timestamps();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('id_glamping')
                  ->references('id_glamping')
                  ->on('glamping')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            $table->foreign('id_pemesanan')
                  ->references('id_pemesanan')
                  ->on('pemesanan')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
