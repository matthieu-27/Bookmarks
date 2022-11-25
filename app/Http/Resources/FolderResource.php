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
		if (count($this->folders) >= 0) {
			return [
				"id" => $this->id,
				"name" => $this->name,
				"folders" => $this->whenLoaded('folders'),
			];
		}
	}
}
