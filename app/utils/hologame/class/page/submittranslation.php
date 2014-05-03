<?php

namespace Hologame;

class Page°SubmitTranslation
{
	use Trait°Json;
	public function main()
	{
		$this->js->alert(get_post('text')."\n".get_post('group')."\n".get_post('id')."\n".get_post('version'));
	}
}

?>