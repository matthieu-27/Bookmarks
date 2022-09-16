<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table("bookmarks", function (Blueprint $table) {
			$table->foreignId("folder_id")->constrained("folders");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::table("bookmarks", function (Blueprint $table) {
			$table->dropConstrainedForeignId("folder_id");
		});
	}
};
