<?php

use Illuminate\Database\Migrations\Migration;

class CreateCrawledContentCluster extends CreateCrawledContent {

	const TALE_NAME = 'crawled_contents';
	const CLUSTERS = 10;

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		for($i = 1; $i <= self::CLUSTERS; $i++)
		{
			$this->createTable('mirror_'.self::TALE_NAME.'_cluster_'.$i);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		for($i = 1; $i <= self::CLUSTERS; $i++)
		{
			Schema::dropIfExists('mirror_'.self::TALE_NAME.'_cluster_'.$i);
		}
	}

}