<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FolderController extends BaseController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
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
		//
		$input = $request->all();
		$validator = Validator::make($input, [
			"name" => "required",
		]);
		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}

		$folder = new Folder();
		$folder->name = $input["name"];
		$folder->owner()->associate(Auth::user());
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
		//
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
			"name" => "required",
		]);
		if ($validator->fails()) {
			return $this->sendError("Validation Error.", $validator->errors());
		}
		if (isset($input["name"])) {
			$folder->name = $input["name"];
		}
		if (isset($input["root_id"])) {
			$folder->root_id = $input["root_id"];
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
		// || $folder->id != Auth::user()->getAuthIdentifier()
		if (is_null($folder)) {
			$this->sendError("Folder not found");
		}
		$folder->delete();
		return $this->sendResponse([], "Folder deleted succesfully");
	}
}
