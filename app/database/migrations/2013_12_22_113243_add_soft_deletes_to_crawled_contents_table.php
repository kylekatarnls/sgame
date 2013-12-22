<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToCrawledContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasColumn('crawled_contents', 'deleted_at'))
		{
			Schema::table('crawled_contents', function(Blueprint $table)
			{
				$table->softDeletes();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if(Schema::hasColumn('crawled_contents', 'deleted_at'))
		{
			Schema::table('crawled_contents', function(Blueprint $table)
			{
				$table->dropColumn('deleted_at');
			});
		}
	}

}