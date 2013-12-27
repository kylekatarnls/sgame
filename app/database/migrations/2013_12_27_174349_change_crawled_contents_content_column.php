<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;

class ChangeCrawledContentsContentColumn extends Migration {

	const TABLE_NAME = 'crawled_contents';
	const COLUMN_NAME = 'content';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasColumn(self::TABLE_NAME, self::COLUMN_NAME))
		{
			try
			{
				$connection = Schema::getConnection();
				$connection->getSchemaGrammar();
				$print = new Blueprint(self::TABLE_NAME, function(Blueprint $table)
				{
					$table->longtext(self::COLUMN_NAME);
				});
				$statement = str_ireplace(
					array(' add ', ' not null'),
					array(' modify ', ''),
					array_get($print->toSql($connection, $connection->getSchemaGrammar()), 0)
				);
				DB::statement($statement);
			}
			catch(QueryException $e)
			{
				$statement = preg_replace('#modify\s+column\s*([\'"`].+[\'"`])#isU', 'alter column $1 type', $statement);
				DB::statement($statement);
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
		// nothing to do
	}

}