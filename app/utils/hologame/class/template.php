<?php

namespace Hologame;

class Template extends Object
{
	protected $file, $ffile;
	const EXTENSION = '.html';
	public function __construct($file = null)
	{
		if($file === null)
		{
			$file = 'page';
		}
		if(finish($file, self::EXTENSION))
		{
			$file = substr($file, 0, -strlen(self::EXTENSION));
		}
		$this->load($file);
	}
	public function load($file)
	{
		$ffile = host_or_core(TEMPLATE_REL_DIR.$file, self::EXTENSION, true);
		if($ffile === false)
		{
			throw new TemplateException("Fichier $file introuvable.", 1);
			return false;
		}
		else
		{
			$this->file = $file;
			$this->ffile = $ffile;
			return true;
		}
	}
	public function filterText($match, $attr = false)
	{
		list($all, $text) = $match;
		if(strlen(trim(preg_replace('`\{(\{.*\}|#.*#|%.*%)\}`sU', '', $text))) === 0)
		{
			return $all;
		}
		if(preg_match('`^#\(([0-9]+),([0-9]+)\)#`sU', $text, $match))
		{
			list($all, $id, $version) = $match;
			$loadText = load_text('template-'.$this->file, $id, $version);
			$text = ($loadText === null ? substr($text, strlen($all)) : $loadText);
			if($attr === false)
			{
				$text = Translation°Text::text($text, 'template-'.$this->file, $id, $version);
			}
		}
		return $text;
	}
	public function filterAttrText($match)
	{
		return $this->filterText($match, true);
	}
	public function filterObject($match)
	{
		$var = trim($match[1]);
		$raw = false;
		if(finish($var, '|raw'))
		{
			$var = substr($var, 0, -4);
			$raw = true;
		}
		$value = $this->getData($var);
		if(!is_a($value, 'Template'))
		{
			return strval($match[0]);
		}
		if($value->file === $this->file)
		{
			throw new TemplateException("Insérer un template $var à l'intérieur de lui-même est interdit.");
			return '';
		}
		$value = $value->out();
		if(!$raw)
		{
			$value = encode($value);
		}
		return $value;
	}
	public function out()
	{
		try
		{
			$out = file_get_contents($this->ffile);
			$out = $this->filterOutText($out);
			$out = preg_replace_callback('#\{\{([^\}]*)\}\}#isU', [$this, 'filterObject'], $out);
			return $out;
		}
		catch(Exception $e)
		{
			debug($e);
			return 'Erreur Twig';
		}
	}
	public function __toString()
	{
		return $this->out();
	}
	public function filterOutText($out)
	{
		$out = preg_replace_callback('`(?<=\>)([^<]+)(?=\<)`isU', [$this, 'filterText'], $out);
		$out = preg_replace('`(alt|title)\s*=\s*(["\'])`isU', '$1=$2', $out);
		$out = preg_replace_callback('`(?<=alt="|title=")([^"]+)(?=")`isU', [$this, 'filterAttrText'], $out);
		$out = preg_replace_callback('`(?<=alt=\'|title=\')([^\']+)(?=\')`isU', [$this, 'filterAttrText'], $out);
		return $out;
	}
	public function render()
	{
		return £($this, $this->data);
	}
}

class TemplateException extends ObjectException {}

?>