<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

CreateLogOutgoingLinksTable:Migration

	TALE_NAME = 'log_outgoing_links';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	+ up()
		if(!Schema::hasTable(:TALE_NAME))
			try
				Schema::create(:TALE_NAME, f°(Blueprint $table)
					$table->increments('id');
					$table->string('search_query');
					$table->integer('crawled_content_id');
					$table->foreign('crawled_content_id')
						->references('id')->on('crawled_contents')
						->onDelete('cascade');
					$table->timestamps();
				);
			catch(QueryException $e)
				Schema::dropIfExists(:TALE_NAME);
				Schema::create(:TALE_NAME, f°(Blueprint $table)
					$table->increments('id');
					$table->string('search_query');
					$table->integer('crawled_content_id');
					$table->timestamps();
				);
				echo "Foreign keys not supported.\n";

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	+ down()
		Schema::dropIfExists(:TALE_NAME);