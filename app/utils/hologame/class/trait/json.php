<?php

namespace Hologame;

trait Trait°Json
{
	use Trait°Output;
	protected $type = TYPE_AJAX;
	public function show()
	{
		if(headers_sent() === false)
		{
			$this->headers();
		}
		$this->main();
		$js = $this->js->js->out();
		if(!empty($js))
		{
			$this->setData('js', $js);
		}
		echo json_encode($this->data);
	}
}

?>