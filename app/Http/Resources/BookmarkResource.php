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
			"id" => $this->id,
			"title" => $this->title,
			"url" => $this->url,
			"description" => $this->description,
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
