<?php

function logb($number, $base = 2)
{
	return log($number) / log($base);
}
function bytes($min, $max = null, $step = 1)
{
	if($max === null)
	{
		$max = $min;
		$min = 1;
	}
	return ceil(logb(ceil(($max - $min + 1) / $step), 256));
}

?>