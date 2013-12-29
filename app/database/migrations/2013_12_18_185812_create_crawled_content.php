<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class CreateCrawledContent extends Migration {

	const TABLE_NAME = 'crawled_contents';

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
				DB::statement("ALTER TABLE " . self::TABLE_NAME . " ADD COLUMN searchtext TSVECTOR");
				DB::statement("UPDATE " . self::TABLE_NAME . "
					SET searchtext = to_tsvector('english', url || '' || title || '' || content)");
				DB::statement("CREATE INDEX searchtext_gin ON " . self::TABLE_NAME . " USING GIN(searchtext)");
				DB::statement("CREATE TRIGGER ts_searchtext
					BEFORE INSERT OR UPDATE ON " . self::TABLE_NAME . "
					FOR EACH ROW EXECUTE PROCEDURE
					tsvector_update_trigger('searchtext', 'pg_catalog.english', 'url', 'title', 'content')");
				echo "TSVECTOR used.\n";

			}
			catch(QueryException $e)
			{
				try
				{
					DB::statement('ALTER TABLE `'.$tableName.'` ADD FULLTEXT search(url, title, content)');
					echo "FULLTEXT used.\n";
				}
				catch(QueryException $e)
				{
					echo "FULLTEXT is not supported.\n";
				}
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
		$this->createTable(self::TABLE_NAME);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists(self::TABLE_NAME);
	}

}

?>