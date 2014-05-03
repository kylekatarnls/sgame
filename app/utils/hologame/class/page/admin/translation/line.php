<?php

namespace Hologame;

class Page°Admin°Translation°Line
{
	use Trait°Json;
	public function main()
	{
		$lan = get_post('language');
		$gText = get_post('text', [], 'array');
		$gFile = get_post('file', [], 'array');
		$gHost = get_post('host', [], 'array');
		$gId = get_post('id', [], 'array');
		$this->setData('language', $lan);
		$this->setData('text', $gText);
		$this->setData('file', $gFile);
		foreach($gText as $key => $text)
		{
			//$file = $gFile[$key];
			//$prop = prop('fM'.($gHost[$key] ? '' : 'C').'Translate°'.$lan);
			//$db = $prop->{$file};
			//$db[$text] = $text;
			//$prop->{$file} = $db;
			//Translation°Text::save($gFile[$key], $text, $lan, $gHost[$key] ? 'HOST' : 'CORE', $gId[$key]);
		}
	}
}

?>