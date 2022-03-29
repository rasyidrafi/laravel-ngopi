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
        $transaksi = Transaksi::find($id);

        if ($transaksi->kasir_id != $request->user->id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not authorized to access this data",
            ], 403);
        }

        $data = $transaksi;
        $data->detail = null;

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

        $menuHolder = [];
        foreach ($request->menu as $menu_id) {
            $menu = Menu::find($menu_id);
            $data['total_harga'] += $menu->harga;
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

        foreach ($request->menu as $menu_id) {
            TransaksiDetail::create([
                "transaksi_id" => $transaksi->id,
                "menu_id" => $menu_id,
            ]);
        }

        unset($data["kasir_id"]);
        $data["menu"] = $menuHolder;

        return response()->json([
            "status" => "success",
            "data" => $data,
        ]);
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
