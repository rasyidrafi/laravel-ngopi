<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, "id", "transaksi_id");
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, "id", "menu_id");
    }
}
