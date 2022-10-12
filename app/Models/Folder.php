<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Folder extends Model
{
	use HasFactory;

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
}
