<?php

namespace Hologame;

class Page°Admin°Translation extends Page°Admin
{
	use Trait°Child;
	const CHILD_MATCH = '[a-z]+';
	protected $gText = [], $context = [false, ''], $gLanguage = [], $currentFile = '';
	private function filterContent($match)
	{
		$file = $this->currentFile;
		if(!empty($match[1]))
		{
			$keep = char_at($match[1]) === 'k';
			$context = substr($match[1], $keep ? 2 : 1);
			if(char_at($match[1]) === 'k')
			{
				$this->context = $context;
			}
		}
		else
		{
			$context = '';
		}
		if(start($match[2], "'"))
		{
			$string = str_replace(
				['\\\\', "\\'"],
				['\\', "'"],
				substr($match[2], 1, -1)
			);
		}
		else
		{
			$string = json_decode($match[2]);
		}
		if($string === null)
		{
			debug($match[2]);
		}
		$id = Translation°Text::newId();
		list(, $subFile) = end_separator('/private/', $file, true);
		preg_match('#/([^/.]+)(/index)?(\.[a-z0-9]{2,5})?$#', $subFile, $match);
		$group = $match[1];
		$this->gText[] = [
			'host' => !start($file, '/core/'),
			'file' => $subFile,
			'group' => $group,
			'gText' => [dec2b64($id, 6), $string, $context]
		];
		return 's('.$id.', 1, "'.addcslashes($group ,'"').'", "'.addcslashes($string ,'"').'")';
	}
	private function filterFile($file)
	{
		$this->currentFile = $file;
		$context = [false, ''];
		$realPath = ROOT_DIR.ltrim($file, '/');
		$text = Storage°File::getContent($realPath);
		if(substr($file, -5) === '.html')
		{
			preg_match_all('`^#\(([0-9]+),([0-9]+)\)#`sU', $text, $gMatch);
			foreach($gMatch as $match)
			{
				/*
				if(start($match[1], '/*'))
				{
					$this->context = [char_at($match[1], 2) === 'k', $match[5]];
				}
				else
				{
					$string = json_decode($match[2]);
					list($keep, $context) = $this->context;
					$this->gText[] = [1, $string, $context];
					if(!$keep)
					{
						$this->context = [false, ''];
					}
				}
				*/
			}
		}
		else
		{
			$this->context = '';
			if(in_string('s(', $text))
			{
				//preg_match_all('#(?<![^a-zA-Z0-9\\\'"_:]|->)s\(.*\)#msU', $text, $gMatch, PREG_SET_ORDER);
				$newText = preg_replace_callback('#(?<![a-zA-Z0-9\\\'"_]|::|-\>)s\(\s*(?:\/\*(k?:.*):\*\/\s*)?(".*[^\\\](?:\\\{2})*"|\'.*[^\\\](?:\\\{2})*\')#msU', [$this, 'filterContent'], $text);
				if($newText !== $text)
				{
					if(!Storage°File::isWritable($realPath))
					{
						return false;
					}
					return Storage°File::putContent($realPath, $newText);
				}
			}
		}
		return true;
	}
	public function scan($file)
	{
		$result = true;
		if(!in_string('/private/storage/', $file)
		&& !in_string('/private/twig/', $file)
		&& !in_string('/module/', $file))
		{
			$fFile = prop('fTranslation°Storage');
			$id = $fFile->autoIncrement;
			$result = $this->filterFile($file);
			if(!$result)
			{
				$fFile->autoIncrement = $id;
			}
		}
		return $result;
	}
	protected function write($group, $data, $hostText = true, $lan = H_LANGUAGE)
	{
		return Storage°File::putContent(
			path('STORAGE', $hostText ? 'HOST' : 'CORE').'text/'.$lan.'/'.$group.'.lan',
			"<"."?php\n$T = ".var_export($data, true).";\n?".">"
		);
	}
	protected function read($group, $hostText = true, $lan = H_LANGUAGE)
	{
		$group = load_text_group($group, $hostText ? 'HOST' : 'CORE', $lan);
		return ($group === null ? [] : $group);
	}
	protected function findRoot($ftp, $root = '/')
	{
		list($gFile, $gDir) = $ftp->getAll($root, true);
		debug($gFile);
		if(in_array('index.php', $gFile) && strpos($ftp->getContent($root.'index.php'), '/// INDEX') !== false)
		{
			return $root;
		}
		foreach($gDir as $dir)
		{
			if(($find = $this->findRoot($ftp, $root.$dir)) !== false)
			{
				return $find;
			}
		}
		return false;
	}
	public function main()
	{
		$this->setData('page_title', s('Traduction'));
		if($this->cUser->logged('post'))
		{
			$storage = $this->fMTranslation°Storage;
			$storage->autoIncrement = 0;
			$key = get_post('crypt-key', ''); // _8t6y-Fg4d-6d4rI
			if($key !== '')
			{
				try
				{
					$ftp = new Ftp('shina', Security°Crypt::stDecrypt(
						'nho09pLcFUxitbTiqFmVVsVTh\/dxRp9pj0L2nfa4Ip0=',
						$key
					));
					/*
					list($gFile, $gDir) = $ftp->getAll('/', true);
					
					$this->addData('ftpForm', new Html('pre', [
						'content' => print_r($ftp->rawList('/sd/hologame'), true)
					]));
					*/
					$this->addData('ftpForm', json_encode($this->findRoot($ftp)));
				}
				catch(FtpException $e)
				{
					$this->addData('ftpForm', new Html('div', [
						'class' => 'error',
						'content' => 'Clé de cryptage erronée.'
					]));
				}
			}
			$button = $this->button([
				'label' => s('Déconnexion')
			]);
			$button->addClass('logout');
			$this->gLanguage = explode(',', LANGUAGES);
			$gOption = [];
			foreach($this->gLanguage as $language)
			{
				$gOption[] = [
					'value' => $language,
					'label' => $language
				];
			}
			$language = (in_array($this->gPath[0], $this->gLanguage) ? $this->gPath[0] : 'fr');
			$form = $this->form->select(post('language', [
				'gOption' => $gOption,
				'value' => $language,
				'select' => Html::htmlAttributes([
					'onchange' => $this->cJavascript->raw('location.href = "/admin/translation/" + '.$this->§('#language')->val())
				])
			]));
			$gText = [];
			$this->gText = [];
			$scan = Dir::each([$this, 'scan']);
			if(in_array(false, $scan, true))
			{
				$error = s("Les fichiers suivants n'ont pas pu être modifiés : ");
				foreach($scan as $file => $ok)
				{
					if(!$ok)
					{
						$error .= "<br />\n - $file";
					}
				}
				$this->addData('ftpForm', (
						new Html('p', ['content' => $error])
					).$this->form
						->text(post('crypt-key', [
							'placeholder' => s("Clé de cryptage")
						])
					)
				);
			}
			else
			{
				$cKey = [];
				foreach($this->gText as $data)
				{
					$key = $data['group'].'//'.$data['file'];
					if($key !== $cKey)
					{
						$gText[$key] = $data;
						$gText[$key]['gText'] = [];
						$cKey = $key;
					}
					$gText[$key]['gText'][] = $data['gText'];
					//array_add($gText[$data['file']], $data);
					//$textGroup = $this->read($data['group'], $data['host']);
				}
				$id = $storage->autoIncrement+1;
				$this->addCoreScript('translation');
				$this->setData('req', dec2b64($id, 6));
				$this->setData('textList', $gText);
			}
			$this->setData('language_selection', $form);
			$this->setData('buttons', $button);
		}
		else
		{
			$this->showPage('Admin°Connexion');
		}
	}
}

?>