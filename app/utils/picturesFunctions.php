<?php

function limite($valeur)
{
	return (int) max(0,min(255,round($valeur)));
}

function teinte($rgba,$teinte)
{
	$min=min($rgba['red'],$rgba['green'],$rgba['blue']);
	$max=max($rgba['red'],$rgba['green'],$rgba['blue']);
	switch($max)
	{
		case $min:
			return $rgba;
		case $rgba['red']:
			$t=(60*($rgba['green']-$rgba['blue'])/($max-$min)+360)%360;
			break;
		case $rgba['green']:
			$t=60*($rgba['blue']-$rgba['red'])/($max-$min)+120;
			break;
		default:
			$t=60*($rgba['red']-$rgba['green'])/($max-$min)+240;
	}
	$t+=360+$teinte%360;
	$t%=360;
	$indice=$t/60;
	$switch=floor($indice);
	switch($switch)
	{
		case 0:
			$rgba['red']=$max;
			$rgba['green']=$min+($max-$min)*($indice-$switch);
			$rgba['blue']=$min;
			break;
		case 1:
			$rgba['red']=$min+($max-$min)*(1+$switch-$indice);
			$rgba['green']=$max;
			$rgba['blue']=$min;
			break;
		case 2:
			$rgba['red']=$min;
			$rgba['green']=$max;
			$rgba['blue']=$min+($max-$min)*($indice-$switch);
			break;
		case 3:
			$rgba['red']=$min;
			$rgba['green']=$min+($max-$min)*(1+$switch-$indice);
			$rgba['blue']=$max;
			break;
		case 4:
			$rgba['red']=$min+($max-$min)*($indice-$switch);
			$rgba['green']=$min;
			$rgba['blue']=$max;
			break;
		case 5:
			$rgba['red']=$max;
			$rgba['green']=$min;
			$rgba['blue']=$min+($max-$min)*(1+$switch-$indice);
	}
	return $rgba;
}

function luminosite_rapide($rgba,$luminosite)
{
	$rgba['red']+=$luminosite;
	$rgba['blue']+=$luminosite;
	$rgba['green']+=$luminosite;
	return $rgba;
}

function luminosite($rgba,$luminosite)
{
	$min=min($rgba['red'],$rgba['green'],$rgba['blue']);
	$max=max($rgba['red'],$rgba['green'],$rgba['blue']);
	$l=($min+$max)/2;
	$nl=max(0,min(255,$l+$luminosite));
	if($max===$min)
	{
		$rgba['red']=$nl;
		$rgba['blue']=$nl;
		$rgba['green']=$nl;
		return $rgba;
	}
	if($nl>$l)
	{
		$nmax=max(0,min(255,$nl-$l+$max));
		$nmin=max(0,min(255,$nl-($nmax-$nl)));
	}
	else
	{
		$nmin=max(0,min(255,$nl-$l+$min));
		$nmax=max(0,min(255,$nl+($nl-$nmin)));
	}
	$rgba['red']=$nmin+($nmax-$nmin)*($rgba['red']-$min)/($max-$min);
	$rgba['blue']=$nmin+($nmax-$nmin)*($rgba['blue']-$min)/($max-$min);
	$rgba['green']=$nmin+($nmax-$nmin)*($rgba['green']-$min)/($max-$min);
	return $rgba;
}

function saturation($rgba,$saturation)
{
	$min=min($rgba['red'],$rgba['green'],$rgba['blue']);
	$max=max($rgba['red'],$rgba['green'],$rgba['blue']);
	$rgba['red']=$rgba['red']*$saturation/100+($min+$max)*(0.5-$saturation/200);
	$rgba['blue']=$rgba['blue']*$saturation/100+($min+$max)*(0.5-$saturation/200);
	$rgba['green']=$rgba['green']*$saturation/100+($min+$max)*(0.5-$saturation/200);
	return $rgba;
}

function contraste($rgba,$contraste,$moyenne)
{
	$min=min($rgba['red'],$rgba['green'],$rgba['blue']);
	$max=max($rgba['red'],$rgba['green'],$rgba['blue']);
	$l=($min+$max)/2;
	return luminosite($rgba,$contraste*($l-$moyenne)/255);
}

/*
$couleurs_composition=array(
	 'red'=>array('red')
	,'rouge'=>array('red')
	,'r'=>array('red')
	,0=>array('red')
	,'yellow'=>array('red,green')
	,'y'=>array('red,green')
	,'jaune'=>array('red,green')
	,'j'=>array('red,green')
	,1=>array('red,green')
	,'green'=>array('green')
	,'g'=>array('green')
	,'vert'=>array('green')
	,'v'=>array('green')
	,2=>array('green')
	,'cyan'=>array('green,blue')
	,'c'=>array('green,blue')
	,3=>array('green,blue')
	,'blue'=>array('blue')
	,'bleu'=>array('blue')
	,'b'=>array('blue')
	,4=>array('blue')
	,'violet'=>array('red,blue')
	,'vi'=>array('red,blue')
	,'purple'=>array('red,blue')
	,'p'=>array('red,blue')
	,5=>array('red,blue')
);

$couleurs_composition_opposees=array(
	 'red'=>array('green','blue')
	,'rouge'=>array('green','blue')
	,'r'=>array('green','blue')
	,0=>array('green','blue')
	,'yellow'=>array('blue')
	,'y'=>array('blue')
	,'jaune'=>array('blue')
	,'j'=>array('blue')
	,1=>array('blue')
	,'green'=>array('red','blue')
	,'g'=>array('red','blue')
	,'vert'=>array('red','blue')
	,'v'=>array('red','blue')
	,2=>array('red','blue')
	,'cyan'=>array('red')
	,'c'=>array('red')
	,3=>array('red')
	,'blue'=>array('red','green')
	,'bleu'=>array('red','green')
	,'b'=>array('red','green')
	,4=>array('red','green')
	,'violet'=>array('green')
	,'vi'=>array('green')
	,'purple'=>array('green')
	,'p'=>array('green')
	,5=>array('green')
);
//*/

function imageretouche($image,$parametres)
{
	if(empty($parametres))
		return $image;

	if(isset($parametres['applique'], $couleurs_composition[$parametres['applique']]))
	{
		$couleurs_composition_opposees=array(
			 'red'=>array('green','blue')
			,'rouge'=>array('green','blue')
			,'r'=>array('green','blue')
			,0=>array('green','blue')
			,'yellow'=>array('blue')
			,'y'=>array('blue')
			,'jaune'=>array('blue')
			,'j'=>array('blue')
			,1=>array('blue')
			,'green'=>array('red','blue')
			,'g'=>array('red','blue')
			,'vert'=>array('red','blue')
			,'v'=>array('red','blue')
			,2=>array('red','blue')
			,'cyan'=>array('red')
			,'c'=>array('red')
			,3=>array('red')
			,'blue'=>array('red','green')
			,'bleu'=>array('red','green')
			,'b'=>array('red','green')
			,4=>array('red','green')
			,'violet'=>array('green')
			,'vi'=>array('green')
			,'purple'=>array('green')
			,'p'=>array('green')
			,5=>array('green')
		);
		$conserve=$couleurs_composition_opposees[$parametres['applique']];
	}
	$x=imagesx($image);
	$y=imagesy($image);
	if(!empty($parametres['contraste']))
	{
		$lum_moy=0;
		$alpha_total=0;
		for($ix=0;$ix<$x;$ix++) for($iy=0;$iy<$y;$iy++)
		{
			$couleur=imagecolorat($image,$ix,$iy);
			$rgba=imagecolorsforindex($image,$couleur);
			if($rgba['alpha']<127)
			{
				$coef=(127-$rgba['alpha'])/127;
				$alpha_total+=$coef;
				$lum_moy+=(min($rgba['red'],$rgba['green'],$rgba['blue'])+max($rgba['red'],$rgba['green'],$rgba['blue']))*$coef/2;
			}
		}
		$lum_moy/=$alpha_total;
	}
	$dest=imagecreatetruecolor($x,$y);
	imagealphablending($dest,false);
	imagesavealpha($dest,true);
	for($ix=0;$ix<$x;$ix++) for($iy=0;$iy<$y;$iy++)
	{
		$couleur=imagecolorat($image,$ix,$iy);
		$rgba=imagecolorsforindex($image,$couleur);
		$rgba1=$rgba;
		if($rgba['alpha']<127)
		{
			$continue=true;
			if(isset($parametres['limite']))
			{
				$rgba2=$rgba;
				unset($rgba2['alpha']);
				$teinte=0;
				$max=max($rgba2);
				$min=min($rgba2);
				if($min!==$max)
				{
					if($rgba2['green']==$max && $rgba2['blue']==$max)
						$teinte=3;
					elseif($rgba2['red']==$max && $rgba2['green']==$max)
						$teinte=1;
					elseif($rgba2['blue']==$max && $rgba2['red']==$max)
						$teinte=5;
					else
					{
						$diff=$max-$min;
						switch($max)
						{
							case $rgba2['red']:
								$vb=($rgba2['green']-$rgba2['blue'])/$diff;
								$teinte=($vb<-0.5? 5:($vb>0.5? 1:0));
								break;
							case $rgba2['green']:
								$br=($rgba2['blue']-$rgba2['red'])/$diff;
								$teinte=($br<-0.5? 1:($br>0.5? 3:2));
								break;
							case $rgba2['blue']:
								$rg=($rgba2['red']-$rgba2['green'])/$diff;
								$teinte=($rg<-0.5? 3:($rg>0.5? 5:4));
								break;
						}
					}
					if(strpos('.'.$parametres['limite'].'.','.'.$teinte.'.')===false)
						$continue=false;
				}
				elseif(strpos('.'.$parametres['limite'].'.','.-1.')===false)
					$continue=false;
			}
			if($continue)
			{
				if(!empty($parametres['saturation']))
					$rgba=saturation($rgba,100+floatval($parametres['saturation']));
				if(!empty($parametres['luminosite']))
					$rgba=luminosite($rgba,floatval($parametres['luminosite']));
				if(!empty($parametres['teinte']) && $rgba['alpha']<127)
					$rgba=teinte($rgba,floatval($parametres['teinte']));
				if(!empty($parametres['contraste']))
					$rgba=contraste($rgba,floatval($parametres['contraste']),$lum_moy);
				if(!empty($parametres['transparence']))
					$rgba['alpha']=(int) round(127-(127-$rgba['alpha'])*(1-$parametres['transparence']/100));
				if(!empty($parametres['rouge']))
					$rgba['red']+=floatval($parametres['rouge']);
				if(!empty($parametres['vert']))
					$rgba['green']+=floatval($parametres['vert']);
				if(!empty($parametres['bleu']))
					$rgba['blue']+=floatval($parametres['bleu']);
			}
		}

		if(!empty($conserve))
			foreach($conserve as $c)
				$rgba[$c]=$rgba1[$c];

		$rgba=array_map('limite',$rgba);
		imagesetpixel($dest,$ix,$iy,
			imagecolorallocatealpha($dest,$rgba['red'],$rgba['green'],$rgba['blue'],$rgba['alpha']));
	}
	if(!empty($parametres['gaussien']))
		for($i=0;$i<min(20,$parametres['gaussien']);$i++)
		imagefilter($dest,IMG_FILTER_GAUSSIAN_BLUR);
	if(!empty($parametres['symetrie']))
		if($parametres['symetrie']>0)
			$dest=symetrie($dest,$parametres['symetrie']);
	return $dest;
}

function redimensionne($source,$dx,$dy,$sx=0,$sy=0)
{
	if($sx<1) $sx=imagesx($source);
	if($sy<1) $sy=imagesy($source);
	$dest=imagecreatetruecolor($dx,$dy);
	imagealphablending($dest,false);
	imagesavealpha($dest,true);
	imagecopyresampled($dest,$source,0,0,0,0,$dx,$dy,$sx,$sy);
	return $dest;
}

function symetrie($img,$type='')
{
	$width=imagesx($img);
	$height=imagesy($img);
	$dest=imagecreatetruecolor($width,$height);
	imagealphablending($dest,false);
	imagesavealpha($dest,true);
	switch($type)
	{
		case 2:
		case 'v':
			for($i=0;$i<$height;$i++)
				imagecopy($dest,$img,0,($height-$i-1),0,$i,$width,1);
			return $dest;
		case 1:
		case 'h':
			for($i=0;$i<$width;$i++)
				imagecopy($dest,$img,($width-$i-1),0,$i,0,1,$height);
			return $dest;
	}
	return $img;
}

?>