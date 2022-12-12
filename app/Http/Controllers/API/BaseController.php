<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
	/**
	 * success response method.
	 *
	 * @return mixed
	 */
	public function sendResponse($data, $message, $code = 200)
	{
		return response()->json($data, $code);
	}
	/**
	 * return error response.
	 *
	 * @return mixed
	 */
	public function sendError($error, $errorMessages = [], $code = 404)
	{



		return response()->json($error, $code);
	}
}
