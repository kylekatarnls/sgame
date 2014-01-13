<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class AddLanguageColumnToCrawledContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasColumn('crawled_contents', 'language'))
		{
			try
			{
				Schema::table('crawled_contents', function(Blueprint $table)
				{
					$table->string('language')->nullable();
				});
			}
			catch (QueryException $e)
			{
				echo "Can not add language column, it probably already exists.\n";
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}