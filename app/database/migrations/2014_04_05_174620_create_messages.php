<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('content');
			$table->integer('user_id');
			// $table->foreign('user')
			// 	->references('id')->on('users')
			// 	->onDelete('cascade');
			$table->integer('canal_id');
			// $table->foreign('canal')
			// 	->references('id')->on('canals')
			// 	->onDelete('cascade');
			$table->smallInteger('type');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
