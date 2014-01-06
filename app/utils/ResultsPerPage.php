<?php

class ResultsPerPage {

	const DEFAULT_CHOICE = 10;
	const ENUM_CHOICES = '10,50,100,500';

	static public function getChoices()
	{
		return array_map('intval', explode(',', self::ENUM_CHOICES));
	}

	static public function getChoice($resultsPerPage = null)
	{
		$defaultResultsPerPage = (int) Cookie::get('resultsPerPage', self::DEFAULT_CHOICE);
		$resultsPerPage = (int) Input::get(
			'resultsPerPage',
			is_null($resultsPerPage) || $resultsPerPage < 1 ?
				Request::get('resultsPerPage', $defaultResultsPerPage) :
				$resultsPerPage
		);
		if($resultsPerPage !== $defaultResultsPerPage)
		{
			Cookie::queue('resultsPerPage', $resultsPerPage, 60);
		}
		return $resultsPerPage;
	}
}

?>