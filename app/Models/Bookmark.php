<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
	use HasFactory;

	public function folder()
	{
		return $this->belongsTo(Folder::class);
	}

	public function tags()
	{
		return $this->morphToMany(Tag::class, 'taggable');
	}
}
