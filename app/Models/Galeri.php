<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'id_glamping',
        'foto',
        'keterangan'
    ];

    public function glamping()
    {
        return $this->belongsTo(Glamping::class,'id_glamping');
    }
}