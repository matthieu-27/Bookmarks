<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FolderBookmarksResource;
use App\Http\Resources\FolderResource;
use App\Models\Bookmark;
use App\Models\Folder;
use Database\Factories\FolderFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class FolderController extends BaseController
{
	/**
	 * Display a listing of the user resource.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
	{
		$folder = Folder::byUser()->rootFolder()->first();
		$folders = $folder->children()->get();

		return response()->json(FolderResource::collection($folders));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		$input = $request->all();

		$validator = Validator::make($input, [
			"name" => "required",
			"parent_id" => "integer|nullable"
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}
		$folder = new Folder();
		$folder->user_id = auth()->user()->id;

		if (isset($input['parent_id'])) {
			try {
				$parent = Folder::findOrFail($input["parent_id"]);
			} catch (\Throwable $th) {
				return response()->json("Cannot find folder.", 404);
			}
		} else {
			$parent = Folder::byUser()->rootFolder()->first();
		}

		if (!Gate::allows('user_folder', $parent)) {
			return response()->json("Unauthorized access to parent folder", 403);
		}

		$folder->parent_id = $parent->id;

		foreach ($input as $key => $value) {
			if (isset($input[$key])) {
				if (Schema::hasColumn('folders', $key) && !str_contains($key, "id")) {
					$folder->$key = $value;
				}
			}
		}

		$folder->save();

		return response()->json(new FolderResource($folder));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Folder  $folder
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show(Folder $folder)
	{
		if (!Gate::allows('user_folder', $folder)) {
			return $this->sendError(null, "Unauthorized access to folder", 403);
		}

		return response()->json(new FolderBookmarksResource($folder));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Folder  $folder
	 * @return mixed
	 */
	public function update(Request $request, Folder $folder)
	{
		//
		$input = $request->all();

		$validator = Validator::make($input, [
			"name" => "string",
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError("Unauthorized access to folder", "Unauthorized access to folder", 403);
		}


		foreach ($input as $key => $value) {
			if (isset($input[$key]) && $key != "id" && $key != "user_id") {
				if (Schema::hasColumn("folders", $key)) {
					$folder->$key = $value;
				}
			}
		}

		$folder->save();

		return response()->json($folder);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Folder  $folder
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Folder $folder)
	{
		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError("Unauthorized access to folder", "Unauthorized access to folder", 403);
		}
		$bookmarks = $folder->bookmarks()->get();
		foreach ($bookmarks as $bookmark) {
			$bookmark->delete();
		}
		$folder->bookmarks()->detach();
		$folder->tags()->detach();
		$folder->delete();
		return $this->sendResponse([], "Folder deleted succesfully");
	}
}
