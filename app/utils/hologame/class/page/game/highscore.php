<?php

namespace Hologame;

class Page°Game°Highscore
{
	use Trait°Json;
	public function main()
	{
		$this->setData('list',
			$this->sHighscore
				->insert([
					'name' => get_post('pseudo'),
					'score' => get_post('score', 0, 'int'),
					'date' => raw('NOW()')
				], true)
				->order('`score` DESC, `date` ASC')
				->limit(14)
				->fetchAll()
		);
	}
}

?>