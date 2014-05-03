<?php

namespace Hologame;

class Socket extends Object
{
	private function register($in, $id)
	{
		$this->mem->arraySet('socketIdList', $in, $id);
		return ['register' => $id];
	}
	private function putVictory($winner, $looser)
	{
		$this->sUser
			->whereId($winner)
			->update([
				'cVictory' => Raw('`cVictory`+1'),
				'cMatch' => Raw('`cMatch`+1')
			]);
		$this->sUser
			->whereId($looser)
			->update([
				'cDefeat' => Raw('`cDefeat`+1'),
				'cMatch' => Raw('`cMatch`+1')
			]);
		return true;
	}
	private function victory($gPlay, $xPlayer, $oPlayer)
	{
		$data = [];
		foreach($gPlay as $i => $play)
		{
			$data[$play] = !!($i%2);
		}
		for($x=0;$x<3;$x++)
		{
			if(isset($data[$x],$data[$x+3],$data[$x+6]) && $data[$x]===$data[$x+3] && $data[$x]===$data[$x+6])
			{
				return $this->putVictory($data[$x] ? $xPlayer : $oPlayer, $data[$x] ? $oPlayer : $xPlayer);
			}
		}
		for($y=0;$y<9;$y+=3)
		{
			if(isset($data[$y],$data[$y+1],$data[$y+2]) && $data[$y]===$data[$y+1] && $data[$y]===$data[$y+2])
			{
				return $this->putVictory($data[$x] ? $xPlayer : $oPlayer, $data[$x] ? $oPlayer : $xPlayer);
			}
		}
		if(isset($data[0],$data[4],$data[8]) && $data[0]===$data[4] && $data[0]===$data[8])
		{
			return $this->putVictory($data[0] ? $xPlayer : $oPlayer, $data[0] ? $oPlayer : $xPlayer);
		}
		if(isset($data[2],$data[4],$data[6]) && $data[2]===$data[4] && $data[2]===$data[6])
		{
			return $this->putVictory($data[2] ? $xPlayer : $oPlayer, $data[2] ? $oPlayer : $xPlayer);
		}
		if(count($gPlay)===9)
		{
			$this->sUser
				->whereId([$xPlayer,$oPlayer])
				->update([
					'cMatch' => Raw('`cMatch`+1')
				]);
			return true;
		}
	}
	private function msg($user, $adv, $data)
	{
		$id = ($adv+$user).'-'.abs($adv-$user);
		$file = 'fMMatch-'.$id;
		// f : file storage, M : mem cache enabled, Match : file name
		// La paire ($adv+$user) et abs($adv-$user) permet d'identifier [$adv, $user] ou [$user, $adv] sans tenir compte de l'ordre

		$outData = [];
		$match = $this->$file;
		$gPlay = $match->get('gPlay', [], 'array');
		if(!isset($match->xPlayer)) // Si les rôles n'ont pas été distribués
		{
			$match->xPlayer = (mt_rand(0, 1) ? $user : $adv);
		}
		$xPlayer = $match->xPlayer;
		$isX = ($xPlayer === $user);
		$isO = !$isX;
		$oPlayer = ($isO ? $user : $adv);
		if(count($gPlay)>4 && $this->victory($gPlay, $xPlayer, $oPlayer))
		{
			$outData['end'] = 1;
			$gPlay = [];
			$match->xPlayer = (mt_rand(0, 1) ? $user : $adv);
			$xPlayer = $match->xPlayer;
			$isX = ($xPlayer === $user);
			$isO = !$isX;
			$oPlayer = ($isO ? $user : $adv);
		}
		$xToPlay = !(count($gPlay) % 2);
		$oToPlay = !$xToPlay;
		$currentPlayer = ($oToPlay ? $oPlayer : $xPlayer);

		$play = array_value($data, 'play', -1, 'int');

		if($play > -1 && $xToPlay === $isX && !in_array($play, $gPlay))
		{
			$gPlay[] = $play;
		}
		if($gPlay !== $match->gPlay)
		{
			$match->gPlay = $gPlay;
		}
		$outData = array_merge($outData, [
			'id' => $id,
			'xPlayer' => $xPlayer,
			'gPlay' =>$gPlay
		]);
		if(isset($data->ping))
		{
			$outData['ping'] = $data->ping;
		}
		return $outData;
	}
	public function controller($in, $out, $data)
	{
		$from = intval($this->cUser->id);
		$to = array_value($data, 'adv', 0, 'int');
		$idOut = array_value($this->mem->socketIdList, $out);

		$ping = array_value($data, 'ping', 0, 'int');

		if($ping > 0)
		{
			return $ping === $idOut ? ['ping' => $from] : null;
		}
		else if(array_value($data, 'action') === 'register')
		{
			return $this->register($in, $from);
		}
		else if($in === $out || $to === $idOut)
		{
			return $this->msg($from, $to, $data);
		}
		return null;
		return [
			'out' => $out,
			'sendBy' => $this->cUser->name,
			'idList' => $this->mem->socketIdList
		];
	}
}

?>