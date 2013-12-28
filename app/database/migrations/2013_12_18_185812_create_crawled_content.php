<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class CreateCrawledContent extends Migration {

	const TALE_NAME = 'crawled_contents';

	protected function createTable($tableName)
	{
		if(!Schema::hasTable($tableName))
		{
			Schema::create($tableName, function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('url')->unique();
				$table->string('title');
				$table->longtext('content');
				$table->timestamps();
				$table->softDeletes();
			});
			try
			{
				DB::statement('ALTER TABLE `'.$tableName.'` ADD FULLTEXT search(url, title, content)');
			}
			catch(QueryException $e)
			{
				echo "FULLTEXT is not supported.\n";
			}
		}
	}
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->createTable(self::TALE_NAME);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists(self::TALE_NAME);
	}

}

?>