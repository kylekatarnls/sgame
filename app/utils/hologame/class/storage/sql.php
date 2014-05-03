<?php

namespace Hologame;

class Storage°Sql extends PDO
{
	use Core§Trait°Option, Core§Trait°Renew;
	protected $db = SQL_DEFAULT_DB,
		$engine = 'InnoDB',
		$mainTable,
		$mem = false,
		$calcRows = null,
		$gColumn = [],
		$where = '',
		$limit = '',
		$group = '',
		$order = '',
		$cache = null,
		$previousQuery = '',
		$beforeFetch;
	public function __construct($db = null, array $options = [])
	{
		$this->renew = false;
		if(is_array($db))
		{
			$options = $db;
		}
		else if($db !== null)
		{
			$this->db = $db;
		}
		parent::__construct(
			'mysql:host='.SQL_HOST.';port=3306;dbname='.$this->db.';',
			SQL_USER,
			SQL_PASS
		);
		parent::setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		parent::exec("SET NAMES 'UTF8'");
	}
	public function __toString()
	{
		$where = trim($this->where);
		if(start($where, 'WHERE'))
		{
			return '('.trim(wubwtr($where,5)).')';
		}
	}
	public function mem($mem = true)
	{
		if(!is_string($mem))
		{
			settype($mem, 'bool');
		}
		$this->mem = $mem;
		return $this;
	}
	public function cleanMem($key = null)
	{
		if($key === null)
		{
			$key = $this->getQuery();
		}
		$oMem = prop('mem');
		$oMem->delete([
			'fetch-'.$key,
			'fetchAll-'.$key,
			'rows-'.$key
		]);
	}
	public function calcRows(&$calcRows = null)
	{
		$state = (func_num_args() === 1);
		if($state)
		{
			$this->calcRows = &$calcRows;
		}
		return $this->setOption(SQL_CALC_ROWS, $state);
	}
	public function selectTable($table)
	{
		$this->renew = true;
		$this->mainTable = $table;
		return $this;
	}
	static public function selectArg(array $gArg)
	{
		if(is_array(array_value($gArg, 0)))
		{
			$return = [];
			foreach($gArg as $arg)
			{
				$return = array_merge($return, (array) $arg);
			}
			return $return;
		}
		return $gArg;
	}
	public function select()
	{
		$this->gColumn = self::selectArg(func_get_args());
		return $this;
	}
	public function selectMore()
	{
		$this->gColumn = array_merge($this->gColumn, self::selectArg(func_get_args()));
		return $this;
	}
	public function log($rq = null, $mem = false)
	{
		static $gLog;
		if($rq === null)
		{
			return (array) $gLog;
		}
		$log = ($mem === false ?
			[$rq, 'sql'] :
			[$rq, 'mem', $mem]
		);
		array_add($gLog, [$log]);
	}
	public function closeQuery($rq)
	{
		if($this->option(SQL_CALC_ROWS))
		{
			$this->calcRows = $this->rows();
		}
		$this->log($rq);
	}
	public function query($rq = false)
	{
		if($rq === false)
		{
			$rq = $this->getQuery();
		}
		$r=parent::query($rq);
		$this->closeQuery($rq);
		return $r;
	}
	public function exec($rq = false)
	{
		if($rq === false)
		{
			$rq = $this->getQuery();
		}
		$r=parent::exec($rq);
		$this->closeQuery($rq);
		return $r;
	}
	public function pExec($rq, $p, $rc=true)
	{
		$q=$this->prepare($rq);
		$q->execute((array) $p);
		$this->closeQuery($rq);
		$q->closeCursor();
		return $rc ? $q->rowCount() : $q;
	}
	public function getQuery($table = null)
	{
		if($table === null)
		{
			$table = $this->mainTable;
		}
		$this->beforeFetch = [];
		$tableSchema = $this->getSchema($table);
		$baseId = array_value($tableSchema, 'id');
		$baseId = (empty($baseId) ? [] : ['id']);
		$mTable = $this->mQuote($table);
		$select = $this->gColumn;
		$gKey = array_keys($select);
		foreach(array_keys($select, '*', true) as $key)
		{
			$pos = array_search($key, $gKey, true);
			$select = array_merge(
				$baseId,
				array_slice($select, 0, $pos, true),
				array_keys((array) $tableSchema->gColumn),
				array_slice($select, $pos+1, null, true)
			);
		}
		foreach($select as $alias => &$column)
		{
			if(is_a($column, 'Raw'))
			{
				$column = strval($column);
			}
			else
			{
				$iColumn = $column;
				if(is_array($column))
				{
					$column = $alias;
				}
				$column = $this->mQuote($column);
				$column = $mTable.'.'.$column;
				$as = $column;
			}
			if(!is_numeric($alias))
			{
				if(isset($as))
				{
					$as = $alias;
				}
				$column .= ' AS '.$alias;
			}
			if(isset($as))
			{
				$this->beforeFetch[$as] = [
					'table' => $table,
					'column' => $iColumn
				];
				unset($as, $iColumn);
			}
		}
		$commande = 'SELECT '.($this->option(SQL_CALC_ROWS) ? 'SQL_CALC_FOUND_ROWS ' : '');
		return $this->remainQuery($commande.implode(', ', array_values($select)).' FROM '.$mTable.' '.$this->where.' '.$this->group.' '.$this->order.' '.$this->limit);
	}
	static public function remainQuery($rq = null)
	{
		static $rrq;
		if($rq !== null)
		{
			$rrq = $rq;
		}
		return $rrq;
	}
	public function getType($data)
	{
		$isObject = is_object($data);
		if((!$isObject && !is_array($data)) || ($isObject && is_a($data, 'Raw')))
		{
			return $data;
		}
		$gColumn = $this->getSchema($this->mainTable)->gColumn;
		$data = (array) $data;
		foreach($data as $column => &$value)
		{
			$type = $gColumn->$column;
			switch(array_value($type, 'type'))
			{
				case 'int':
					$step = array_value($type, 'step', 1, 'int');
					list($min, $max) = $this->getLimit($type);
					$value /= $step;
					$value -= $min;
					$value = intval($value);
					break;
				/*
				case 'float':
					$step = array_value($type, 'step', 0, 'float');
					if($step)
					{
						list($min, $max) = $this->getLimit($type);
						$value /= $step;
						$value -= $min;
					}
					$value = round($value);
					break;
				//*/
			}
		}
		if($isObject)
		{
			$data = (object) $data;
		}
		return $data;
	}
	public function setType($data)
	{
		$isObject = is_object($data);
		if(is_a($data, 'Raw') || (!$isObject && !is_array($data)))
		{
			return $data;
		}
		$data = (array) $data;
		foreach($data as $key => &$value)
		{
			if(!isset($this->beforeFetch[$key]))
			{
				$key=$this->mQuote($key);
				if(!isset($this->beforeFetch[$key]))
				{
					$key=$this->mQuote($this->mainTable).'.'.$key;
				}
			}
			if(isset($this->beforeFetch[$key]))
			{
				$gColumn = $this->getSchema($this->beforeFetch[$key]['table'])->gColumn;
				$column = $this->beforeFetch[$key]['column'];
				if(is_array($column))
				{
					// Trouver une astuce pour ne faire qu'une seule jointure
				}
				else
				{
					$type = array_value($gColumn, $column);
					$type = (object) ($type === 'join' ? ['type' => 'join'] : $type);
					switch(array_value($type, 'type'))
					{
						case 'join':
						case 'int':
							$step = array_value($type, 'step', 1, 'int');
							list($min, $max) = $this->getLimit($type);
							$value *= $step;
							$value += $min;
							$value = intval($value);
							break;
						/*
						case 'float':
							$step = array_value($type, 'step', 0, 'float');
							if($step)
							{
								list($min, $max) = $this->getLimit($type);
								$value *= $step;
								$value += $min;
							}
							$value = floatval($value);
							break;
						//*/
					}
				}
			}
		}
		if($isObject)
		{
			$data = (object) $data;
		}
		return $data;
	}
	public function fetch($type = null, $all = false, $allKey = null)
	{
		$method = ($all ? 'fetchAll' : 'fetch');
		$failReturnValue = ($all ? [] : false);
		if(empty($this->gColumn))
		{
			$this->gColumn = ['*'];
		}
		$rq = $this->getQuery();
		if($this->mem !== false)
		{
			$mem = (is_string($this->mem) ? $this->mem : $rq);
			$key = $method.'-'.$mem;
			$obj = new Object;
			if($obj->mem->exists($key, $data))
			{
				$this->log($rq, $key);
			}
			else
			{
				if($this->createIfNot('query', $rq, $query) === false)
				{
					return $failReturnValue;
				}
				$data = $query->$method($type);
				$obj->mem($key, $data);
				if($this->option(SQL_CALC_ROWS))
				{
					$obj->mem('rows-'.$mem, $this->rows());
				}
			}
		}
		else
		{
			if($this->createIfNot('query', $rq, $query) === false)
			{
				return $failReturnValue;
			}
			if($all && !is_null($allKey))
			{
				$data = [];
				while($d = $query->fetch($type))
				{
					$data[$d->{$allKey}] = $d;
				}
			}
			else
			{
				$data = $query->$method($type);
			}
		}
		$data = ($all ? array_map([$this, 'setType'], $data) : $this->setType($data));
		return $data;
	}
	public function count()
	{
		// Si pas de select
		if($this->gColumn === [])
		{
			$fetch = $this
				->select(['c' => Raw('COUNT(*)')])
				->fetch();
			return ($fetch === false ? false : intval($fetch->c));
		}
		$rq = $this->getQuery();
		if($this->mem !== false)
		{
			$mem = (is_string($this->mem) ? $this->mem : $rq);
			$key = 'fetchAll-'.$mem;
			$oMem = prop('mem');
			if($oMem->exists($key, $data))
			{
				$this->log($rq, $key);
				return count($data);
			}
		}
		if($this->createIfNot('query', $rq, $query) === false)
		{
			return false;
		}
		return $query->rowCount();
	}
	public function fetchAll($type = null, $allKey = null)
	{
		return $this->fetch($type, true, $allKey);
	}
	public function get(&$data)
	{
		$data = $this->fetch();
		return $data !== false;
	}
	public function getAll(&$gData, $allKey = null)
	{
		$gData = $this->fetchAll(null, true, $allKey);
		return $this;
	}
	public function where($where)
	{
		$this->where = trim($where);
		if(stripos($this->where, 'where') !== 0)
		{
			$this->where = 'WHERE '.$this->where;
		}
		return $this;
	}
	public function whereCol($column, $value, $symbol = '=', $operator = 'and')
	{
		if(is_array($value) || (is_object($value) && !is_a($value, 'Raw')))
		{
			$symbol = (in_array($symbol, ['!=', '<>']) ? 'NOT IN' : 'IN');
		}
		return $this->{'_'.$operator}($this->mQuote($column).' '.$symbol.' '.$this->mixedQuote($value));
	}
	public function group($group = 'id', $table = null)
	{
		if($table === null)
		{
			$table = $this->mainTable;
		}
		$this->group = 'GROUP BY '.$this->mQuote($table).'.'.implode(', ',array_map([$this, 'mQuote'], (array) $group));
		return $this;
	}
	public function order($order = 'id', $table = null)
	{
		$this->order = 'ORDER BY '.$order;
		return $this;
		// À faire évoluer
		if($table === null)
		{
			$table = $this->mainTable;
		}
		$this->order = 'ORDER BY '.$this->mQuote($table).'.'.implode(', ',array_map([$this, 'mQuote'], (array) $order));
		return $this;
	}
	public function limit($offset, $length = null)
	{
		$this->limit = 'LIMIT '.$offset.($length !== null ? ', '.$length : '');
		return $this;
	}
	public function _and($where)
	{
		return $this->where((empty($this->where) ? '' : $this->where.' AND ').trim($where));
	}
	public function _or($where)
	{
		return $this->where((empty($this->where) ? '' : $this->where.' OR ').trim($where));
	}
	public function a($where)
	{
		return $this->_and($where);
	}
	public function o($where)
	{
		return $this->_or($where);
	}
	public function cache($cache = true)
	{
		$this->cache = $cache;
	}
	private function mixedQuote($value)
	{
		if(is_a($value, 'Raw'))
		{
			return $value;
		}
		if(is_array($value) || is_object($value))
		{
			return '('.implode(', ', array_map([$this, 'quote'], (array) $value)).')';
		}
		return $this->quote($value);
	}
	public function __call($method, $gArg)
	{
		$operator = null;
		foreach(['where', 'and', 'or'] as $op)
		{
			if(start($method, $op))
			{
				$operator = $op;
			}
		}
		if($operator !== null)
		{
			$opratorLength = strlen($operator);
			$gWord = [
				'Is' => '=',
				'Equal' => '=',
				'Not' => '!=',
				'IsNot' => '!=',
				'Different' => '!=',
				'Upper' => '>',
				'Lower' => '<',
				'UpperOrEqual' => '>=',
				'LowerOrEqual' => '<=',
				'Like' => 'LIKE',
				'NotLike' => 'NOT LIKE',
				'In' => 'IN',
				'NotIn' => 'NOT IN',
			];
			foreach($gWord as $word => $symbol)
			{
				if(finish($method, $word))
				{
					return $this->whereCol(lcfirst(substr($method, $opratorLength, -strlen($word))), $gArg[0], $symbol, $operator === 'where' ? 'and' : $operator);
				}
			}
			return $this->whereCol(lcfirst(substr($method, $opratorLength)), $gArg[0], array_value($gArg, 1, '='));
		}
		if(start($method, 'by'))
		{
			return $this->whereCol(lcfirst(substr($method, 2)), $gArg[0], '=');
		}
		if(start($method, 'not'))
		{
			return $this->whereCol(lcfirst(substr($method, 3)), $gArg[0], '!=');
		}
		if(start($method, 'or'))
		{
			return $this->whereCol(lcfirst(substr($method, 2)), $gArg[0], '=');
		}
		if(start($method, 'and'))
		{
			return $this->whereCol(lcfirst(substr($method, 3)), $gArg[0], '=');
		}
		if(in_array($method, [
			'analyse',
			'check',
			'drop',
			'optimize',
			'repair',
			'truncate'
		]))
		{
			$table = (isset($gArg[0]) && is_string($gArg[0]) ? $gArg[0] : $this->mainTable);
			return $this->exec(strtoupper($method).' TABLE '.$this->mQuote($table));
		}
		throw new SqlException("Unknown method ".$method, -1);
		return $this;
	}
	public static function mQuote($s)
	{
		if(is_a($s, 'Raw'))
		{
			return strval($s);
		}
		$s = '`'.preg_replace('#[^a-zA-Z0-9_-]#', '', $s).'`';
		return $s;
	}
	public function sQuote($s)
	{
		if($s === null)
		{
			return 'NULL';
		}
		if(is_a($s, 'Raw'))
		{
			return strval($s);
		}
		return $this->quote($s);
	}
	public function rows()
	{
		if($this->mem !== false)
		{
			$mem = is_string($this->mem) ? $this->mem : $rq;
			$oMem = prop('mem');
			if($oMem->exists('rows-'.$mem, $rows))
			{
				return $rows;
			}
		}
		$q=parent::query('SELECT FOUND_ROWS() AS `nb`');
		$nb=$q->fetch()->nb;
		if(is_numeric($nb))
		{
			$nb = intval($nb);
		}
		return $nb;
	}
	public function insert($gKey, $data = null, $returnThis = false)
	{
		if(is_bool($data))
		{
			$returnThis = $data;
			$data = null;
		}
		if($data === null)
		{
			$data = $gKey;
			if(is_sub_array($data))
			{
				$gKey = array_keys(reset($data));
				foreach($data as &$array)
				{
					if($gKey !== array_keys($array))
					{
						throw new SqlException("Keys of all elements must be identic", -2);
						return false;
					}
					$array = implode(', ',
						array_map([$this, 'sQuote'],
						array_values(
							array_map([$this, 'getType'],
							array_combine($gKey, $array)
						)
					)));
				}
			}
			else
			{
				$gKey = array_keys($data);
				$data = [implode(', ',
					array_map([$this, 'sQuote'],
					array_values(
						array_map([$this, 'getType'],
						$data
					)
				)))];
			}
		}
		else
		{
			if(is_sub_array($data))
			{
				foreach($data as &$array)
				{
					$array = implode(', ',
						array_map([$this, 'sQuote'],
						array_values(
							array_map([$this, 'getType'],
							array_combine($gKey, $array)
						)
					)));
				}
			}
			else
			{
				$data = [implode(', ',
					array_map([$this, 'sQuote'],
					array_values(
						array_map([$this, 'getType'],
						$data
					)
				)))];
			}
		}
		$gKey = array_map([$this, 'mQuote'], $gKey);
		$exec = 'INSERT INTO '.$this->mQuote($this->mainTable).' ('.implode(', ', $gKey).') VALUES('.implode('), (', $data).')';
		$return1 = $this->createIfNot('exec', $exec, $return2);
		return $returnThis ? $this : ($return1 && $return2);
	}
	public function update($data = null)
	{
		$gValue = [];
		foreach((array) $data as $column => $value)
		{
			$gValue[] = $this->mQuote($column).' = '.$this->sQuote($value);
		}
		$exec = 'UPDATE '.$this->mQuote($this->mainTable).' SET '.implode(', ', $gValue).' '.$this->where;
		$return1 = $this->createIfNot('exec', $exec, $return2);
		return ($return1 && $return2);
	}
	public function delete($gCol = [])
	{
		foreach((array) $gCol as $column => $value)
		{
			$this->wherCol($column, $value);
		}
		$exec = 'DELETE FROM '.$this->mQuote($this->mainTable).' '.$this->where;
		debug($exec);
		$return1 = $this->createIfNot('exec', $exec, $return2);
		return ($return1 && $return2);
	}
	protected function createIfNot($method, $rq, &$return = null)
	{
		try
		{
			$return = $this->$method($rq);
			return true;
		}
		catch(PDOException $e)
		{
			try
			{
				if(in_array($e->getCode(), [1146, 42, 42000, '42S02']) && $this->create())
				{
					$return = $this->$method($rq);
					return true;
				}
			}
			catch(PDOException $e2)
			{
				if(!in_array($e2->getCode(), [1146, 42, 42000, '42S02']))
				{
					$e = $e2;
				}
				else
				{
					throw $e2;
				}
			}
			throw $e;
		}
		return false;
	}
	protected function getLimit($type)
	{
		$min = array_value($type, 'min', 0, 'int');
		if(isset($type->max))
		{
			if(start($type->max, 'B'))
			{
				$max = pow(256, intval(substr($type->max, 1))) - 1 + $min;
			}
			else if(start($type->max, '+'))
			{
				$max = pow(256, bytes($min, intval(substr($type->max, 1)))) - 1 + $min;
			}
			else
			{
				$max = intval($type->max);
			}
		}
		else
		{
			return 0xFFFFFFFF;
		}
		return [$min, $max];
	}
	protected function column($column, $type)
	{
		if($type === 'join')
		{
			$type = ['type' => 'join'];
		}
		$type = (object) $type;
		$null = (array_value($type, 'null', false, 'bool') ? '' : 'NOT ').'NULL ';
		switch($type->type)
		{
			case 'join':
				$table = array_value($type, 'table', $column);
				$tableSchema = $this->getSchema($table);
				if($tableSchema === false)
				{
					throw new SqlException(s("La table {name} est introuvable", ['name' => $table]), -8);
					return false;
				}
				$id = array_value($tableSchema, 'id');
				if($id === null)
				{
					throw new SqlException(s("La table {name} n'a pas d'id", ['name' => $table]), -8);
					return false;
				}
				$type = (object) [
					'type' => 'int',
					'max' => $id
				];
			case 'int':
				$step = array_value($type, 'step', 1, 'int');
				list($min, $max) = $this->getLimit($type);
				$bytes = bytes($min, $max);
				switch($bytes)
				{
					case 1:
						$value = array_value($type, 'value', 3, 'int');
						$sqlType = 'TINYINT';
						break;
					case 2:
						$value = array_value($type, 'value', 5, 'int');
						$sqlType = 'SMALLINT';
						break;
					case 3:
						$value = array_value($type, 'value', 8, 'int');
						$sqlType = 'MEDIUMINT';
						break;
					case 4:
						$value = array_value($type, 'value', 11, 'int');
						$sqlType = 'INT';
						break;
					case 8:
					default:
						$value = array_value($type, 'value', 20, 'int');
						$sqlType = 'BIGINT';
				}
				$sqlType .= '('.$value.') UNSIGNED '.(array_value($type, 'zerofill', false, 'bool') ? 'ZEROFILL ' : '');
				break;
			case 'text':
				list($min, $max) = $this->getLimit($type);
				$bytes = bytes(0, $max);
				switch($bytes)
				{
					case 1:
						$sqlType = ($min === $max ? 'CHAR' : 'VARCHAR').'('.$max.')';
						break;
					case 2:
						$sqlType = 'TEXT';
						break;
					case 3:
						$sqlType = 'MEDIUMTEXT';
						break;
					case 4:
					default:
						$sqlType = 'LONGTEXT';
				}
				break;
			case 'bin':
				list($min, $max) = $this->getLimit($type);
				$bytes = bytes(0, $max);
				switch($bytes)
				{
					case 1:
						$sqlType = ($min === $max ? 'BINARY' : 'VARBINARY').'('.$max.')';
						break;
					case 2:
						$sqlType = 'BLOB';
						break;
					case 3:
						$sqlType = 'MEDIUMBLOB';
						break;
					case 4:
					default:
						$sqlType = 'LONGBLOB';
				}
				break;
			default:
				$sqlType = strtoupper($type->type);
				if(isset($type->value))
				{
					$sqlType .= '(';
					if(is_array($type->value))
					{
						$sqlType .= "'".implode("', '", $type->value)."'";
					}
					else
					{
						$sqlType .= json_encode($type->value);
					}
					$sqlType .= ')';
				}
				
		}
		return $this->mQuote($column).' '.$sqlType.' '.$null.array_value($type, 'options', '');
	}
	public function getSchema($table = null)
	{
		if($table === null)
		{
			$table = $this->mainTable;
		}
		static $gTable = [];
		if(isset($gTable[$table]))
		{
			return $gTable[$table];
		}
		$tableSchemaFile = host_or_core(SQL_REL_DIR.$table, '.json');
		if($tableSchemaFile === false)
		{
			$tableSchemaFile = host_or_core(SQL_REL_DIR.$table, '.dia');
			if($tableSchemaFile === false)
			{
				throw new SqlException("No table schema file found for ".$table, -3);
				return false;
			}
			$dia = (new Util°Dia)->fromFile($tableSchemaFile)->getData();
			throw new SqlException("Dia diagrams reading not yet implemented. / Lecture des diagrammes Dia pas encore implémentée.", -7);
			return false;
		}
		else
		{
			$tableSchema = json_decode(file_get_contents($tableSchemaFile));
			if($tableSchema === null)
			{
				$tableSchema = json_decode(str_replace("'", '"', $tableSchema));
			}
		}
		if(isset($tableSchema->gColumn) === false)
		{
			throw new SqlException($table." table schema must be a json encoded object and must contain a gColumn named array", -3);
			return false;
		}
		$gTable[$table] = $tableSchema;
		return $tableSchema;
	}
	public function create($table = null, $gColumnType = null)
	{
		if($table === null)
		{
			$table = $this->mainTable;
		}
		$tableSchema = $this->getSchema($table);
		if($tableSchema === false)
		{
			return false;
		}
		$id = array_value($tableSchema, 'id');
		if($id !== null)
		{
			$tableSchema->gColumn = (object) array_merge(
				[
					'id' => [
						'type' => 'int',
						'max' => $id,
						'options' => 'AUTO_INCREMENT'
					]
				],
				(array) $tableSchema->gColumn
			);
			array_add($tableSchema->primary, 'id');
		}
		$gColumn = [];
		foreach($tableSchema->gColumn as $column => &$type)
		{
			$gColumn[] = $this->column($column, $type);
		}
		if(isset($tableSchema->index))
		{
			array_add($tableSchema->key, $tableSchema->index);
		}
		$data = implode(', ', $gColumn);
		foreach([
			'primary',
			'unique',
			'key'
		] as $property)
		{
			$value = array_value($tableSchema, $property, [], 'array');
			if(!empty($value))
			{
				if($property !== 'key')
				{
					$property .= ' key';
				}
				$data .= ', '.strtoupper($property).'('.implode(', ', array_map([$this, 'mQuote'], (array) $value)).')';
			}
		}
		try
		{
			parent::exec('CREATE TABLE '.$this->mQuote($this->db).'.'.$this->mQuote($table).' (
					'.$data.'
				) ENGINE = '.array_value($tableSchema, 'engine', $this->engine));
		}
		catch(PDOException $e)
		{
			return false;
		}
		return true;
	}
	public function alter($column, $action, $newName = null, $columnType = null)
	{
		if(empty($this->mainTable))
		{
			throw new SqlException("First select a table", -5);
			return false;
		}
		$table = $this->mQuote($this->mainTable);
		if(is_string($action))
		{
			$action = strtoupper($action);
			switch($action)
			{
				case 'CHANGE':
					if(empty($columnType))
					{
						$columnType = array_value($this->getSchema()->gColumn, $column, [], 'array');
						if(empty($columnType))
						{
							throw new SqlException($column." column type not found in the table schema", -6);
							return false;
						}
					}
					return parent::exec('ALTER TABLE '.$table.' CHANGE '.$this->mQuote($column).' '.$this->column($newName, $columnType));

				case 'ADD':
					$columnType = $newName;
					if(empty($columnType))
					{
						$columnType = array_value($this->getSchema()->gColumn, $column, [], 'array');
						if(empty($columnType))
						{
							throw new SqlException($column." column type not found in the table schema", -6);
							return false;
						}
					}
					$columnType = (object) $columnType;
					$exec = 'ALTER TABLE '.$table.' ADD '.$this->column($column, $columnType);
					if(array_value($columnType, 'first', false, 'bool'))
					{
						$exec .= ' FIRST';
					}
					else
					{
						$after = array_value($columnType, 'after');
						if(!empty($after))
						{
							$exec .= ' AFTER '.$this->mQuote($after);
						}
					}
					return parent::exec($exec);

				default:
					return parent::exec('ALTER TABLE '.$table.' '.$action.' '.$this->mQuote($column));
			}
		}
		return false;
	}
	public function change($column, $newName = null, $columnType = null)
	{
		return $this->alter($column, 'CHANGE', $newName, $columnType);
	}
	public function add($column, $columnType = null)
	{
		return $this->alter($column, 'ADD', $columnType);
	}
	public function dropColumn($column)
	{
		return $this->alter($column, 'DROP');
	}
}

class SqlException extends PDOException {}

?>