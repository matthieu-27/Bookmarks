<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TagController extends BaseController
{


    public function index()
    {

        $tags = Tag::byUser()->orderBy('created_at')->get();

        return $this->sendResponse(
            TagResource::collection($tags),
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

        $validator = Validator::make($input, [
            "name" => "required",
            "folder_id" => "integer|nullable",
            "bookmark_id" => "integer|nullable"
        ]);
        if ($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors());
        }

        /* Checking if folder_id and bookmark_id are set */
        if (!isset($input["folder_id"]) && !isset($input["bookmark_id"])) {
            return $this->sendError(null, "Please indicate at least one id.");
        } else {
            /* Initializing Bookmark or Folder $ressource to null */
            $ressource = null;
            /* Checking if folder_id or bookmark_id is set and not null / Checking authorization / Assigning $ressource */
            if (isset($input["folder_id"]) && !isset($input["bookmark_id"])) {
                $ressource = Folder::find($input["folder_id"]);
                if (!Gate::allows("user_folder", $ressource)) {
                    return $this->sendError(null, "Unauthorized access to folder", 403);
                }
            }
            if (!isset($input["folder_id"]) && isset($input["bookmark_id"])) {
                $ressource = Bookmark::find($input["bookmark_id"]);
                if (!Gate::allows('user_bookmark', $ressource)) {
                    return $this->sendError(null, "Unauthorized access to bookmark", 403);
                }
            }
            /* if the $ressource is not null: creation of the tag */
            if (isset($ressource)) {
                $tag = $this->makeTag($ressource, $input["name"]);

                return $this->sendResponse(
                    new TagResource($tag),
                    "Tag created successfully."
                );
            } else {
                /* this block is executed if both variables folder_id and bookmark_id are given */
                return $this->sendError(null, "Please indicate only one id.");
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        if (!Gate::allows('user_tag', $tag)) {
            return $this->sendError(null, "Unauthorized access to tag", 403);
        }
        return $this->sendResponse(
            new TagResource($tag),
            "Tag retrieved successfully."
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        //
        $input = $request->all();
        dd($input, $tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
    }

    private function makeTag($model, $name)
    {
        $tag = new Tag();
        $tag->name = $name;
        $model->tags()->save($tag);

        return $tag;
    }
}
