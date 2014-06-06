<?

DevController:BaseController

	IMAGE_SYSTEM_EXTENSIONS = 'png jpg jpeg gif ico txt'

	+ specs
		<>view('specs')

	- init
		set_time_limit(0)

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
		>init()
		$data = array()
		$imgDiretory = >imgDiretory()
		if Input::has('commit-message')
			$output = ""
			$gitAdd = Input::get('git-add')
			if ! empty($gitAdd)
				array_keys(**$gitAdd)
				$output = 
					Git::add($gitAdd) .
					Git::commit(Input::get('commit-message')) .
					Git::push(Input::get('git-username'), Input::get('git-password'))
			$data = (object) {
				input = implode("\n", Git::getCommands())
				output = $output
			}
			unset($output)
		if Input::hasFile('image')
			foreach Input::file('image') as $name => $file
				if ! is_null($file)
					$originalName = $file->getClientOriginalName()
					list($baseName, $extension) = end_separator('.', $originalName)
					strtolower(**$extension)
					if $extension is 'jpeg'
						$extension = 'jpg'
					$path = $imgDiretory . '/' . $name . '.' . $extension
					if file_exists($path)
						$saveFile = $path . '.save'
						rename($path, $saveFile)
					$file->move($imgDiretory, $name . '.' . $extension)
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
					$continue = true
					if file_exists($txtFile)
						$info = parse_ini_file($txtFile)
						$width = array_get($info, 'width', 0)
						$height = array_get($info, 'height', $width)
						$continue = false
						if isset($saveFile)
							if ! $width
								$imageInfo = getimagesize($saveFile)
								$width = array_get($imageInfo, 0, 0)
								$height = array_get($imageInfo, 1, $width)
								unset($imageInfo)
							$retinaFile = $imgDiretory . '/' . $name . '@2x.' . $extension
							if $imageSize[0] is $width * 2 && $imageSize[1] is $height * 2
								rename($path, $retinaFile)
								rename($saveFile, $path)
								unset($saveFile)
							elseif $imageSize[0] * 2 is $width && $imageSize[1] * 2 is $height
								rename($saveFile, $retinaFile)
								unset($saveFile)
							else
								if file_exists($retinaFile)
									unlink($retinaFile)
								$continue = true
						else
							$continue = true

					if $continue
						if $imageSize[0] is $width * 2 && $imageSize[1] is $height * 2
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
							$thumb = imagecreatetruecolor($width, $height)
							imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $imageSize[0], $imageSize[1])
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
					if isset($saveFile)
						unlink($saveFile)

					touch($path)

		<>survey($data)

	+ survey $data = array()
		>init()
		$tab = Input::get('tab')
		$tab :=
			"update" ::
				$output = inRoot(f°
					< trim(shell_exec('php artisan update')) // no-debug
				)
				$vars = {
					output = $output
				}
				:;
			"git" ::
				$vars = {
					git = new Git
				}
				:;
			"img" ::
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
					elseif $isInfoFile
						$list[$directory][$name]['info'] = parse_ini_file($imgDiretory . $path)
						if ! isset($list[$directory][$name]['image'])
							list($list[$directory][$name]['image'], $removeTxt) = end_separator('.', ltrim($path, '/'))
							$list[$directory][$name]['missing-image'] = true
					else
						$list[$directory][$name]['image'] = ltrim($path, '/')
						$list[$directory][$name]['missing-image'] = false

				, '', :IMAGE_SYSTEM_EXTENSIONS, $imgDiretory)
				$vars = {
					git = new Git
					images = $list
				}
				:;
			d:
				$check = >command('check', '-v')
				$checkLog = array()
				foreach array('error', 'warning', 'notice', 'help') as $type
					$checkLog[$type] = substr_count($check, '[' . strtoupper($type) . ']')

				$('#check-details')->slideUp(0)
				$check = {
					summary = >command('check')
					details = $check
					log = (object) $checkLog
				}
				$vars = {
					check = (object) $check
				}

		<>view('survey/index', array_merge($vars, {
			tab = $tab
			data = empty($data) ? null : $data
		}))