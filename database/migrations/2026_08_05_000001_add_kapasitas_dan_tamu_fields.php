<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Menambahkan kolom kapasitas glamping dan detail pemesanan lengkap (5 kolom)
    public function up(): void
    {
        Schema::table('glamping', function (Blueprint $table) {
            if (!Schema::hasColumn('glamping', 'kapasitas_dewasa')) {
                $table->integer('kapasitas_dewasa')->default(2)->after('kapasitas');
                $table->decimal('harga_extrabed', 10, 2)->nullable()->after('harga');
            }
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemesanan', 'jumlah_dewasa')) {
                $table->integer('jumlah_dewasa')->default(1)->after('jumlah_tamu');
                $table->integer('jumlah_anak')->default(0)->after('jumlah_dewasa');
                $table->integer('jumlah_unit_pesan')->default(1)->after('jumlah_anak');
                $table->integer('jumlah_extrabed')->default(0)->after('jumlah_unit_pesan');
                $table->decimal('biaya_extrabed', 10, 2)->default(0)->after('jumlah_extrabed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('glamping', function (Blueprint $table) {
            $table->dropColumn(['kapasitas_dewasa', 'harga_extrabed']);
        });

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dewasa', 'jumlah_anak', 'jumlah_unit_pesan', 'jumlah_extrabed', 'biaya_extrabed']);
        });
    }
};
