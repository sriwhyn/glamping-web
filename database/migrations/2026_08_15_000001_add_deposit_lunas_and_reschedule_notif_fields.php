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
                if (!Schema::hasColumn('pemesanan', 'status_pelunasan')) {
                    $table->enum('status_pelunasan', ['Belum Lunas', 'Lunas'])->default('Belum Lunas')->after('status');
                }
                if (!Schema::hasColumn('pemesanan', 'nominal_deposit')) {
                    $table->decimal('nominal_deposit', 10, 2)->default(0)->after('status_pelunasan');
                }
                if (!Schema::hasColumn('pemesanan', 'status_deposit')) {
                    $table->enum('status_deposit', ['Belum Bayar', 'Ditahan', 'Dikembalikan', 'Dipotong Kerusakan'])->default('Belum Bayar')->after('nominal_deposit');
                }
                if (!Schema::hasColumn('pemesanan', 'denda_kerusakan')) {
                    $table->decimal('denda_kerusakan', 10, 2)->default(0)->after('status_deposit');
                }
                if (!Schema::hasColumn('pemesanan', 'catatan_kerusakan')) {
                    $table->text('catatan_kerusakan')->nullable()->after('denda_kerusakan');
                }
                if (!Schema::hasColumn('pemesanan', 'reschedule_at')) {
                    $table->timestamp('reschedule_at')->nullable()->after('catatan_kerusakan');
                }
                if (!Schema::hasColumn('pemesanan', 'is_reschedule_notified')) {
                    $table->boolean('is_reschedule_notified')->default(1)->after('reschedule_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $columns = [
                    'status_pelunasan',
                    'nominal_deposit',
                    'status_deposit',
                    'denda_kerusakan',
                    'catatan_kerusakan',
                    'reschedule_at',
                    'is_reschedule_notified',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('pemesanan', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
