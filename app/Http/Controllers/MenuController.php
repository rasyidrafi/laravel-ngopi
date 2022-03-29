<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "status" => "success",
            "data" => Menu::all()
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
            "name" => "required",
            "description" => "required",
            "price" => "required|numeric",
        ]);

        $newMenu = new Menu($request->all());
        $newMenu->created_by = $request->user->id;
        $newMenu->save();

        return response()->json([
            "status" => "success",
            "message" => "Menu created successfully",
            "data" => Menu::find($newMenu->id)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);

        $menu = Menu::find($request->id);
        if (!$menu) return response()->json([
            "status" => "error",
            "message" => "Data not found"
        ], 404);

        $valid = $request->only(["name", "description", "price"]);
        $menu->fill($valid);
        $menu->save();

        return response()->json([
            "status" => "success",
            "message" => "Menu updated successfully",
            "data" => Menu::find($menu->id)
        ]);
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
