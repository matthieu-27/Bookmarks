<?php

use App\Http\Controllers\UserController;
use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('home', function () {
	return view('home');
})->middleware('auth');

Route::get("/", function () {
	return view("home");
})->middleware("auth");

Route::get('admin', [UserController::class, 'checkAdmin'])->middleware('auth')->name("admin.index");
Route::delete('admin/{id}', [UserController::class, 'delete'])->middleware('auth')->name("user.destroy");
