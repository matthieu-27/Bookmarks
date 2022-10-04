<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user_folder', function (User $user, Folder $folder) {
            return $user->id === $folder->user_id;
        });
        Gate::define('user_bookmark', function (User $user, Bookmark $bookmark) {
            return $user->id === $bookmark->folder()->first()->user_id;
        });
        Gate::define('user_tag', function (User $user, Tag $tag) {
            return $user->id === $tag->folders()->owner()->getRootFolder()->user_id;
        });
    }
}
