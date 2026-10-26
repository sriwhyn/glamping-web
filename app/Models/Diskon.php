<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = 'diskon';

    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'kode_diskon',
        'jenis_diskon',
        'nilai_diskon',
        'status',
    ];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_diskon', 'id_diskon');
    }
}
