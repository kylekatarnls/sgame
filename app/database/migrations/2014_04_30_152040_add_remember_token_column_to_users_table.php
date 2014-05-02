<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class AddRememberTokenColumnToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasColumn('users', 'remember_token'))
		{
			try
			{
				Schema::table('users', function(Blueprint $table)
				{
					$table->string('remember_token')->nullable();
				});
			}
			catch (QueryException $e)
			{
				echo "Can not add remember_token column, it probably already exists.\n";
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
	}

}