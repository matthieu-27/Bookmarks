<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TagResource;
use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class TagController extends BaseController
{


    public function index()
    {
        $tags = Tag::byUser()->with(['bookmarks', 'folders'])->get();
        return response()->json(TagResource::collection($tags));
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
            "name" => 'required|unique:tags,name,NULL,id,user_id,' . Auth::user()->id,
            "folder_id" => "integer|nullable",
            "bookmark_id" => "integer|nullable"
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 401);
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
            } elseif (!isset($input["folder_id"]) && isset($input["bookmark_id"])) {
                $ressource = Bookmark::find($input["bookmark_id"]);
                if (!Gate::allows('user_bookmark', $ressource)) {
                    return $this->sendError(null, "Unauthorized access to bookmark", 403);
                }
            } else {
                return $this->sendError(null, "You can't enter both a folder_id and a bookmark_id");
            }
            /* if the $ressource is not null: creation of the tag */
            if (isset($ressource)) {
                $tag = $this->makeTag($ressource, $input["name"]);

                return $this->sendResponse(
                    new TagResource($tag),
                    "Tag created successfully."
                );
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
        $input = $request->all();

        if (!Gate::allows('user_tag', $tag)) {
            return $this->sendError(null, "Unauthorized access to tag", 403);
        }

        $validator = Validator::make($input, [
            "name" => "required|unique:tags",
            "folder_id" => "integer|nullable",
            "bookmark_id" => "integer|nullable"
        ]);

        if ($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors());
        }

        if (!isset($input["folder_id"]) && !isset($input["bookmark_id"])) {
            return $this->sendError(null, "Please indicate at least one id.");
        } else {
            if (isset($input["folder_id"]) && !isset($input["bookmark_id"])) {
                $ressource = Folder::find($input["folder_id"]);
                if (!Gate::allows("user_folder", $ressource)) {
                    return $this->sendError(null, "Unauthorized access to folder", 403);
                }
                $tag->folders()->detach();
            } elseif (!isset($input["folder_id"]) && isset($input["bookmark_id"])) {
                $ressource = Bookmark::find($input["bookmark_id"]);
                if (!Gate::allows('user_bookmark', $ressource)) {
                    return $this->sendError(null, "Unauthorized access to bookmark", 403);
                }
                $tag->bookmarks()->detach();
            } else {
                return $this->sendError(null, "You can't enter both a folder_id and a bookmark_id");
            }
        }
        /* if the $ressource is not null: call syncTag() with the tag id */
        if (isset($ressource)) {
            $tag = $this->syncTag($ressource, $input, $tag);
            return $this->sendResponse(
                new TagResource($tag),
                "Tag updated successfully."
            );
        } else {
            $this->sendError(null, "Problem during Update");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        if (!Gate::allows('user_tag', $tag)) {
            return $this->sendError(null, "Unauthorized access to bookmark", 403);
        }
        $tag->folders()->detach();
        $tag->bookmarks()->detach();
        $tag->delete();
        return $this->sendResponse([], "Tag deleted succesfully");
    }
    /**
     * Create a Tag and assign it to the given Model
     *
     * @param mixed $model
     * @param string $name
     * @return \App\Models\Tag
     */
    private function makeTag($model, $name)
    {
        $tag = new Tag();
        $tag->name = $name;
        $tag->user_id = auth()->user()->id;
        $tag->save();
        $model->tags()->attach($tag);
        return $tag;
    }
    /**
     * Update a Tag and sync it to the given Model
     *
     * @param mixed $model
     * @param string $name
     * @param int $id
     * @return \App\Models\Tag
     */
    private function syncTag($ressource, $input, $tag)
    {

        foreach ($input as $key => $value) {
            if (!is_null($input[$key]) && $key != "id" && $key != "user_id") {
                if (Schema::hasColumn("tags", $key)) {
                    $tag->$key = $value;
                }
            }
        }
        $tag->save();
        $ressource->tags()->attach($tag);
        return $tag;
    }
}
