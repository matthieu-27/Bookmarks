<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		$bookmark = new Bookmark();
		$bookmark->title = $input["title"];
		$bookmark->url = $input["url"];
		$bookmark->comment = $input["comment"];
		$bookmark->folder_id = $input["folder_id"];

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
		$regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

		$validator = Validator::make($input, [
			"title" => "required",
			"url" => "required|url|regex:" . $regex,
		]);
		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		// foreach ($request as $key => $value) {
		// 	$bookmark->$key = $value;
		// }

		if (isset($input["title"])) {
			$bookmark->title = $input["title"];
		}
		if (isset($input["url"])) {
			$bookmark->url = $input["url"];
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
		// || $folder->id != Auth::user()->getAuthIdentifier()
		if (is_null($bookmark)) {
			$this->sendError("Bookmark not found");
		}
		$bookmark->delete();
		return $this->sendResponse([], "Bookmark deleted succesfully");
	}
}
