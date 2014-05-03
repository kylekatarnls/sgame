<?php

namespace Hologame;

make_flags('NO', ['JQUERY', 'JQUERYMOBILE', 'STYLE']);

trait Core§Trait°Page
{
	use Trait°Output;
	use Trait°Option;
	protected $type = TYPE_PAGE;

	public function start()
	{
		$this->lang('fr');
		if(!$this->option(NOJQUERY))
		{
			$this->addScript('://code.jquery.com/jquery-2.0.3.min.js', 'head');
			if(!$this->option(NOJQUERYMOBILE))
			{
				$this->addStyle('://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css', 'screen');
				$this->addScript('://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js', 'head');
			}
			$this->addCoreScript('core', 'body');
			//$this->cTranslation°Text->load();
		}
		if(!$this->option(NOJQUERY))
		{
			$this->addCoreStyle('core', 'screen');
		}
		$this->mobile();
		$this->addMeta([
			'name' => 'siteid',
			'content' => H_HOST
		]);
		if(start(get_server('REMOTE_ADDR'), '192.168.'))
		{
			$this->addMeta([
				'name' => 'ip',
				'content' => get_server('SERVER_ADDR')
			]);
		}

		/*
		$this->setData('main_title', 'Hologame');
		$this->setData('page_title', 'Accueil');
		//$this->setData('page', $this->getTemplate());
		$navbar = $this->hNavbar
			->label('Nav')
			->href('/nav');
		$button = $this->hButton
			->label('Boutbout')
			->href('/bout');
		$button->onclick = $this->§this->css('border', raw("toggle('border-button') ? '2px solid red' : ''"));
		$collapsible = $this->hCollapsible
			->label('Truc')
			->href('#bla')
			->content($button);
		$this->setData('page', $navbar.$collapsible);
		*/
		if(method_exists($this, 'head'))
		{
			$this->head();
		}
	}
	public function end()
	{
		try
		{
			$template = $this->getTemplate();
		}
		catch(TemplateException $e)
		{
			if($e->getCode() === 1)
			{
				$template = '';
			}
			else
			{
				throw $e;
			}
		}
		$this->setDataIfNot('page', $template);
		$this->setDataIfNot('title', £('{{ page_title|raw }} - {{ main_title|raw }}'));
		$this->callData('title', 'strip_tags');
		$this->setDataIfNot('inline_script', $this->js->html());
		echo $this->cTemplate->render();
	}
	public function show()
	{
		if(headers_sent() === false)
		{
			$this->headers();
		}
		$this->start();
		if(method_exists($this, 'main'))
		{
			$this->main();
		}
		$this->end();
	}
	public function __toString()
	{
		$page = $this->getData('page');
		if(method_exists($this, 'main'))
		{
			$this->main();
		}
		$return = strval($this->getData('page'));
		$this->page($page);
		return $return;
	}
}

?>