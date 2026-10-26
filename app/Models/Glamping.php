<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Glamping extends Model
{
    protected $table = 'glamping';

    protected $primaryKey = 'id_glamping';

    protected $fillable = [
        'id_tipe',
        'nama_glamping',
        'harga',
        'harga_extrabed',
        'kapasitas',
        'kapasitas_dewasa',
        'jumlah_unit',
        'deskripsi',
        'gambar',
        'status'
    ];

    public function tipeGlamping()
    {
        return $this->belongsTo(TipeGlamping::class,'id_tipe');
    }

    public function fasilitas()
    {
        return $this->hasMany(FasilitasLayanan::class,'id_glamping');
    }

    public function fasilitasLayanan()
    {
        return $this->hasMany(FasilitasLayanan::class,'id_glamping');
    }

    public function galeri()
    {
        return $this->hasMany(Galeri::class,'id_glamping');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class,'id_glamping');
    }

    public function review()
    {
        return $this->hasMany(Review::class,'id_glamping');
    }

}