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
	public function sendResponse($result, $message, $code = 200)
	{
		$response = [
			"success" => true,
			"data" => $result,
			"message" => $message,
		];
		return response()->json($response, $code);
	}
	/**
	 * return error response.
	 *
	 * @return mixed
	 */
	public function sendError($error, $errorMessages = [], $code = 404)
	{
		$response = [
			"success" => false,
		];
		if (!empty($errorMessages)) {
			$response["data"] = $errorMessages;
		}

		return response()->json($response, $code);
	}
}
