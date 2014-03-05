<?

/*
 * Log de clic sur un lien sortant
 */
LogSearch:Model

	REMEMBER = false;
	// Entrer une valeur en minutes pour la durée de mise en cache des requêtes SQL
	// ou false pour ne pas mettre les résultats de requêtes SQL en cache

	* $collection = 'log_search';
	* $fillable = array('search_query', 'ip', 'results', 'created_at');
	+ $timestamps = false;

	s+ log($searchQuery = '', $results = 0)
		<static::create(array(
			'search_query' => $searchQuery,
			'ip' => ip2bin(),
			'results' => $results,
			'created_at' => new DateTime
		));

	s+ mine()
		<static::where('ip', ip2bin())
			->select('search_query', 'created_at', 'results')
			->groupBy('id')
			->orderBy('created_at', 'desc');

	s+ startWith($searchQuery = '')
		$result = static::select('search_query', DB::raw('SUM(results) AS sum_results'), DB::raw('COUNT(DISTINCT ip) AS count'))
			->whereRaw('LOWER(search_query) LIKE ?', array(addcslashes(strtolower($searchQuery), '_%') . '%'))
			->whereRaw('LENGTH(search_query) > 1')
			->where('results', '>', 0)
			->groupBy('search_query')
			->orderBy('count', 'desc')
			->orderBy('sum_results', 'desc')
			->take(8);
		if(:REMEMBER)
			$result = $result->remember(:REMEMBER);
		<$result->lists('search_query');