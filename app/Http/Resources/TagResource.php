<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            "id" => $this->id,
            "tag" => $this->tag,
            // "root_id" => $this->root_id,
            // // "user_id" => $this->user_id,
            // // "childs" => FolderResource::collection($this->foldersin),
            // "links" => [
            // 	"show" => route("folders.show", $this->id),
            // 	"store" => route("folders.store"),
            // 	"udpate" => route("folders.update", $this->id),
            // 	"destroy" => route("folders.destroy", $this->id),
            // ],
        ];
    }
}
