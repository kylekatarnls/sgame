<?

DevController:BaseController

	IMAGE_SYSTEM_EXTENSIONS = 'png jpg jpeg gif ico txt'

	+ specs
		<>view('specs')

	- imgDiretory
		< unix_path(realpath(app_path() . '/../public/img'))

	- command $name, $options = ''
		$name = ucfirst($name) . 'Command'
		< trim(str_replace("\n\n", "\n", call_user_func(array($name, 'getResult'), $options)))

	+ imageToBeReplaced
		$imgDiretory = >imgDiretory()
		$ok = true
		foreach Input::get('to-be-replaced') as $name => $checked
			$file = $imgDiretory . '/' . $name . '.txt'
			$data = file_exists($file) ? parse_ini_file($file) : array()
			if $checked
				$data['to-be-replaced'] = '1'
			else
				unset($data['to-be-replaced'])
			$string = ''
			foreach $data as $key => $value
				$string .= $key . '=' . $value . "\n"
			if ! file_put_contents($file, $string)
				$ok = false
		< $ok ? 'OK' : 'KO'

	+ postSurvey
		$imgDiretory = >imgDiretory()
		foreach Input::file('image') as $name => $file
			if ! is_null($file)
				$originalName = $file->getClientOriginalName()
				list($baseName, $extension) = end_separator('.', $originalName)
				strtolower(**$extension)
				if $extension is 'jpeg'
					$extension = 'jpg'
				$file->move($imgDiretory, $name . '.' . $extension)
				$path = $imgDiretory . '/' . $name . '.' . $extension
				$imageSize = getimagesize($path)
				$txtFile = $path . '.txt'
				if ! file_exists($txtFile)
					$txtFile = $imgDiretory . '/' . $name . '.txt'
					if ! file_exists($txtFile)
						$txtFile = $imgDiretory . '/' . $name . '.png.txt'
						if ! file_exists($txtFile)
							$txtFile = $imgDiretory . '/' . $name . '.jpg.txt'
							if ! file_exists($txtFile)
								$txtFile = $imgDiretory . '/' . $name . '.gif.txt'
				if file_exists($txtFile)
					$info = parse_ini_file($txtFile)
					if $imageSize[0] is $info['width'] * 2 && $imageSize[1] is $info['height'] * 2
						$retinaFile = $imgDiretory . '/' . $name . '@2x.' . $extension
						rename($path, $retinaFile)
						$extension :=
							'gif' ::
								$image = imagecreatefromgif($retinaFile)
								:;
							'jpg' ::
								$image = imagecreatefromjpeg($retinaFile)
								:;
							'png' ::
							d:
								$image = imagecreatefrompng($retinaFile)
								:;
						$thumb = imagecreatetruecolor($info['width'], $info['height'])
						imagecopyresampled($thumb, $image, 0, 0, 0, 0, $info['width'], $info['height'], $imageSize[0], $imageSize[1])
						$extension :=
							'gif' ::
								imagegif($thumb, $path)
								:;
							'jpg' ::
								imagejpeg($thumb, $path)
								:;
							'png' ::
							d:
								imagepng($thumb, $path)
								:;
		<>survey()

	+ survey
		$imgDiretory = >imgDiretory()
		$extensions = preg_split('#\s+#', :IMAGE_SYSTEM_EXTENSIONS)
		$list = array()
		scanApp(f° $path use $imgDiretory, $extensions, &$list

			list($directory, $file) = end_separator('/', $path)
			list($name, $extension) = end_separator('.', $file)
			$isInfoFile = $extension is 'txt'
			$isRetina = ends_with($name, '@2x')
			if $isRetina
				substr(**$name, 0, -3)
			while(in_array($extension, $extensions))
				list($name, $extension) = end_separator('.', $name)
			if ! isset($list[$directory])
				$list[$directory] = array()
			if ! isset($list[$directory][$name])
				$list[$directory][$name] = array()
			if $isRetina
				$list[$directory][$name]['retina-image'] = true
			if $isInfoFile
				$list[$directory][$name]['info'] = parse_ini_file($imgDiretory . $path)
				if ! isset($list[$directory][$name]['image'])
					list($list[$directory][$name]['image'], $removeTxt) = end_separator('.', ltrim($path, '/'))
					$list[$directory][$name]['missing-image'] = true
			else
				$list[$directory][$name]['image'] = ltrim($path, '/')
				$list[$directory][$name]['missing-image'] = false

		, '', :IMAGE_SYSTEM_EXTENSIONS, $imgDiretory)

		$check = >command('check', '-v')
		$checkLog = array()
		foreach array('error', 'warning', 'notice', 'help') as $type
			$checkLog[$type] = substr_count($check, '[' . strtoupper($type) . ']')
		if false
			var_dump($checkLog)

		$('#check-details')->slideUp(0)
		<>view('survey/index', array(
			'check' => (object) array(
				'summary' => >command('check'),
				'details' => $check,
				'log' => (object) $checkLog,
			),
			'images' => $list,
		))