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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');

            $table->unsignedBigInteger('id_pemesanan');
            $table->date('tanggal_pembayaran');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->string('metode_pembayaran', 50);
            $table->string('bukti_pembayaran')->nullable();
            $table->string('snap_token')->nullable();

            $table->enum('status_verifikasi', [
                'Menunggu',
                'Diterima',
                'Ditolak'
            ])->default('Menunggu');

            $table->timestamps();

            $table->foreign('id_pemesanan')
                ->references('id_pemesanan')
                ->on('pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
