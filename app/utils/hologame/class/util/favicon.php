<?php

namespace Hologame;

class Util°Favicon extends Object
{
	public $file = false, $format = false;
	public function __construct()
	{
		static $file = null, $format = null;
		if(is_null($file))
		{
			if($this->lmem->exists('favicon', $get))
			{
				list($file, $format) = $get;
			}
			else foreach(['ico', 'jpeg' => 'jpg', 'png', 'ico' => 'gif'] as $format => $ext)
			{
				if(false !== ($file = host_or_core('public/image/favicon.'.$ext)))
				{
					$format = (is_string($format) ? $format : $ext);
					$this->lmem->set('favicon', [$file, $format], 43200);
					break;
				}
			}
		}
		$this->file = $file;
		$this->format = $format;
	}
}

?>