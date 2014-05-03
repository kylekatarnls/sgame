<?php

namespace Hologame;

class Translation°Text extends Object
{
	protected $enabled = false;
	public function __construct()
	{
		$this->enabled = ($this->cUser->logged() || get_constant('SPANTEXT') === true);
	}
	public function load()
	{
		$this->addCoreScript('text', 'body');
		$this->addCoreStyle('text', 'screen');
	}
	static public function newId($format = 'dec')
	{
		$file = prop('fMTranslation°Storage');
		$id = $file->autoIncrement;
		$id = ($id > 0 ? $id+1 : 1);
		$file->autoIncrement = $id;
		return $format === 'dec' ? $id : dec2b64($id, 6);
	}
	static public function text($text, $group, $id, $version)
	{
		static $enabled = null;
		if(is_null($enabled))
		{
			$enabled = (new self)->enabled;
		}
		if($enabled)
		{
			$text = '<span class="text">'.
				data2html([
					'group' => $group,
					'id' => $id,
					'version' => $version
				]).
				$text.
			'</span>';
		}
		return $text;
	}
	static public function export($T)
	{
		$r = '<'."?php\n".'$T = ['."\n";
		foreach($T as $key => $value)
		{
			if(is_array($value) && count($value)===2 && is_int($value[0]) && is_string($value[1]))
			{
				$r .= '"'.$key.'" => ['.intval($value[0]).', "'.addcslashes($value[1], '"').'"]';
			}
		}
		return $r.'];'."\n?".'>';
	}
	static public function save($group, $text, $lan = H_LANGUAGE, $dir = null, $id = null)
	{
		if(is_null($id))
		{
			$id = self::newId('b64');
		}
		$gText = load_text_group($group, $dir, $lan);
		$version = (isset($gText[$id]) ? $gText[$id][0]+1 : 1);
		$gText[$id] = [$version, $text];
		if(!Storage°File::putContent(language_file($group, $dir, $lan), self::export($gText)))
		{
			return false;
		}
		return $id;
	}
}

?>