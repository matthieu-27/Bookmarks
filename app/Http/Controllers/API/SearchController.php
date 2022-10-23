<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Http\Resources\FolderResource;
use App\Http\Resources\TagResource;
use App\Models\Bookmark;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SearchController extends BaseController
{
    /**
     * Search user's bookmarks with title / description
     *
     * @param Request $request
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

        return $this->sendResponse(new BookmarkResource($search), "Bookmarks found.");
    }

    /**
     * Search user's tags with name
     *
     * @param Request $request
     */
    public function searchTags(Request $request)
    {
        $query = $request->input('query', false);

        $tags = Tag::byUser($request->user()->id)->where('name', 'like', '%' . $query . '%')->with(["folders", "bookmarks"])->first();

        return $this->sendResponse(new TagResource($tags), "Tags found.");
    }
}
