<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';

    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'id_glamping',
        'tanggal_checkin',
        'jam_checkin',
        'tanggal_checkout',
        'jumlah_tamu',
        'jumlah_dewasa',
        'jumlah_anak',
        'jumlah_unit_pesan',
        'jumlah_extrabed',
        'biaya_extrabed',
        'tipe_pembayaran',
        'total_harga',
        'snap_token',
        'status',
        'status_pelunasan',
        'metode_pelunasan',
        'jenis_deposit',
        'nominal_deposit',
        'status_deposit',
        'denda_kerusakan',
        'catatan_kerusakan',
        'reschedule_at',
        'is_reschedule_notified',
        'reschedule_count',
        'id_diskon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_user');
    }

    public function glamping()
    {
        return $this->belongsTo(Glamping::class,'id_glamping');
    }

    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon', 'id_diskon');
    }

    public function promo()
    {
        return $this->diskon();
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function semuaPembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_pemesanan', 'id_pemesanan')->orderBy('id_pembayaran', 'asc');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_pemesanan', 'id_pemesanan');
    }

    // Hitung jumlah anak
    public function getJumlahAnakAttribute()
    {
        return max(0, (int) $this->jumlah_tamu - (int) $this->jumlah_dewasa);
    }

    // Hitung biaya ekstra bed
    public function getBiayaExtrabedAttribute()
    {
        if ($this->jumlah_extrabed <= 0) return 0;
        $hargaEB = ($this->glamping && $this->glamping->harga_extrabed > 0)
            ? $this->glamping->harga_extrabed : 150000;
        $start = new \DateTime($this->tanggal_checkin);
        $end   = new \DateTime($this->tanggal_checkout);
        $malam = max(1, $start->diff($end)->days);
        return $this->jumlah_extrabed * $hargaEB * $malam;
    }

    // Nominal DP
    public function getNominalDpAttribute()
    {
        return $this->total_harga * 0.5;
    }

    // Sisa pelunasan
    public function getSisaPelunasanAttribute()
    {
        if ($this->status_pelunasan == 'Lunas') {
            return 0;
        }
        return $this->total_harga * 0.5;
    }

    // Total dibayar
    public function getDibayarAttribute()
    {
        if ($this->status == 'Dibatalkan') {
            return 0;
        }
        if ($this->status_pelunasan == 'Lunas') {
            return $this->total_harga;
        }
        if ($this->pembayaran) {
            return $this->pembayaran->jumlah_bayar;
        }
        if (in_array($this->status, ['Dikonfirmasi', 'Selesai'])) {
            return $this->total_harga * 0.5;
        }
        return 0;
    }

    // Sisa pembayaran
    public function getSisaBayarAttribute()
    {
        if ($this->status == 'Dibatalkan' || $this->status_pelunasan == 'Lunas') {
            return 0;
        }
        return max(0, $this->total_harga - $this->dibayar);
    }

    // Batas waktu
    public function getBatasWaktuAttribute()
    {
        return $this->created_at ? $this->created_at->addHour() : now()->addHour();
    }

    // Sisa detik
    public function getSisaDetikAttribute()
    {
        if (!$this->created_at) return 3600;
        $deadline = $this->created_at->addHour()->timestamp;
        return max(0, $deadline - time());
    }

    // Selesaikan & batalkan pesanan otomatis
    public static function cancelExpiredOrders()
    {
        // Batalkan pesanan kadaluarsa HANYA yang tidak sedang ditolak untuk re-upload
        self::where('status', 'Menunggu Pembayaran')
            ->where('created_at', '<', now()->subHour())
            ->where(function($q) {
                $q->whereDoesntHave('pembayaran')
                  ->orWhereHas('pembayaran', function($p) {
                      $p->where('status_verifikasi', '!=', 'Ditolak');
                  });
            })
            ->update(['status' => 'Dibatalkan']);

        // Selesaikan reservasi yang tanggal checkout nya sudah tercapai / lewat (Otomatis Selesai!)
        self::whereIn('status', ['Dikonfirmasi', 'Menunggu Verifikasi'])
            ->where('tanggal_checkout', '<=', now()->toDateString())
            ->update(['status' => 'Selesai']);
    }
}