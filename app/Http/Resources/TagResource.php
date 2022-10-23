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
            "name" => $this->name,
            "links" => [
                "show" => route("tags.show", $this->id),
                "store" => route("tags.store"),
                "udpate" => route("tags.update", $this->id),
                "destroy" => route("tags.destroy", $this->id),
            ],
            "folders" => $folders,
            "bookmarks" => $bookmarks,
        ];
    }
}
