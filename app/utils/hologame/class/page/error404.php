<?php

namespace Hologame;

class Page°Error404 extends Page°Default
{
	protected $image = false;
	public function headers()
	{
		$ext = '';
		if(in_string('.', QUERY_STRING))
		{
			$ext = strtolower(array_value(end_separator('.', QUERY_STRING), 1));
		}
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not Found");
		switch($ext)
		{
			case 'jpg':
			case 'jpeg':
				header('Content-type: image/jpeg');
				$this->image = 'jpg';
				break;
			case 'png':
				header('Content-type: image/png');
				$this->image = 'png';
				break;
			case 'gif':
				header('Content-type: image/gif');
				$this->image = 'gif';
				break;
			default:
				parent::headers();
		}
	}
	public function main()
	{
		if($this->image)
		{
			readfile(host_or_core('public/image/default.'.$this->image));
			exit;
		}
		else
		{
			$this->setData('page_title', 'Page introuvable');
			try
			{
				parent::main();
			}
			catch(ObjectException $e)
			{
				if($e->getCode() !== 1)
				{
					throw $e;
				}
			}
		}
	}
}

?>