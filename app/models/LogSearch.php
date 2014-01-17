<?php

/*
 * Log de clic sur un lien sortant
 */
class LogSearch extends Eloquent {

	const REMEMBER = false;
	// Entrer une valeur en minutes pour la durée de mise en cache des requêtes SQL
	// ou false pour ne pas mettre les résultats de requêtes SQL en cache

	protected $collection = 'log_search';
	protected $fillable = array('search_query', 'ip', 'results', 'created_at');
	public $timestamps = false;

	static public function log($searchQuery = '', $results = 0)
	{
		return static::create(array(
			'search_query' => $searchQuery,
			'ip' => ip2bin(),
			'results' => $results,
			'created_at' => new DateTime
		));
	}

	static public function mine()
	{
		return static::select('search_query', 'created_at', 'results')
			->where('ip', ip2bin())
			->orderBy('created_at', 'desc');
	}

	static public function startWith($searchQuery = '')
	{
		$result = static::select('search_query', 'results', DB::raw('COUNT(DISTINCT ip) AS count'))
			->whereRaw('LOWER(search_query) LIKE ?', array(addcslashes(strtolower($searchQuery), '_%') . '%'))
			->whereRaw('LENGTH(search_query) > 1')
			->where('results', '>', 0)
			->groupBy('search_query')
			->orderBy('count', 'DESC')
			->orderBy('results', 'DESC')
			->take(8)
			->lists('search_query');
		if(static::REMEMBER)
		{
			$result = $result->remember(static::REMEMBER);
		}
		return $result;
	}
}

?>