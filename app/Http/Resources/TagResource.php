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
            "name" => $this->name,
            "user_id" => $this->user_id,
            "links" => [
                "show" => route("tags.show", $this->id),
                "store" => route("tags.store"),
                "udpate" => route("tags.update", $this->id),
                "destroy" => route("tags.destroy", $this->id),
            ],
        ];
    }
}
