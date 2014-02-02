<?php

/**
 * ModelBuilder apporte quelques améliorations au Builder de Eloquent
 */
class ModelBuilder extends Illuminate\Database\Eloquent\Builder {

	/**
	 * Retourne des variables exploitables pour afficher des données paginées dans la view 
	 *
	 * @return ModelBuilder $dataForView
	 */
	public function paginatedData(&$page, $resultsPerPage, array $mergedData = array())
	{
		$nbResults = clone $this;
		$nbResults = $nbResults->count();
		ResultsPerPage::paginate($nbResults, $page, $choice, $resultsPerPage, $nbPages, $mergedData);
		return array_merge(
			array(
				'nbPages' => (int) $nbPages,
				'currentPage' => (int) $page,
				'results' => $this->forPage($page, $resultsPerPage)->get(),
				'nbResults' => (int) $nbResults,
				'resultsPerPage' => (int) $resultsPerPage,
				'choiceResultsPerPage' => $choice
			),
			$mergedData
		);
	}

	/**
	 * Amélioration de ->count() : élimine les éventuels éléments de requêtes entrant en conflit avec
	 * le COUNT() de SQL
	 *
	 * @return int $nombreDeResultats
	 */
	public function count($column = null)
	{
		if(is_null($column))
		{
			$this->query->select(array());
			$this->query->orders = null;
			$this->query->groups = null;
			$column = DB::raw('DISTINCT ' . $this->query->from . '.id');
		}
		return parent::count($column);
	}

}

?>