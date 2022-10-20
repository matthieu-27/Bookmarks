<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Tag
 *
 * @package App\Models
 * @property int                    $id
 * @property int                    $user_id
 * @property string                 $name
 * @property Carbon|null            $created_at
 * @property Carbon|null            $updated_at
 * @property Collection|Folder[]    $folders
 * @property Collection|Bookmark[]  $bookmarks
 * @method static Builder|Tag byUser(int $user_id = null)
 */
class Tag extends Model
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

    /*
     | ========================================================================
     | RELATIONSHIPS
     */

    /**
     * @return BelongsToMany
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Bookmark', 'bookmark_tags', 'tag_id', 'bookmark_id');
    }

    /**
     * @return BelongsToMany
     */
    public function folders(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Folder', 'folder_tags', 'tag_id', 'folder_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
