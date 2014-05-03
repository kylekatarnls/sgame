<?php

namespace Hologame;

class Html°Chessboard extends Html°Util
{
	public function __construct($rows = 8, $cols = null, $data = [])
	{
		if(is_num_array($rows))
		{
			$data = array_value($rows, 2, []);
			$cols = array_value($rows, 1);
			$rows = array_value($rows, 0, 8);
		}
		$data = (array) $data;
		if(!is_object($rows) && !is_array($rows))
		{
			$data['rows'] = intval($rows);
		}
		else
		{
			$data = array_merge($data, (array) $rows);
		}
		if(!is_object($cols) && !is_array($cols))
		{
			if(!is_null($cols))
			{
				$data['cols'] = intval($cols);
			}
		}
		else
		{
			$data = array_merge($data, (array) $cols);
		}
		parent::__construct($data);
	}
}

?>