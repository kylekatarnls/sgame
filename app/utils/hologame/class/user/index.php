<?php

namespace Hologame;

class User
{
	use Trait°Error;
	protected $data, $object, $id = 0;
	const SESSION_KEY = 'gUser';
	public function extractFromSession()
	{
		$this->data = $this->object->session->{self::SESSION_KEY};
		if(is_array($this->data))
		{
			if(isset($this->data['id']))
			{
				$this->id = $this->data['id'];
				unset($this->data['id']);
				$this->buildData();
			}
		}
		else
		{
			$this->data = [];
			if($this->object->cookie->exists('auto', $cookie))
			{
				$id = hexdec(substr($cookie, 0, 8));
				$code = substr($cookie, 8);
				if($this->object
					->sAutoconnexion
					->byUser($id)
					->byCookie(hex2bin($code))
					->count() === 1)
				{
					$this->logIn($id);
					$this->cookieAuto($cookie);
				}
			}
		}
	}
	public function __construct()
	{
		$this->object = new Object;
		$this->extractFromSession();
	}
	public function get($name, $default = null)
	{
		switch($name)
		{
			case 'mail':
				return $this->mailUser.'@'.$this->mailHost;
				break;
		}
		$data = array_value($this->data, $this->id, [], 'array');
		if(empty($data))
		{
			$data = $this->object->sUser
				->select('*')
				->byId($this->id)
				->fetch();
			if($data === false)
			{
				throw new UserException(s("Utilisateur n°{number} introuvable.", [ 'number' => $this->id ]), 1);
				return false;
			}
		}
		return array_value(
			$data,
			$name,
			$default
		);
	}
	public function __get($name)
	{
		return $this->get($name);
	}
	public function getArray()
	{
		$data = [];
		$gKey = func_get_args();
		foreach($gKey as $key)
		{
			$data[$key] = $this->__get($key);
		}
		return $data;
	}
	public function fetchAll($cols = [])
	{
		$data = $this->object->sUser;
		foreach((array) $cols as $name => $value)
		{
			$data->whereCol($name, $value);
		}
		return $data->fetchAll(null, 'id');
	}
	public function __set($name, $value)
	{
		if(!isset($this->data[$this->id]) || !is_array($this->data[$this->id]))
		{
			$this->data[$this->id] = [];
		}
		$this->data[$this->id][$name] = $value;
	}
	public function logBy(array $data, &$info = null)
	{
		$sUser = $this->object->sUser
			->select('*');
		$testPassword = isset($data['password']);
		if($testPassword)
		{
			$password = $data['password'];
			unset($data['password']);
		}
		foreach($data as $column => $value)
		{
			$sUser = $sUser->whereCol($column, $value);
		}
		$sUser = $sUser->fetch();
		if($sUser === false)
		{
			$info = array_keys($data);
			return false;
		}
		if(isset($sUser->password))
		{
			if($testPassword && $sUser->password !== Security°Hash::hash($password, sha1($sUser->register, true)))
			{
				$info = ['password'];
				return false;
			}
			unset($sUser->password);
		}
		return $sUser;
	}
	public function logged($post = null)
	{
		if($post !== null)
		{
			if($post === 'post')
			{
				if(get_post('logout', false, 'bool'))
				{
					unset($this->object->cookie->auto);
					$this->logOut();
				}
				else
				{
					$post = get_post(['name', 'password', 'action']);
					if($post['name'] !== null && $post['password'] !== null)
					{
						if(!$this->object->cSecurity°Field->check(false))
						{
							$this->errorInfo = [
								'logIn' => 'securityField',
								'signIn' => 'securityField'
							];
						}
						else if($post['action'] === 'sign')
						{
							unset($post['action']);
							$post = array_merge($post, get_post(['password2', 'email', 'email2']));
							if(strlen($post['password']) < 5)
							{
								$this->error('signIn', 'password');
							}
							else if($post['password'] !== $post['password2'])
							{
								$this->error('signIn', 'password2');
							}
							if(filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false)
							{
								$this->error('signIn', 'email');
							}
							else if($post['email'] !== $post['email2'])
							{
								$this->error('signIn', 'email2');
							}
							else
							{
								list($user, $host) = end_separator('@', $post['email']);
								if($this->object->sUser
									->byMailUser($user)
									->byMailHost($host)
									->count() > 0)
								{
									$this->error('signIn', 'email');
									$this->error('duplicate', true);
								}
							}
							if($this->object->sUser
								->byName($post['name'])
								->count() > 0)
							{
								$this->error('signIn', 'name');
								$this->error('duplicate', true);
							}
							if(!$this->isError('signIn'))
							{
								$register = date('Y-m-d H:i:s');
								$sUser = $this->object->sUser;
								$sUser->insert([
									'name' => $post['name'],
									'password' => Security°Hash::hash($post['password'], sha1($register, true)),
									'mailUser' => $user,
									'mailHost' => $host,
									'register' => $register
								]);
								$this->logIn($sUser->lastInsertId());
								$this->object->setData('error', new Html('div', [
									'class' => 'succes',
									'content' => s('Inscription réussie')
								]));
							}
						}
						else
						{
							unset($post['action']);
							$key = 'log-'.$post['name'];
							$bruteForce = $this->object->cSecurity°Antibruteforce($key);
							if($bruteForce->check($info) === false)
							{
								if(is_numeric($info))
								{
									$info = 'bruteForce';
								}
								$this->errorInfo = [
									'logIn' => 'bruteForce',
									'errorName' => $info,
									'bruteForceKey' => $key
								];
								return false;
							}
							$this->errorInfo = [
								'count' => $info
							];
							if($this->object->cSecurity°Field->check(false))
							{
								if(false === $this->logIn($post))
								{
									$bruteForce->put();
									$this->errorInfo['logIn'] = 'failed';
								}
							}
						}
					}
				}
			}
		}
		if($post !== null && $this->id > 0 && get_post('auto', false, 'bool'))
		{
			$sAuto = $this->object->sAutoconnexion;
			if($data = $sAuto->byUser($this->id)->fetch())
			{
				$cookie = bin2hex($data->cookie);
			}
			else
			{
				$cookie = random(16, '0123456789abcdef');
				$sAuto->insert([
					'cookie' => hex2bin($cookie),
					'user' => $this->id
				]);
			}
			$this->cookieAuto(str_pad(dechex($this->id), 8, '0', STR_PAD_LEFT).$cookie);
		}
		return ($this->id > 0);
	}
	protected function cookieAuto($code)
	{
		$this->object->cookie->set('auto', $code, 720000);
	}
	public function buildData()
	{
		$data = array_value($this->data, $this->id, null, 'object');
		if(!is_null($data))
		{
			$data->mail = $this->mail;
			$this->object->setData('user', $data);
		}
		return $data;
	}
	public function logIn($id = null)
	{
		if(is_array($id))
		{
			$sUser = $this->logBy($id);
			if(empty($sUser))
			{
				return false;
			}
			$this->id = intval($sUser->id);
			$this->data[$this->id] = $sUser;
		}
		else
		{
			$this->id = intval($id);
			if(empty($this->data[$this->id]))
			{
				$this->data[$this->id] = $this->logBy([ 'id' => $id ]);
			}
		}
		$this->buildData();
		return true;
	}
	public function logOut()
	{
		unset($this->data[$this->id]);
		$this->id = 0;
	}
	public function __destruct()
	{
		if(empty($this->data))
		{
			unset($this->object->session->{self::SESSION_KEY});
		}
		else
		{
			$this->data['id'] = $this->id;
			$this->object->session->{self::SESSION_KEY} = $this->data;
		}
	}
}

class UserException extends Exception {}

?>