<?php

use App\Http\Controllers\API\BookmarkController;
use App\Http\Controllers\API\FolderBookmarks;
use App\Http\Controllers\API\FolderController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\TagController;
use App\Http\Resources\FolderBookmarksResource;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
	return $request->user();
});

Route::controller(RegisterController::class)->group(function () {
	Route::post("register", "register");
	Route::post("login", "login");
});

Route::middleware("auth:sanctum")->group(function () {
	//...
	Route::apiresource("folders", FolderController::class);
	Route::apiresource("/folders/{id}/bookmarks", FolderBookmarks::class);
	Route::apiresource("bookmarks", BookmarkController::class);
	// Route::apiresource("bookmarks.tags", TagController::class);
	Route::apiresource("tags", TagController::class);
	Route::post('search/bookmarks/', [SearchController::class, 'searchBookmarks'])
		->name('api.search.bookmarks');
	Route::post('search/tags/', [SearchController::class, 'searchTags'])
		->name('api.search.tags');
});
