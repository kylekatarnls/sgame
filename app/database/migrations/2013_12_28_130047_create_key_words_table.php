<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\QueryException;

class CreateKeyWordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('key_words', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('word');
			$table->timestamps();
		});
		Schema::create('key_word_crawled_content', function(Blueprint $table)
		{
			$table->increments('id');
			$table->foreign('key_word_id')->references('id')->on('key_words');
			$table->foreign('crawled_content_id')->references('id')->on('crawled_contents');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('key_words');
	}

}
