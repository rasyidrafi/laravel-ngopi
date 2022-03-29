<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'waktu_transaksi',
        'total_bayar',
        'nomor_meja',
        'kasir_id',
        'total_harga',
        'total_jumlah_pesanan',
        'kembalian',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "kasir_id",
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, "id", "kasir_id");
    }

    public function transaksi_detail()
    {
        return $this->hasMany(TransaksiDetail::class, "transaksi_id", "id");
    }
}
