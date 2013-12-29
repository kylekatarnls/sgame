<?php

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

	static protected function isPostgresql()
	{
		return Config::get('database.default') === 'pgsql';
	}

	static protected function quote($value)
	{
		return DB::getPdo()->quote($value);
	}

	static protected function words($value)
	{
		return is_array($value) ? $value : preg_split('#\s+#', $value);
	}

	static public function pgSearch($values)
	{
		foreach($values as $value)
		{
			$where = "searchtext @@ to_tsquery(" . self::quote($value) . ")";
			$result = isset($result) ?
				$result->orWhereRaw($where) :
				self::whereRaw($where);
		}
		return $result;
	}

	static protected function eachLike(&$result = null, $value='')
	{
		$like = 'LIKE ' . self::quote('%' . addcslashes(strtolower($value), '_%') . '%');
		$class = get_called_class();
		$self = new $class;
		foreach($self->fillable as $column)
		{
			$result = is_null($result) ?
				self::whereRaw('LOWER(' . $column . ')' . $like) :
				$result->orWhereRaw('LOWER(' . $column . ')' . $like);	
		}
	}

	static public function likeSearch($values)
	{
		foreach($values as $value)
		{
			self::eachLike($result, $value);
		}
		return $result;
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
		self::$lastQuerySearch = urlencode($value);
		$result = self::isPostgresql() ?
			self::pgSearch($values) :
			self::likeSearch($values);
		if(self::REMEMBER)
		{
			$result = $result->remember(self::REMEMBER);
		}
		return $result;
	}

	static public function searchCount($query)
	{
		return self::search($query)->count();
	}

}

?>