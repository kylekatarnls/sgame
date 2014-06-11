<?

use Hologame\Html

DevController:BaseController

	IMAGE_SYSTEM_EXTENSIONS = 'png jpg jpeg gif ico txt'

	+ specs
		<>view('specs')

	- init
		set_time_limit(0)

	- imgAssetDiretory
		< unix_path(realpath(app_path() . '/assets/images'))

	- imgDirectory
		< unix_path(realpath(app_path() . '/../public/img'))

	- command $name, $options = ''
		$name = ucfirst($name) . 'Command'
		< trim(str_replace("\n\n", "\n", call_user_func(array($name, 'getResult'), $options)))

	+ imageToBeReplaced
		$imgDirectory = >imgDirectory()
		$ok = true
		foreach Input::get('to-be-replaced') as $name => $checked
			$file = $imgDirectory . '/' . $name . '.txt'
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

	+ deleteImage
		Session::regenerateToken()
		$image = Input::get('delete')
		list($baseName, $extension) = end_separator('.', $image)
		foreach array(>imgDirectory(), >imgAssetDiretory()) as $directory
			foreach array('jpg', 'jpeg', 'png', 'gif') as $extension
				foreach array('', '@2x') as $resolution
					$file = $directory . '/' . $baseName . $resolution . '.' . $extension
					if file_exists($file)
						unlink($file)
		< Redirect::to('/survey?tab=img')

	+ postSurvey
		>init()
		$data = array()
		$imgDirectory = >imgDirectory()
		$imgAssetDirectory = >imgAssetDiretory()
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
		elseif Input::has('git-username', 'git-password')
			$output = ""
			$gitAdd = Input::get('git-add')
			if ! empty($gitAdd)
				array_keys(**$gitAdd)
				$output = Git::push(Input::get('git-username'), Input::get('git-password'))
			$data = (object) {
				input = implode("\n", Git::getCommands())
				output = $output
			}
			unset($output)
		$files = Input::file('image')
		if is_traversable($files)
			foreach $files as $name => $file
				if ! is_null($file)
					$originalName = $file->getClientOriginalName()
					list($baseName, $extension) = end_separator('.', $originalName)
					strtolower(**$extension)
					if $extension is 'jpeg'
						$extension = 'jpg'
					$path = $imgAssetDirectory . '/' . $name . '.' . $extension
					if file_exists($path)
						$saveFile = $path . '.save'
						rename($path, $saveFile)
					$file->move($imgAssetDirectory, $name . '.' . $extension)
					copy($path, $imgDirectory . '/' . $name . '.' . $extension)
					$imageSize = getimagesize($path)
					$txtFile = $path . '.txt'
					if ! file_exists($txtFile)
						$txtFile = $imgDirectory . '/' . $name . '.txt'
						if ! file_exists($txtFile)
							$txtFile = $imgDirectory . '/' . $name . '.png.txt'
							if ! file_exists($txtFile)
								$txtFile = $imgDirectory . '/' . $name . '.jpg.txt'
								if ! file_exists($txtFile)
									$txtFile = $imgDirectory . '/' . $name . '.gif.txt'
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
							$retinaFile = $imgAssetDirectory . '/' . $name . '@2x.' . $extension
							if $imageSize[0] is $width * 2 && $imageSize[1] is $height * 2
								rename($path, $retinaFile)
								rename($saveFile, $path)
								copy($retinaFile, $imgDirectory . '/' . $name . '@2x.' . $extension)
								unset($saveFile)
							elseif $imageSize[0] * 2 is $width && $imageSize[1] * 2 is $height
								rename($saveFile, $retinaFile)
								copy($retinaFile, $imgDirectory . '/' . $name . '@2x.' . $extension)
								unset($saveFile)
							else
								if file_exists($retinaFile)
									unlink($retinaFile)
								if file_exists($imgDirectory . '/' . $name . '@2x.' . $extension)
									unlink($imgDirectory . '/' . $name . '@2x.' . $extension)
								$continue = true
						else
							$continue = true

					if $continue
						if $imageSize[0] is $width * 2 && $imageSize[1] is $height * 2
							$retinaFile = $imgAssetDirectory . '/' . $name . '@2x.' . $extension
							rename($path, $retinaFile)
							copy($retinaFile, $imgDirectory . '/' . $name . '@2x.' . $extension)
							touch($retinaFile, time(), time())
							DependancesCache::flush($retinaFile)
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

					touch($path, time(), time())
					DependancesCache::flush($path)

		<>survey($data)

	+ survey $data = array()
		>init()
		$git = new Git
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
					git = $git
				}
				:;
			"img" ::
				$imgDirectory = >imgDirectory()
				$extensions = preg_split('#\s+#', :IMAGE_SYSTEM_EXTENSIONS)
				$list = array()
				scanApp(f° $path use $imgDirectory, $extensions, &$list

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
						$list[$directory][$name]['info'] = parse_ini_file($imgDirectory . $path)
						if ! isset($list[$directory][$name]['image'])
							list($list[$directory][$name]['image'], $removeTxt) = end_separator('.', ltrim($path, '/'))
							$list[$directory][$name]['missing-image'] = true
					else
						$list[$directory][$name]['image'] = ltrim($path, '/')
						$list[$directory][$name]['missing-image'] = false

				, '', :IMAGE_SYSTEM_EXTENSIONS, $imgDirectory)
				$vars = {
					git = $git
					images = $list
				}
				:;
			"img-history" ::
				$image =Input::get('img')
				$path = 'public/img/' . $image
				$commits = array_map(f° $value use &$git, &$image

					trim(**$value)
					htmlspecialchars(**$value)
					preg_match('#commit\s+([a-z0-9]+)\s#', $value, $match)
					$key = $match[1] . '/' . $image
					$src = '/eimg/simg/commit/' . $key
					$style = {
						float = 'right'
					}
					$tag = new Html('img', {
						src = $src
						style = $style
					})
					< $tag . $value
				, preg_split('#[\n\r](?=commit)#', $git->log($path)))
				$vars = {
					git = $git
					image = $image
					commits = $commits
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