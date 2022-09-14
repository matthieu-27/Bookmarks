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
			"id" => $this->id,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"name" => $this->name,
			"root_id" => $this->root_id,
			"user_id" => $this->user_id,
		];
	}
}
