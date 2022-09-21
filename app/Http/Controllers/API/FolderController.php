<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
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
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$folders = DB::table("folders")
			->where("user_id", "=", Auth::user()->getAuthIdentifier())
			->select("*")
			->get();
		return $this->sendResponse(FolderResource::collection($folders), "Success");
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

		$validator = Validator::make($input, [
			"name" => "required",
		]);
		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}
		$folder = new Folder();
		foreach ($input as $key => $value) {
			if (isset($input[$key])) {
				if (Schema::hasColumn('folders', $key)) {
					$folder->$key = $value;
				}
			}
		}
		if ($folder->root_id === NULL) {
			$folder->root_id = Folder::where('root_id', '=', null)->where('user_id', '=', Auth::user()->id)->get()->first()->id;
		}
		$folder->owner()->associate(Auth::user());
		if (!Gate::allows('user_folder', $folder)) {
			return $this->sendError(null, "Unauthorized access to parent folder", 403);
		}
		$folder->save();
		return $this->sendResponse(
			new FolderResource($folder),
			"Folder created successfully."
		);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Folder  $folder
	 * @return \Illuminate\Http\Response
	 */
	public function show(Folder $folder)
	{
		if (!Gate::allows('user_folder', $folder)) {
			return $this->sendError(null, "Unauthorized access to folder", 403);
		}

		return $this->sendResponse(
			new FolderResource($folder),
			"Folder retrieved successfully."
		);
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
			"root_id" => "integer",
			"id" => "integer"
		]);

		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		if (!Gate::allows("user_folder", $folder)) {
			return $this->sendError("Unauthorized access to folder", "Unauthorized access to folder", 403);
		}



		if ($folder->root_id === NULL && $input["root_id"] != NULL) {
			return $this->sendError(null, "Can't change root_id of root folder !", 403);
		}
		if ($folder->id != $input["id"]) {
			return $this->sendError(null, "Incorrect id", 403);
		}


		foreach ($input as $key => $value) {
			if (isset($input[$key])) {
				if (Schema::hasColumn("folders", $key)) {
					$folder->$key = $value;
				}
			}
		}


		$folder->save();
		return $this->sendResponse(
			new FolderResource($folder),
			"Folder updated successfully."
		);
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
		$folder->delete();
		return $this->sendResponse([], "Folder deleted succesfully");
	}
}
