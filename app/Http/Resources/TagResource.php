<?php

namespace App\Http\Resources;

use App\Models\Bookmark;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Array_;

class TagResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $folders = FolderResource::collection($this->folders);
        $bookmarks = BookmarkResource::collection($this->bookmarks);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "folders" => $folders,
            "bookmarks" => $bookmarks
        ];
    }
}
