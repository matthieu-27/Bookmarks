<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
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
			// "id" => $this->id,
			// "created_at" => $this->created_at,
			// "updated_at" => $this->updated_at,
			"title" => $this->title,
			"url" => $this->url,
			"description" => $this->description,
			// "user_id" => $this->user_id,
			"thumbnail" => $this->thumbnail,
			"links" => [
				"show" => route("bookmarks.show", $this->id),
				"store" => route("bookmarks.store"),
				"udpate" => route("bookmarks.update", $this->id),
				"destroy" => route("bookmarks.destroy", $this->id),
			],
		];
	}
}
