<?php

use Illuminate\Database\Migrations\Migration;

class CreateCrawledContent extends Migration {

	const TALE_NAME = 'crawled_contents';

	protected function createTable($tableName)
	{
		if(!Schema::hasTable($tableName))
		{
			Schema::create($tableName, function($table)
			{
				$table->increments('id');
				$table->string('url');
				$table->string('title');
				$table->string('content');
				$table->timestamps();
			});
			try
			{
				DB::statement('ALTER TABLE `'.$tableName.'` ADD FULLTEXT search(url, title, content)');
			}
			catch(Illuminate\Database\QueryException $e)
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
		$this>createTable(self::TALE_NAME);
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