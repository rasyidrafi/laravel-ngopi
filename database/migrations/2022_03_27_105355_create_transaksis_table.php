<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("kasir_id");
            $table->foreign("kasir_id")->references("id")->on("users");
            $table->dateTime("waktu_transaksi")->default(now());
            $table->integer("total_harga");
            $table->integer("total_jumlah_pesanan");
            $table->integer("total_bayar");
            $table->integer("kembalian")->default(0);
            $table->integer("nomor_meja");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
    }
}
