<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class CreateKeyWordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		self::down();
		Schema::create('key_words', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('word')->unique();
			$table->timestamps();
		});
		try
		{
			Schema::create('crawled_content_key_word', function(Blueprint $table)
			{
				$table->increments('id');
				$table->integer('crawled_content_id');
				$table->foreign('crawled_content_id')->references('id')->on('crawled_contents');
				$table->integer('key_word_id');
				$table->foreign('key_word_id')->references('id')->on('key_words');
			});
		}
		catch(QueryException $e)
		{
			Schema::dropIfExists('crawled_content_key_word');
			Schema::create('crawled_content_key_word', function(Blueprint $table)
			{
				$table->increments('id');
				$table->integer('crawled_content_id');
				$table->integer('key_word_id');
				echo "Foreign keys not supported.\n";
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
		Schema::dropIfExists('key_words');
		Schema::dropIfExists('crawled_content_key_word');
	}

}
