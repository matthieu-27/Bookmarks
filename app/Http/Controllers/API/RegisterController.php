<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
	/**
	 * Register api
	 *
	 * @return mixed
	 */
	public function register(Request $request)
	{
		$email_check = Validator::make($request->all(), ["email" => "required|email|unique:users"]);
		if($email_check->fails()){
			return $this->sendError($email_check->errors(), 401);
		}
		$validator = Validator::make($request->all(), [
			"name" => "required",
			"password" => "required",
			"c_password" => "required|same:password",
		]);
		if ($validator->fails()) {
			return $this->sendError($validator->errors(), 401);
		}
		$input = $request->all();
		$input["password"] = bcrypt($input["password"]);
		$user = User::create($input);
		$success["token"] = $user->createToken("bookmarks")->plainTextToken;
		$success["name"] = $user->name;
		return $this->sendResponse($success, "User register successfully.");
	}
	/**
	 * Login api
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function login(Request $request)
	{
		if (
			Auth::attempt([
				"email" => $request->email,
				"password" => $request->password,
			])
		) {
			/** @var \App\Models\User */
			$user = Auth::user();
			$success["token"] = $user->createToken("MyApp")->plainTextToken;
			$success["name"] = $user->name;
			$success["id"] = $user->id;
			$success["email"] = $user->email;
			$success["root_id"] = $user->getRootId();
			return response()->json($success);
		} else {
			return response()->json("Login failed", 403);
		}
	}

}
