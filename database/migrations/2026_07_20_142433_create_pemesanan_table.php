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
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id('id_pemesanan');

            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_glamping');

            $table->date('tanggal_checkin');
            $table->date('tanggal_checkout');
            $table->string('jam_checkin', 20)->default('14:00 WIB')->nullable();
            $table->integer('jumlah_tamu');

            $table->enum('tipe_pembayaran', ['dp', 'full'])->default('dp');
            $table->decimal('total_harga', 10, 2);
            $table->string('snap_token')->nullable();

            $table->enum('status', [
                'Menunggu Pembayaran',
                'Menunggu Verifikasi',
                'Dikonfirmasi',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu Pembayaran');

            $table->timestamps();

            $table->unsignedBigInteger('id_promo')->nullable();

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('id_glamping')
                  ->references('id_glamping')
                  ->on('glamping')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('id_promo')
                  ->references('id_promo')
                  ->on('promo')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
