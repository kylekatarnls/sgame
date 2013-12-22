<?php

use Illuminate\Database\Migrations\Migration;

class CreateCrawledContent extends Migration {

    const TALE_NAME = 'crawled_contents';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(self::TALE_NAME, function($table)
        {
            $table->increments('id');
            $table->string('url');
            $table->string('title');
            $table->string('content');
            $table->timestamps();
        });
        DB::statement('ALTER TABLE `'.self::TALE_NAME.'` ADD FULLTEXT search(url, title, content)');
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