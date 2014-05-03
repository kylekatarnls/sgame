<?php

namespace Hologame;

class FtpException extends ObjectException {}

class Ftp extends Object
{
	protected $host = 'localhost',
		$port = 21,
		$timeout = 6,
		$user = '',
		$pass = '',
		$connexion;

	public function __construct($user = null, $pass = null, $host = null, $port = null, $timeout = null)
	{
		foreach(['user', 'pass', 'host', 'port', 'timeout'] as $param)
		{
			if(!is_null($$param))
			{
				$this->$param = $$param;
			}
			if(empty($this->$param))
			{
				throw new FtpException($param.' is empty', 1);
				return false;
			}
		}
		if(!($this->connexion = ftp_connect($this->host, $this->port, $this->timeout)))
		{
			throw new FtpException('Connexion failed', 2);
			return false;
		}
		if(!$this->login($this->user, $this->pass))
		{
			throw new FtpException('Login failed', 3);
			return false;
		}
	}

	public function __call($method, array $gArg)
	{
		$function = 'ftp_'.strtolower(preg_replace('#[A-Z]#', '_$1', $method));
		if(!function_exists($function))
		{
			$function = 'ftp_'.strtolower($method);
		}
		if(function_exists($function))
		{
			array_unshift($gArg, $this->connexion);
			return call_user_func_array($function, $gArg);
		}
		return parent::__call($method, $gArg);
	}

	public function getContent($remoteFile, $mode = FTP_BINARY)
	{
		$localFile = 'tmp/ftp/get-'.strtr(microtime(true), '.', '-');
		Storage°File::getFile($localFile);
		$result = false;
		if(Storage°File::touch($localFile)
		&& $this->get($localFile, $remoteFile, $mode))
		{
			$result = Storage°File::getContent($localFile);
			Storage°File::unlink($localFile);
		}
		return $result;
	}

	public function putContent($remoteFile, $content, $mode = FTP_BINARY)
	{
		$localFile = 'tmp/ftp/put-'.strtr(microtime(true), '.', '-');
		Storage°File::getFile($localFile);
		$result = false;
		if(Storage°File::touch($localFile))
		{
			$result = (
				Storage°File::putContent($localFile, $content) &&
				$this->put($remoteFile, $localFile, $mode)
			);
			Storage°File::unlink($localFile);
		}
		return $result;
	}

	public function content($file, $content = null, $mode = FTP_BINARY)
	{
		return (is_null($content) ?
			$this->getContent($file, $mode) :
			$this->putContent($file, $content, $mode));
	}

	public function getFiles($dir)
	{
		$list = [];
		foreach($this->rawList($dir) as $file)
		{
			if(!start($file, 'd'))
			{
				$list[] = array_value(preg_split('#\s+#', $file, 9), 8);
			}
		}
		return $list;
	}

	public function getDirs($dir)
	{
		$list = [];
		foreach($this->rawList($dir) as $file)
		{
			if(start($file, 'd'))
			{
				$val = array_value(preg_split('#\s+#', $file, 9), 8);
				if(!in_array($val, ['.', '..']))
				{
					$list[] = $val;
				}
			}
		}
		return $list;
	}

	public function getAll($dir, $separate = false)
	{
		$list = [];
		if($separate)
		{
			$list2 = [];
		}
		if($raw = $this->rawList($dir))
		{
			foreach($raw as $file)
			{
				$val = array_value(preg_split('#\s+#', $file, 9), 8);
				if(!in_array($val, ['.', '..']))
				{
					${'list'.(start($file, 'd') ? '2' : '')}[] = $val;
				}
			}
		}
		return ($separate ? [$list, $list2] : $list);
	}

	public function __destruct()
	{
		$this->close();
	}
}

?>