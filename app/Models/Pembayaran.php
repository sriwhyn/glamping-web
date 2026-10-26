<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_pemesanan',
        'tanggal_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'snap_token',
        'status_verifikasi',
    ];

    
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }
}