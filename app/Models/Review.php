<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review';

    protected $primaryKey = 'id_review';

    protected $fillable = [
        'id_user',
        'id_glamping',
        'id_pemesanan',
        'subjek',
        'rating',
        'komentar',
        'balasan_admin',
        'balasan_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function glamping()
    {
        return $this->belongsTo(Glamping::class, 'id_glamping');
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
}