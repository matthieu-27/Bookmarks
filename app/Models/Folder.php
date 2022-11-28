<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Folder
 *
 * @package App\Models
 * @property int                    $id
 * @property int                    $user_id
 * @property string                 $name
 * @property string|null			$description
 * @property Carbon|null         	$created_at
 * @property Carbon|null           	$updated_at
 * @property Collection|Bookmark[]  $folders
 * @property Collection|Tag[]      	$tags
 * @method static Builder|Folder byUser(int $user_id = null)
 */
class Folder extends Model
{
	use HasFactory;
	protected $hidden = ['pivot'];

	/**
	 * Scope for the user relation
	 *
	 * @param Builder  $query
	 * @param int|null $user_id
	 * @return Builder
	 */
	public function scopeByUser(Builder $query, int $user_id = null): Builder
	{
		if (is_null($user_id) && auth()->check()) {
			$user_id = auth()->id();
		}
		return $query->where('user_id', $user_id);
	}

	/**
	 * Scope where parent_id = NULL ( Return root folder )
	 * 
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeRootFolder(Builder $query): Builder
	{
		return $query->where('parent_id', null);
	}

	/*
     | ========================================================================
     | RELATIONSHIPS
    */
	/**
	 * @return BelongsTo
	 */
	public function parent(): BelongsTo
	{
		return $this->belongsTo(Folder::class, 'parent_id');
	}
	/**
	 * @return HasMany
	 */
	public function children(): HasMany
	{
		return $this->hasMany(Folder::class, 'parent_id');
	}
	/**
	 * @return BelongsToMany
	 */
	public function bookmarks(): BelongsToMany
	{
		return $this->belongsToMany(Bookmark::class, 'folder_bookmarks', 'folder_id', 'bookmark_id');
	}
	/**
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	/**
	 * @return BelongsToMany
	 */
	public function tags(): BelongsToMany
	{
		return $this->belongsToMany(Tag::class, 'folder_tags', 'folder_id', 'tag_id');
	}
}
