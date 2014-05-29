<?php

namespace Hologame;

class Core extends Object
{
	protected function toCanonicalURL($queryString = '')
	{
		$redirectTo = get_constant('REDIRECT_TO');
		if(!empty($redirectTo))
		{
			$protocole = true;
			$tab = explode('://', $redirectTo);
			$withProtocole = (count($tab) === 2);
			if($withProtocole)
			{
				if(finish($tab[0], 's') !== (get_server('HTTPS') === ''))
				{
					$protocole = false;
				}
				$tab[0] = $tab[1];
			}
			if(!$protocole || get_server('HTTP_HOST') !== trim($tab[0], '/'))
			{
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: '.($withProtocole ? '' : 'http://').trim($redirectTo, '/').'/'.$queryString);
				exit; // no-debug
			}
		}
	}
	public function __construct()
	{
		define('QUERY_STRING', ltrim(get_get('hQueryString'), './'));
		$this->toCanonicalURL(QUERY_STRING);
		unset($_GET['hQueryString']);
		switch(QUERY_STRING)
		{
			// URI spéciales
			case 'favicon.ico':
				$fav = $this->cUtil°Favicon;
				if($fav->file !== false)
				{
					header('Content-type: image/'.$fav->format);
					header('Cache-Control: max-age=2592000, public');
					readfile($fav->file);
					break;
				}
			default:
				$q = array_map('ucfirst', explode('/', QUERY_STRING));
				if($q[0] === DEFAULT_PAGE || $q[0] === 'index')
				{
					$q[0] = '';
				}
				else if($q[0] === '')
				{
					$q[0] = DEFAULT_PAGE;
				}
				$page = rtrim(implode('°', $q), '°');
				if(empty($page))
				{
					$page = 'Default';
				}
				if($this->cPage->exists($page))
				{
					$page = 'p'.$page;
					return $this->$page->show();
				}
				else
				{
					return $this->error404();
				}

				header('Content-type: text/html; charset=utf-8');
				if(isset($_GET['modules']))
				{
					// Pour tester le listage des modules
					$this->cLinkModule->moduleList();
				}
				else
				{
					// Pour tester le module jQuery
					$this->§('#truc')
						->machin('abc')
						->truc();
					echo $this->js->js; // no-debug
				}
		}
	}
}

class CoreException extends ObjectException {}

?>