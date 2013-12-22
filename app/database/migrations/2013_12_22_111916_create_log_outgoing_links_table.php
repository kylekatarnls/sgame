<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogOutgoingLinksTable extends Migration {

	const TALE_NAME = 'log_outgoing_links';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable(self::TALE_NAME))
		{
			Schema::create(self::TALE_NAME, function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('search_query');
				$table->integer('crawled_content_id');
				$table->foreign('crawled_content_id')
					->references('id')->on('crawled_contents')
					->onDelete('cascade');
				$table->timestamps();
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
		Schema::dropIfExists(self::TALE_NAME);
	}

}
