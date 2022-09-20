<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
		$bookmarks = DB::table("bookmarks")
			->join("folders", "bookmarks.folder_id", "=", "folders.id")
			->where("user_id", "=", Auth::user()->getAuthIdentifier())
			->select("bookmarks.*")
			->get();
		return $this->sendResponse(
			BookmarkResource::collection($bookmarks),
			"Success"
		);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
		$input = $request->all();
		$regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

		$validator = Validator::make($input, [
			"title" => "required",
			"url" => "required|url|regex:" . $regex,
			"folder_id" => "integer"
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		$folder = Folder::findOrFail($input["folder_id"]);

		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError("Unauthorized access to folder", "Unauthorized access to folder", 403);
		}
		$bookmark = new Bookmark();
		foreach ($input as $key => $value) {
			if (Schema::hasColumn("bookmarks", $key)) {
				$bookmark->$key = $value;
			}
		}
		$bookmark->save();
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
		return $this->sendResponse(
			new BookmarkResource($bookmark),
			"Bookmark retrieved successfully."
		);
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
			"url" => "regex:" . $regex,
			"folder_id" => "integer"
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		if (!isset($input["folder_id"])) {
			$folder = $bookmark->folder()->first();
		} else {
			if ($input["folder_id"] != $bookmark->folder()->first()->id) {
				$folder = Folder::findOrFail($input["folder_id"]);
			}
		}

		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError("Unauthorized access to folder", "Unauthorized access to folder", 403);
		}


		foreach ($input as $key => $value) {
			if (isset($input[$key])) {
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
		$bookmark->delete();
		return $this->sendResponse([], "Bookmark deleted succesfully");
	}
}
