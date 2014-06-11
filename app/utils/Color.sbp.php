<?

Color

	* $red
	* $green
	* $blue
	* $alpha

	+ __construct $red, $green = null, $blue = null, $alpha = null

		if is_array($red)
			if isset($red['red'])
				extract($red)
			else
				if count($red) === 4
					list($red, $green, $blue, $alpha) = $red
				else
					list($red, $green, $blue) = $red
		>red = floatval($red)
		>green = floatval($green)
		>blue = floatval($blue)
		>alpha = floatval($alpha)


	* dechex $number, $digits = 2

		< str_pad(dechex(>red), $digits, '0', STR_PAD_LEFT)


	+ hex

		< '#' . >dechex(>red) . >dechex(>green) . >dechex(>blue)


	s+ fromImage $image

		$x = imagesx($image)
		$y = imagesy($image)
		$red = 0
		$green = 0
		$blue = 0
		$alpha = 0
		for $ix = 0; $ix < $x; $ix++
			for $iy = 0; $iy < $y; $iy++
				$couleur=imagecolorat($image,$ix,$iy)
				$rgba=imagecolorsforindex($image,$couleur)
				if $rgba['alpha'] < 127
					$ratio = 1 - $rgba['alpha'] / 127
					$red += $rgba['red'] * $ratio
					$green += $rgba['green'] * $ratio
					$blue += $rgba['blue'] * $ratio
					$alpha += $ratio
		$red = round($red / $alpha)
		$green = round($green / $alpha)
		$blue = round($blue / $alpha)
		< new Color($red, $green, $blue)


	s+ fromFile $file

		list(, $extension) = end_separator('.', $file)
		$extension :=
			'gif' ::
				$image = imagecreatefromgif($file)
				:;
			'jpeg' ::
			'jpg' ::
				$image = imagecreatefromjpeg($file)
				:;
			'png' ::
			d:
				$image = imagecreatefrompng($file)
		< static::fromImage($image)


	s+ fromAsset $path

		< static::fromFile(imageAsset($path))


	+ __toString

		<>alpha ?
			"rgba(" . >red . ", " . >green . ", " . >blue . ", " . >alpha . ")" :
			"rgb(" . >red . ", " . >green . ", " . >blue . ")"