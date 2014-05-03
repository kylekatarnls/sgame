<?php

function include_dir($directory)
{
	$directory = rtrim($directory, '/');
	if(file_exists($directory))
	{
		foreach(scandir($directory) as $file)
		{
			if(substr($file, -4) === '.php')
			{
				include_once($directory.'/'.$file);
			}
		}
	}
}

include_dir(__DIR__.'/function');

?>