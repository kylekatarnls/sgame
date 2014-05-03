<?php

namespace Hologame;

class Html°Error extends Html°Util
{
	public function __construct($error = null)
	{
		$this->utilData = [ 'content' => '' ];
		$this->add($error === null ? s("Erreur interne") : $error);
	}
	public function call($gError)
	{
		$this->utilData['content'] = '';
		foreach($gError as $error)
		{
			$this->add($error);
		}
		return $this;
	}
	public function add($error)
	{
		$this->utilData['content'] .= new Html([ 'content' => $error ]);
	}
}

?>