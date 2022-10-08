<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("bookmarks", function (Blueprint $table) {
			$table->id();
			$table->integer('user_id')->unsigned();
			$table->string("title");
			$table->string("url");
			$table->string("description")->nullable();
			$table->text('thumbnail', 255)->nullable()->default(null);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists("bookmarks");
	}
};
