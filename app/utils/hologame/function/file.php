<?php

function get_host_dir()
{
	return array_value($GLOBALS, 'HOST_DIR', HOST_DIR);
}
function get_existing_files($file, $gPrefix, $gSuffix, $extension = '')
{
	foreach($gPrefix as $prefix)
	{
		foreach($gSuffix as $suffix)
		{
			if(file_exists($prefix.$file.$suffix.$extension))
			{
				return $prefix.$file.$suffix.$extension;
			}
		}
	}
	return false;
}
function host_or_core($file, $extension = '', $widthIndex = false)
{
	$gSuffix = $widthIndex ? ['', '/index'] : [''];
	$gPrefix = [get_host_dir(), CORE_DIR];
	return get_existing_files($file, $gPrefix, $gSuffix, $extension);
}
function core_or_host($file, $extension = '', $widthIndex = false)
{
	$gSuffix = $widthIndex ? ['', '/index'] : [''];
	$gPrefix = [CORE_DIR, get_host_dir()];
	return get_existing_files($file, $gPrefix, $gSuffix, $extension);
}
function get_host($host)
{
	$gHost = hosts($host);
	if(isset($gHost[0]))
	{
		return $gHost[0];
	}
	return DEFAULT_HOST;
}
function get_hosts(array $gHost, $host)
{
	$return = [];
	foreach($gHost as $h => $directory)
	{
		$regex = '#^'.$h.'$#';
		if(preg_match($regex, $host))
		{
			if(strpos($directory, '$') !== false)
			{
				$directory = preg_replace($regex, $directory, $host);
			}
			if(!file_exists(ROOT_DIR.'site/'.$directory))
			{
				$directory = DEFAULT_HOST;
			}
			$return[] = $directory;
		}
	}
	return $return;
}
function hosts($host = null, $directory = null, $mode = HOST_JOKER)
{
	if(is_array($host))
	{
		$return = [];
		foreach($host as $h)
		{
			$return = hosts(array_value($h, 0), array_value($h, 1), array_value($h, 2, HOST_JOKER));
		}
		return $return;
	}
	static $gHost = [];
	if($host === null)
	{
		return $gHost;
	}
	if($directory === null)
	{
		return get_hosts($gHost, $host);
	}
	switch($mode)
	{
		case HOST_JOKER:
			$host = preg_quote($host);
			$host = str_replace('\\*', '.*', $host);
			break;
		case HOST_STRING:
			$host = preg_quote($host);
			break;
	}
	$gHost[$host] = $directory;
	return $gHost;
}
function path($dir = 'PRIVATE', $root = 'ROOT')
{
	return constant($root.'_DIR').constant($dir.'_REL_DIR');
}
function size($octets, $unite = 'o', $precision = 3, $multiple = 1024)
{
        $resultat = floatval($octets);
        $unites = ['', 'K', 'M', 'G', 'T', 'P', 'E', 'Z'];
        for($i=0; $i<9; $i++)
        {
                $log = log10(abs(max(1, $resultat)));
                $pow = pow(10, max(0, $precision-1-floor($log)));
                if($resultat < $multiple && $resultat > -$multiple)
                {
                        $retour = round($resultat*$pow)/$pow;
                        $retour = str_replace('.', ',', $retour);
                        return $retour.' '.$unites[$i].$unite;
                }
                $resultat /= $multiple;
        }
        return $resultat.' Y'.$unite;
}
function dirsize($path)
{
	$result = explode("\t", exec("du -s ".$path), 2); // no-debug
	$size = floatval($result[0])*1024;
	if($size === .0)
	{
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file)
		{
			try
			{
				$size += $file->getSize();
			}
			catch(Exception $e) {}
		}
	}
	return $size;
}
function ressource_href($url, $ext = null, $type = null, $directories = null)
{
	if(is_null($ext))
	{
		list($url, $ext) = end_separator('.', $url);
	}
	if(is_null($type))
	{
		$type = in_array($ext, ['css', 'js']) ? $ext : 'image';
	}
	if(is_null($directories))
	{
		$directories = [
			HOST_DIR => 's%/'.H_HOST,
			CORE_DIR => 'c%'
		];
	}
	foreach($directories as $directory => $path)
	{
		// Si le fichier (par exemple jquery) n'existe pas
		if(file_exists($directory.'public/'.$type.'/'.$url.'.'.$ext))
		{
			$time_cache = abs(filemtime($directory.'public/'.$type.'/'.$url.'.'.$ext)-TIME_CACHE_RESSOURCES);
			return '/'.str_replace('%', $time_cache, $path).'/'.$url.'.'.$ext;
		}
		// On essaye de trouver des versions (jquery-3.2.1, jquery.a.b.c, jquery/index, etc.)
		$result = shell_exec('ls '.$directory.'public/'.$type.'/'.$url.'*.'.$ext); // no-debug
		$result = preg_split('#\s#', $result);
		// On prend le premier fichier (qui commence par jquery)
		$result = end_separator('/', $result[0]);
		$result = $result[1];
	
		$pos_extension = -1-strlen($type);
		if(substr($result, $pos_extension) === '.'.$type)
		{
			$url = substr($result, 0, $pos_extension);
		}
		$time_cache = (filemtime($directory.'public/'.$type.'/'.$url.'.'.$ext)-TIME_CACHE_RESSOURCES);
		return '/'.str_replace('%', $time_cache, $path).'/'.$url.'.'.$ext;
	}
	return false;
}

?>