<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('promo') && !Schema::hasTable('diskon')) {
            Schema::rename('promo', 'diskon');
        }

        if (Schema::hasTable('diskon')) {
            Schema::table('diskon', function (Blueprint $table) {
                if (Schema::hasColumn('diskon', 'id_promo') && !Schema::hasColumn('diskon', 'id_diskon')) {
                    $table->renameColumn('id_promo', 'id_diskon');
                }
                if (Schema::hasColumn('diskon', 'kode_promo') && !Schema::hasColumn('diskon', 'kode_diskon')) {
                    $table->renameColumn('kode_promo', 'kode_diskon');
                }
            });
        }

        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                if (Schema::hasColumn('pemesanan', 'id_promo') && !Schema::hasColumn('pemesanan', 'id_diskon')) {
                    $table->renameColumn('id_promo', 'id_diskon');
                }
                if (!Schema::hasColumn('pemesanan', 'reschedule_count')) {
                    $table->integer('reschedule_count')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                if (Schema::hasColumn('pemesanan', 'reschedule_count')) {
                    $table->dropColumn('reschedule_count');
                }
                if (Schema::hasColumn('pemesanan', 'id_diskon') && !Schema::hasColumn('pemesanan', 'id_promo')) {
                    $table->renameColumn('id_diskon', 'id_promo');
                }
            });
        }

        if (Schema::hasTable('diskon')) {
            Schema::table('diskon', function (Blueprint $table) {
                if (Schema::hasColumn('diskon', 'id_diskon') && !Schema::hasColumn('diskon', 'id_promo')) {
                    $table->renameColumn('id_diskon', 'id_promo');
                }
                if (Schema::hasColumn('diskon', 'kode_diskon') && !Schema::hasColumn('diskon', 'kode_promo')) {
                    $table->renameColumn('kode_diskon', 'kode_promo');
                }
            });

            if (!Schema::hasTable('promo')) {
                Schema::rename('diskon', 'promo');
            }
        }
    }
};
