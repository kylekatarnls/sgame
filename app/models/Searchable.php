<?php

use Illuminate\Database\Query\Expression;
/**
 * Modèle abstrait doté d'outils de recherche
 */
abstract class Searchable extends Eloquent {

	const REMEMBER = false;
	// Entrer une valeur en minutes pour la durée de mise en cache des requêtes SQL
	// ou false pour ne pas mettre les résultats de requêtes SQL en cache
	const KEY_WORD_SCORE = 10;
	const COMPLETE_QUERY_SCORE = 5;
	const ONE_WORD_SCORE = 1;

	static protected $lastQuerySearch = '';

	static public function crossDriver(array $methods)
	{
		$driver = DB::getDefaultConnection();
		if(!isset($methods[$driver]))
		{
			$driver = 'default';
			if(!isset($methods[$driver]))
			{
				return head($methods);
			}
		}
		return $methods[$driver];
	}

	static public function findAndCount($whereToFind, $wordsToFind)
	{
		if(!is_array($wordsToFind))
		{
			$wordsToFind = array($wordsToFind);
		}
		$replace = self::quote($whereToFind);
		$static = new static;
		return DB::raw(
			'(' . implode(' + ', array_map(function ($word) use($replace, $static)
			{
				return '(
					(
						LENGTH(' . $replace . ') -
						LENGTH(REPLACE(LOWER(' . $replace . '), LOWER(' . $static::quote($word) . '), \'\'))
					)
					/ ' . strlen($word) .'
				)';
			}, $wordsToFind)) . ')'
		);
	}

	static public function substr($string, $offset, $length = null)
	{
		return DB::raw(
			static::crossDriver(array(
				'sqlite' => 'SUBSTR',
				'default' => 'SUBSTRING'
			)) .
			'(' . self::quote($string) . ', ' . self::quote($offset) . (is_null($length) ? '' : ', ' . self::quote($length)) . ')'
		);
	}

	static public function substring($string, $offset, $length = null)
	{
		return static::substr($string, $offset, $length);
	}

	static public function caseWhen($case, $when = null, $else = null)
	{
		if(is_array($case))
		{
			$else = $when;
			$when = $case;
			$case = null;
		}
		$return = '(CASE ' . self::quote($case) . ' ';
		if(!empty($when) && is_array($when))
		{
			foreach($when as $if => $then)
			{
				$return .= 'WHEN ' . self::quote($if) . ' THEN ' . self::quote($then) . ' ';
			}
		}
		if(!is_null($else))
		{
			$return .= 'ELSE ' . self::quote($else) . ' ';
		}
		$return .= 'END)';
		return DB::raw($return);
	}

	static protected function quote($value)
	{
		if(is_int($value) || is_float($value) || $value instanceof Expression)
		{
			return strval($value);
		}
		return DB::getPdo()->quote($value);
	}

	static protected function words($value)
	{
		if(!is_array($value))
		{
			if(count($tab = explode('"', $value)) > 2)
			{
				$value = array();
				foreach($tab as $i => $val)
				{
					$val = trim($val);
					if(!empty($val))
					{
						if($i & 1)
						{
							$value[] = $val;
						}
						else
						{
							$value = array_merge($value, preg_split('#\s+#', $val));
						}
					}
				}
			}
			else
			{
				$value = preg_split('#\s+#', $value);
			}
		}
		return $value;
	}

	public function getFillable()
	{
		return $this->fillable;
	}

    /**
     * Prépare une requête de recherche
     * 
     * $values peut être passée par référence pour récupérer
     * 
     * @return QueryBuilder $result
     */
	static public function search($value = '', &$values = null)
	{
		$values = self::words($value);
		if($value === '')
		{
			return self::whereRaw('1 = 0');
		}
		static::$lastQuerySearch = urlencode($value);
		$static = new static;
		$result = static::where(function ($result) use($values, $static)
		{
			return call_user_func(
				$static::crossDriver(
					array(
						'pgsql' => function () use($result, $values)
						{
							foreach($values as $value)
							{
								$result->orWhereRaw(
									"searchtext @@ to_tsquery(?)",
									array($value)
								);
							}
							return $result;
						},
						'default' => function () use($result, $values, $static)
						{
							$fillable = $static->getFillable();
							foreach($values as $value)
							{
								foreach($fillable as $column)
								{
									$result->orWhereRaw(
										'LOWER(' . $column . ') LIKE ?',
										array('%' . addcslashes(strtolower($value), '_%') . '%')
									);
								}
							}
							return $result;
						}
					)
				)
			);
		});
		if(static::REMEMBER)
		{
			$result = $result->remember(static::REMEMBER);
		}
		return $result;
	}

	static public function searchCount($query)
	{
		return self::search($query)->count();
	}

}

?>