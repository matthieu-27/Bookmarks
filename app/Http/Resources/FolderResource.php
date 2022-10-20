<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
			"name" => $this->name,
			// "user_id" => $this->user_id,
			"links" => [
				"show" => route("folders.show", $this->id),
				"store" => route("folders.store"),
				"udpate" => route("folders.update", $this->id),
				"destroy" => route("folders.destroy", $this->id),
			],
		];
	}
}
