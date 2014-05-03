<?php

namespace Hologame;

class LinkModule extends Object
{
	public function moduleList()
	{
		$gModule = [];
		$list = array_merge(
			$this->cDir->getList('private', true, ROOT_DIR),
			$this->cDir->getList('public', true, ROOT_DIR)
		);
		foreach($list as $file)
		{
			$file = trim($file, '/');
			$ini = $file;
			list($file, $extension) = end_separator('.', $file, true);
			$directories = array_slice(explode('/', $file, 4), 1);
			if($directories[0] !== 'module' && isset($directories[1]))
			{
				if(empty($directories[2]))
				{
					$directories[2] = 'index';
				}
				list($type, $dir, $file) = $directories;
				$gModule[$dir][] = $type.'/'.$file.$extension;
				$link = ROOT_DIR.'module/'.$dir.'/'.$file.$extension;
				Dir::make($link, true);
				if(is_link($link) === false)
				{
					if(file_exists($link) === false)
					{
						if(symlink(ROOT_DIR.$ini, $link) === false)
						{
							throw new Exception("Impossible de créer le lien ".$link." pointant sur ".ROOT_DIR.$ini, 1);
							return false;
						}
					}
					else
					{
						throw new Exception("Attention, un fichier ".$link." existe déjà et ce n'est pas un lien symbolique.", 1);
						return false;
					}
				}
				else if(readlink($link) !== ROOT_DIR.$ini)
				{
					throw new Exception("Attention, ".$link." pointe vers ".readlink($link)." mais devrait pointer vers ".ROOT_DIR.$ini, 1);
					return false;
				}
			}
		}
		return $gModule;
	}
}

?>