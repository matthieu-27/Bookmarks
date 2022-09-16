<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
	use HasFactory;

	public function bookmarks()
	{
		return $this->hasMany(Bookmark::class);
	}

	public function foldersin()
	{
		return $this->hasMany(Folder::class, "root_id");
	}

	public function parent()
	{
		return $this->belongsTo(Folder::class, "root_id");
	}

	public function owner()
	{
		return $this->belongsTo(User::class, "user_id");
	}
}
