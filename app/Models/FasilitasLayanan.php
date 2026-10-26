<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasLayanan extends Model
{
    protected $table = 'fasilitas_layanan';

    protected $primaryKey = 'id_fasilitas';

    protected $fillable = [
        'id_glamping',
        'nama',
        'deskripsi',
        'gambar'
    ];

    public function glamping()
    {
        return $this->belongsTo(Glamping::class,'id_glamping');
    }
}