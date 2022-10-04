<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = ["name", "email", "password"];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = ["password", "remember_token"];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		"email_verified_at" => "datetime",
	];

	public static function create($attributes)
	{
		$model = static::query()->create($attributes);

		$folder = new Folder();
		$folder->name = "root";
		$folder->user_id = $model->id;
		$folder->save();
		return $model;
	}

	public function folders()
	{
		return $this->hasMany(Folder::class, 'user_id');
	}

	public function getRootFolder()
	{
		return $folder = Folder::where("root_id", "=", NULL)->where("user_id", "=", user()->id)->get();
	}
}
