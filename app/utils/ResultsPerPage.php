<?php

class ResultsPerPage {

	const DEFAULT_CHOICE = 10;
	const ENUM_CHOICES = '10,50,100,500';

	/**
	 * Retourne un array de int des choix possibles pour le nombre de résultats par page
	 * Par exemple sur ENUM_CHOICES = '10,50,100,500'
	 * Alors getChoices() return array(10, 50, 100, 500)
	 *
	 * @return array $possibleChoices
	 */
	static public function getChoices()
	{
		return array_map('intval', explode(',', static::ENUM_CHOICES));
	}

	/**
	 * Retourne le nombre de résultats par page choisi par l'utilisateur ou DEFAULT_CHOICE
	 * à défaut
	 *
	 * @return int $resultsPerPage
	 */
	static public function getChoice($resultsPerPage = null)
	{
		$defaultResultsPerPage = (int) Cookie::get('resultsPerPage', static::DEFAULT_CHOICE);
		$resultsPerPage = (int) Input::get(
			'resultsPerPage',
			is_null($resultsPerPage) || $resultsPerPage < 1 ?
				Request::get('resultsPerPage', $defaultResultsPerPage) :
				$resultsPerPage
		);
		if($resultsPerPage !== $defaultResultsPerPage)
		{
			Cookie::queue('resultsPerPage', $resultsPerPage, 60); // Crée/Met à jour le cookie "resultsPerPage"
		}
		return $resultsPerPage;
	}

	/**
	 * Complète les URLs pour ajouter le nombre de résultats par page
	 *
	 * @return string $url
	 */
	static public function completeUrl($value, $resultsPerPage = self::DEFAULT_CHOICE)
	{
		return str_replace(
			'{keepResultsPerPage}',
			$resultsPerPage == static::DEFAULT_CHOICE ? '' : '/' . $resultsPerPage,
			$value
		);
	}

	/**
	 * Modifie les valeurs de $page, ^choice, $resultsPerPage, $nbPages et $mergedData en fonction
	 * de la pagination
	 *
	 * @return void
	 */
	static public function paginate($nbResults, &$page = null, &$choice = null, &$resultsPerPage = null, &$nbPages = null, &$mergedData = null)
	{
		$page = (int) $page;
		$resultsPerPage = static::getChoice($resultsPerPage);
		$choice = static::getChoices();
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = static::DEFAULT_CHOICE;
		}
		$nbPages = ceil($nbResults / $resultsPerPage);
		if($page > $nbPages || $page < 1)
		{
			$page = 1;
		}
		$mergedData = array_combine(
			array_keys($mergedData),
			array_map(array('static', 'completeUrl'), $mergedData, array($resultsPerPage))
		);
	}
}

?>