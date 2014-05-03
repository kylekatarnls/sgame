<?php

namespace Hologame;

class Storage°Gsession {

	private static function path() {
		$path = '/var/www/holowar-sd/hologame-h/sessions';
		if(!file_exists($path)) {
			$path = session_save_path();
		}
		return $path;
	}

	public static function set($id) {
		$f = self::path() . '/sess_' . $id;
		if(!file_exists($f)){
			return false;
		}
		session_id($id);
		$data = file_get_contents($f);
		$_SESSION = self::unserialize($data);
		return true;
	}

	public static function get($id) {
		$f = self::path() . '/sess_' . $id;
		if(!file_exists($f)){
			return null;
		}
		$data = file_get_contents($f);
		return self::unserialize($data);
	}

	public static function idList() {
		$ids = [];
		$dir = session_save_path();
		foreach(scandir($dir) as $f) {
			if(strpos($f, 'sess_') === 0) {
				$ids[] = substr($f, 5);
			}
		}
		return $ids;
	}
	public static function unserialize($session_data) {
		$method = ini_get("session.serialize_handler");
		switch ($method) {
			case "php":
				return self::unserialize_php($session_data);
				break;
			case "php_binary":
				return self::unserialize_phpbinary($session_data);
				break;
			default:
				throw new Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
		}
	}

	private static function unserialize_php($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			if (!strstr(substr($session_data, $offset), "|")) {
				throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
			}
			$pos = strpos($session_data, "|", $offset);
			$num = $pos - $offset;
			$varname = substr($session_data, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}

	private static function unserialize_phpbinary($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			$num = ord($session_data[$offset]);
			$offset += 1;
			$varname = substr($session_data, $offset, $num);
			$offset += $num;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}
}

?>