<?php

/**
 * Observateur des contenus enregistrés
 */
class CrawledContentObserver {

	public function saved($contentCrawled)
	{
		// On récupère tous les mots et groupes de mots importants du contenus
		preg_match_all('#<strong>(.+)</strong>#sU', $contentCrawled->content, $matches);
		// On les regroupe, on supprime les espaces en trop, on récupère les mots seuls, puis on enlève les doublons
		$words = array_unique(explode(' ', preg_replace('#\s+#', ' ', trim(implode(' ', $matches[1])))));
		$ids = array();
		// Enregistrement de chaque mot-lcé
		foreach($words as $word)
		{
			// On enlève les accents et les caractères spéciaux
			$word = preg_replace('#[^a-z0-9_-]#', '', normalize($word, true));
			if($word !== '')
			{
				// On enregistre le mot-clé en base de données s'il n'y est pas encore
				$keyWord = KeyWord::firstOrCreate(array(
					'word' => $word
				));
				// On récupère sont ID
				$ids[] = $keyWord->id;
			}
		}
		// On enregistre les IDs des mots-clés dans la table d'association
		$contentCrawled->keyWords()->sync($ids);
	}

}

?>