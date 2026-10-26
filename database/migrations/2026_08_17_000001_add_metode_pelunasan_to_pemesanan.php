<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                if (!Schema::hasColumn('pemesanan', 'jenis_deposit')) {
                    $table->enum('jenis_deposit', ['KTP', 'Uang Tunai'])->nullable()->after('status_pelunasan');
                }
                if (!Schema::hasColumn('pemesanan', 'metode_pelunasan')) {
                    $table->string('metode_pelunasan', 50)->nullable()->after('status_pelunasan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                if (Schema::hasColumn('pemesanan', 'jenis_deposit')) {
                    $table->dropColumn('jenis_deposit');
                }
                if (Schema::hasColumn('pemesanan', 'metode_pelunasan')) {
                    $table->dropColumn('metode_pelunasan');
                }
            });
        }
    }
};
