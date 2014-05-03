<?php

namespace Hologame;

class Payment°Allopass extends Payment
{
	protected $site, $produit, $auth;
	public function __construct($site = null, $produit = null, $auth = null)
	{
		$this->cCall($site, $produit, $auth);
	}
	public function cCall($site = null, $produit = null, $auth = null)
	{
		if(is_null($produit) && is_string($site))
		{
			list($site, $produit, $auth) = explode('/', $site, 3);
		}
		$this->site = intval($site);
		$this->produit = intval($produit);
		$this->auth = intval($auth);
		return $this;
	}
	public function check($code)
	{
		$r=@file("http://payment.allopass.com/api/checkcode.apu?code=".
			urlencode($code)."&auth=".
			urlencode($this->site.'/'.$this->produit.'/'.$this->auth));
		return (substr($r[0],0,2)==="OK");
	}
	public function iframe($width = 550, $height = 480)
	{
		return new Html('iframe',[
			'width' => $width,
			'height' => $height,
			'frameborder' => 0,
			'marginheight' => 0,
			'marginwidth' => 0,
			'scrolling' => "no",
			'src' => $this->href(),
		]);
	}
	public function href()
	{
		return 'https://payment.allopass.com/buy/buy.apu?recall=1&ids='.$this->site.'&idd='.$this->produit;
	}
	public function button($img = null, $lang = 'fr')
	{
		if(is_string($img))
		{
			$img = new Html('img', [
				'src' => $img,
				'alt' => "Acheter"
			]);
		}
		if(is_object($img) && is_a($img, 'Html'))
		{
			return new Html('a', [
				'href' => $this->href(),
				'content' => $img
			]);
		}
		$script = new Html('script', [
				'type' => "text/javascript",
				'src' => "https://payment.allopass.com/buy/checkout.apu?ids=".$this->site."&idd=".$this->produit."&lang=".$lang
			]);
		$noscript = new Html('noscript', [
			'content' => new Html('a', [
				'href' => $this->href(),
				'content' => $img
			])
		]);
		return $script.$noscript;
	}
	public function a($img = null, $lang = 'fr')
	{
		return $this->button($img, $lang);
	}
}

?>