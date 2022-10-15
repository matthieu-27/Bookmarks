<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $model = User::class;

    /**
     * Check if user is admin
     * @return View
     */
    public function checkAdmin()
    {
        $users = User::all();

        if (Auth::user()->is_admin) {
            return view("admin/index")->with("users", $users);
        } else {
            return view("home");
        }
    }

    /**
     * Delete user TODO: and ressources
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        $users = User::all();

        return redirect()->route("admin.index")->with("users", $users);
    }
}
