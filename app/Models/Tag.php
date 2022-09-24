<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function bookmarks()
    {
        return $this->morphedByMany(Bookmark::class, 'taggable');
    }

    public function folders()

    {
        return $this->morphedByMany(Folder::class, 'taggable');
    }
}
