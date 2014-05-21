<?php

function include_dir($directory)
{
	$exclude = explode(' ', HOLOGAME_EXCLUDE_FILES);
	$directory = rtrim($directory, '/');
	if(file_exists($directory))
	{
		foreach(scandir($directory) as $file)
		{
			if(substr($file, -4) === '.php' && ! in_array(substr($file, 0, -4), $exclude))
			{
				include_once($directory.'/'.$file);
			}
		}
	}
}

include_dir(__DIR__.'/function');

?>