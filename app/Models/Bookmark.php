<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookmark extends Model
{
	use HasFactory;
	protected $hidden = ['pivot'];
	public $timestamps = false;
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
	 * @return BelongsToMany
	 */
	public function folders(): BelongsToMany
	{
		return $this->belongsToMany(Folder::class, 'folder_bookmarks', 'bookmark_id', 'folder_id');
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
		return $this->belongsToMany(Tag::class, 'bookmark_tags', 'bookmark_id', 'tag_id');
	}
}
