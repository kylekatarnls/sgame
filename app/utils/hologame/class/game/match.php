<?php

namespace Hologame;

class Game°MatchException extends ObjectException {}

class Game°Match extends Object
{
	protected $user = 0,
		$adv = 0;

	public function uniqueId()
	{
		return ($this->user+$this->adv).'-'.abs($this->user-$this->adv);
	}
	public function match($key = 'Match', $data = null, $count = null)
	{
		if(is_null($count))
		{
			$count = is_null($data) ? 0 : count($data);
		}
		$gStorageSystem = ['session', 'mem'];
		foreach($gStorageSystem as $storageSystem)
		{
			$group = $this->$storageSystem->{'match-'.$key.'-'.$this->uniqueId()};
			if(is_array($group) && $group[0] > $count)
			{
				$data = $group[1];
				$count = $group[0];
			}
		}
		foreach($gStorageSystem as $storageSystem)
		{
			$this->$storageSystem->{'match-'.$key.'-'.$this->uniqueId()} = [$count, $data];
		}
		return $data;
		//.'-'.$this->uniqueId()
	}
	public function endMatch($key = 'Match', $table = 'Match')
	{
		unset($this->session->{'match-'.$key.'-'.$this->uniqueId()});
		unset($this->mem->{'match-'.$key.'-'.$this->uniqueId()});
		$this->{'s'.$table}
			->whereAdv($user)
			->whereUser($start)
			->whereStateLower(2)
			->update([
				'state' => 2,
				'register' => Raw('NOW()')
			]);
	}
	public function start($start = null, $table = 'Match')
	{
		if(is_null($start))
		{
			$start = get_post('start', 0, 'int');
		}
		if($start > 0)
		{
			$this->{'s'.$table}
				->whereStateLower(3)
				->whereRegisterLower(Raw('DATE_SUB(NOW(), INTERVAL 1 HOUR)'))
				->delete();
			if($this->{'s'.$table}
				->select('state')
				->whereAdv($user)
				->whereUser($start)
				->whereStateLower(3)
				->get($data))
			{
				$this->{'s'.$table}
					->whereAdv($user)
					->whereUser($start)
					->whereStateLower(2)
					->update([
						'state' => 2,
						'register' => Raw('NOW()')
					]);
				return true;
			}
			return false;
		}
		else
		{
			return $this->started(null, null, null, $table);
		}
	}
	public function started($adv = null, $user = null, $force = null, $table = 'Match')
	{
		$return = false;
		$remove = false;
		if(is_null($adv))
		{
			$adv = get_post('adv', 0, 'int');
		}
		if(is_null($force))
		{
			$force = get_post('force', false, 'bool');
		}
		if(is_null($user))
		{
			$remove = ($this->getData('user') === null);
			$user = $this->cUser->id;
		}
		if($adv > 0)
		{
			if($this->session->adv === $adv)
			{
				return true;
			}
			$this->{'s'.$table}
				->whereState(0)
				->whereRegisterLower(Raw('DATE_SUB(NOW(), INTERVAL 1 HOUR)'))
				->delete();
			if($this->{'s'.$table}
				->select('id', 'state', 'user', 'adv')
				->andState(0)
				->andUser($user)
				->andAdv($adv)
				->orState(0)
				->andUser($adv)
				->andAdv($user)
				->get($data))
			{
				$state = $data->state;
				if($adv === $data->adv)
				{
					if($state < 2)
					{
						$state++;
					}
				}
				else
				{
					$this->mem->delete('g'.$table.'-'.$data->adv);
					$state = 0;
				}
				if($adv !== $data->adv || $state !== $data->state)
				{
					$this->mem->delete('g'.$table.'-'.$adv);
					$this->{'s'.$table}
						->whereId($data->id)
						->update([
							'state' => $state,
							'adv' => $adv,
							'register' => Raw('NOW()')
						]);
				}
				if($state >= 2)
				{
					$this->adv = $adv;
					$this->user = $user;
					$return = true;
				}
			}
			else
			{
				$this->mem->delete('g'.$table.'-'.$adv);
				$this->{'s'.$table}
					->insert([
						'state' => 0,
						'adv' => $adv,
						'user' => $user,
						'register' => Raw('NOW()')
					]);
			}
		}
		if($force || !$this->mem->exists('g'.$table.-'.$user))
		{
			$this->mem->set('g'.$table'-'.$user, true);
			$this->setData('g'.$table,
				$this->{'s'.$table}
					->select('state', 'user', 'register')
					->whereAdv($user)
					->whereStateLower(3)
					->fetchAll()
			);
		}
		if($remove)
		{
			$this->removeData('user');
		}
		return $return;
	}
}