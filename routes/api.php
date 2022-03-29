<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post("/auth/login", [AuthController::class, "login"]);

Route::middleware("auth.token")->group(function () {
    Route::prefix("auth")->group(function () {
        Route::middleware("only.admin")->post("register", [AuthController::class, "register"]);
    });

    Route::middleware("only.manager")->prefix("menu")->group(function () {
        Route::get("/", [App\Http\Controllers\MenuController::class, "index"]);
        Route::post("/", [App\Http\Controllers\MenuController::class, "store"]);
        Route::put("/", [App\Http\Controllers\MenuController::class, "update"]);
        // Route::get("/{id}", [App\Http\Controllers\MenuController::class, "show"]);
        // Route::delete("/{id}", [App\Http\Controllers\MenuController::class, "destroy"]);
    });

    Route::middleware("only.kasir")->prefix("transaksi")->group(function () {
        Route::get("/", [App\Http\Controllers\TransaksiController::class, "index"]);
        Route::get("/{id}", [App\Http\Controllers\TransaksiController::class, "detail"]);
        Route::post("/", [App\Http\Controllers\TransaksiController::class, "store"]);
        // Route::put("/", [App\Http\Controllers\TransaksiController::class, "update"]);
        // Route::delete("/{id}", [App\Http\Controllers\TransaksiController::class, "destroy"]);
    });

    Route::middleware("only.manager")->prefix("transaksi-manager")->group(function() {
        Route::get("/", [App\Http\Controllers\TransaksiController::class, "index_manager"]);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
