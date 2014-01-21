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

	protected function asDateTime($value)
	{
		return new TranslatableDateTime(parent::asDateTime($value));
	}

	static protected function myIp()
	{
		return static::where('ip', ip2bin());
	}

	static public function log($searchQuery = '', $results = 0)
	{
		return static::create(array(
			'search_query' => $searchQuery,
			'ip' => ip2bin(),
			'results' => $results,
			'created_at' => new DateTime
		));
	}

	static public function mine($page = null, $resultsPerPage = null)
	{
		$result = static::myIp()
			->select('search_query', 'created_at', 'results')
			->groupBy('id')
			->orderBy('created_at', 'desc');
		if(!is_null($page))
		{
			$result = $result->forPage($page, $resultsPerPage);
		}
		return $result->get();
	}

	static public function mineCount()
	{
		return (int) static::myIp()->count();
	}

	static public function startWith($searchQuery = '')
	{
		$result = static::select('search_query', 'results', DB::raw('COUNT(DISTINCT ip) AS count'))
			->whereRaw('LOWER(search_query) LIKE ?', array(addcslashes(strtolower($searchQuery), '_%') . '%'))
			->whereRaw('LENGTH(search_query) > 1')
			->where('results', '>', 0)
			->groupBy('search_query')
			->orderBy('count', 'desc')
			->orderBy('results', 'desc')
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