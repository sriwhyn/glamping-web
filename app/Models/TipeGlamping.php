<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeGlamping extends Model
{
    protected $table = 'tipe_glamping';

    protected $primaryKey = 'id_tipe';

    protected $fillable = [
        'nama_tipe'
    ];

    public function glamping()
    {
        return $this->hasMany(Glamping::class,'id_tipe');
    }
}