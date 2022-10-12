<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends BaseController
{
    /**
     * Search user's bookmarks with title / description
     *
     * @param Request $request
     * @return mixed
     */
    public function searchBookmarks(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            "title" => "nullable|max:255",
            "description" => "nullable|max:255"
        ]);

        if ($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors());
        }
        if (is_null($input["title"]) && is_null($input["description"])) {
            return $this->sendError("", "Please indicate a title or a description", 404);
        }

        $search = Bookmark::byUser();

        if (isset($input["title"])) {
            $search->where("title", "like", '%' . $input["title"] . '%');
        }
        if (isset($input["description"])) {
            $search->where("description", "like", '%' . $input["description"] . '%');
        }

        $search = $search->get();

        return $this->sendResponse(BookmarkResource::collection($search), "Bookmarks Found");
    }
}
