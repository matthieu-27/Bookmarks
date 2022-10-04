<?php

use App\Models\User;

/**
 * Shorter auth()->user()
 * But used mainly due to interface Authenticatable and not User on original method,
 * that causes undefined method warning on IDE
 *
 * @return \App\User|\Illuminate\Contracts\Auth\Authenticatable|null
 */
function user()
{
    return auth()->user();
}
