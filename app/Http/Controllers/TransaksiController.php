<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Menu;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Transaksi::where("kasir_id", $request->user->id)->get();
        return response()->json([
            "status" => "success",
            "data" => $data,
        ]);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $id)
    {
        if(!isset($id)) return response()->json([
            "status" => "error",
            "message" => "id param is required",
        ], 400);

        $transaksi = Transaksi::find($id);

        if (!$transaksi) return response()->json([
            "status" => "error",
            "message" => "transaksi not found",
        ], 404);

        if ($transaksi->kasir_id != $request->user->id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not authorized to access this data",
            ], 403);
        }

        $transaksi->menu = $transaksi->transaksi_detail->map(function ($item) {
            $data = array_merge($item->menu->toArray(), $item->toArray());
            $data["subtotal"] = $data["price"] * $data["jumlah"];
            unset($data["menu"]);
            return $data;
        });

        unset($transaksi->transaksi_detail);

        return response()->json([
            "status" => "success",
            "data" => $transaksi,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "total_bayar" => "required|numeric",
            "nomor_meja" => "required|numeric",
            "menu" => "required|array",
        ]);

        $data = $request->only(["total_bayar", "nomor_meja"]);
        $data["kasir_id"] = $request->user->id;
        $data['total_harga'] = 0;
        $data["total_jumlah_pesanan"] = 0;
        $menuHolder = [];

        foreach ($request->menu as $menu_data) {
            if(!array_key_exists("id", $menu_data)) return response()->json([
                "status" => "error",
                "message" => "Menu id is required",
            ], 400);

            $menu = Menu::find($menu_data["id"]);
            if (!$menu) return response()->json([
                "status" => "error",
                "message" => "Menu not found",
            ], 404);

            if(!array_key_exists("jumlah", $menu_data) || $menu_data["jumlah"] <= 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "Jumlah menu harus lebih dari 0",
                ], 400);
            }

            $data["total_jumlah_pesanan"] += $menu_data["jumlah"];
            $data['total_harga'] += ($menu->price * $menu_data["jumlah"]);
            $menu->jumlah = $menu_data["jumlah"];
            $menu->subtotal = $menu->price * $menu_data["jumlah"];
            $menuHolder[] = $menu;
        }

        if ($request->total_bayar < $data['total_harga']) {
            return response()->json([
                "status" => "error",
                "message" => "Total bayar is not enough",
            ], 400);
        }

        $data["kembalian"] = $request->total_bayar - $data['total_harga'];

        $transaksi = Transaksi::create($data);

        foreach ($request->menu as $menu_data) {
            TransaksiDetail::create([
                "transaksi_id" => $transaksi->id,
                "menu_id" => $menu_data["id"],
                "jumlah" => $menu_data["jumlah"],
            ]);
        }

        return $this->detail($request, $transaksi->id);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
