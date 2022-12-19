<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class BookmarkController extends BaseController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		$bookmarks = Bookmark::byUser()->get();
		return response()->json($bookmarks);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{

		$input = $request->all();
		$regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

		$validator = Validator::make($input, [
			"title" => "required",
			"url" => "required|url|regex:" . $regex,
			"folder_id" => "integer|nullable"
		]);

		if ($validator->fails()) {
			return $this->sendError($validator->errors());
		}

		$bookmark = new Bookmark();

		if (isset($input["folder_id"])) {
			$folder = Folder::findOrFail($input["folder_id"]);
		} else {
			$folder = Folder::where("user_id", "=", auth()->user()->id)->first();
		}

		foreach ($input as $key => $value) {
			if (Schema::hasColumn("bookmarks", $key) && $key != "id" && $key != "user_id") {
				$bookmark->$key = $value;
			}
		}

		$bookmark->user()->associate(auth()->user());


		if (!Gate::allows('user_folder', $folder) || !Gate::allows('user_bookmark', $bookmark)) {
			return $this->sendError(null, "Unauthorized access to ressource", 403);
		}

		$bookmark->save();
		$folder->bookmarks()->attach($bookmark);

		return $this->sendResponse(
			new BookmarkResource($bookmark),
			"Bookmark created successfully."
		);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Bookmark  $bookmark
	 * @return \Illuminate\Http\Response
	 */
	public function show(Bookmark $bookmark)
	{
		if (!Gate::allows('user_bookmark', $bookmark)) {
			return $this->sendError(null, "Unauthorized access to bookmark", 403);
		}
		return response()->json($bookmark);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Bookmark  $bookmark
	 * @return mixed
	 */
	public function update(Request $request, Bookmark $bookmark)
	{
		//
		$input = $request->all();

		if (!Gate::allows('user_bookmark', $bookmark)) {
			return $this->sendError(null, "Unauthorized access to bookmark", 403);
		}

		$regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

		$validator = Validator::make($input, [
			"url" => "nullable|regex:" . $regex,
			"folder_id" => "integer|nullable"
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		if (isset($input["folder_id"])) {
			$folder = Folder::findOrFail($input["folder_id"]);
			$bookmark->folders()->detach();
			$bookmark->folders()->attach($folder);
		} else {
			$folder = $bookmark->folders()->first();
		}



		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError(null, "Unauthorized access to folder", 403);
		}

		foreach ($input as $key => $value) {
			if (isset($input[$key]) && $key != "id" && $key != "user_id") {
				if (Schema::hasColumn("bookmarks", $key)) {
					$bookmark->$key = $value;
				}
			}
		}

		$bookmark->save();

		return $this->sendResponse(
			new BookmarkResource($bookmark),
			"Bookmark updated successfully."
		);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Bookmark  $bookmark
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Bookmark $bookmark)
	{
		if (!Gate::allows('user_bookmark', $bookmark)) {
			return $this->sendError(null, "Unauthorized access to bookmark", 403);
		}
		$bookmark->folders()->detach();
		$bookmark->tags()->detach();
		$bookmark->delete();
		return $this->sendResponse([], "Bookmark deleted succesfully");
	}
}
