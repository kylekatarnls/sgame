<?php
sbp_include_once(__DIR__ . '/TestCase.sbp.php');

foreach(scandir($dir = __DIR__ . '/../') as $file)
{
	if(is_file($file = $dir . $file))
	{
		sbp_include_once($file);
	}
}

?>