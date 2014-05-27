<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Utils\Lang\CSV;

CsvCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'csv';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Import/export CSV language files.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct();


	+ getOptions
		< array(
			array('input', 'i', InputOption::VALUE_OPTIONAL, 'Input CSV file', null),
			array('output', 'o', InputOption::VALUE_OPTIONAL, 'Output CSV file', null),
			array('test', 't', InputOption::VALUE_NONE, 'Output CSV file', null),
			array('languages', 'l', InputOption::VALUE_OPTIONAL, 'Output CSV file', null),
		);


	+ input $input
		$simulation = >option('test')
		>msg("Import en cours...\n")
		$contents = file_get_contents($input)
		if substr_count($contents, "\t") / substr_count($contents, ";") > 10
			>msg("Conversion depuis un format Microsoft...\n")
			$stream = fopen($input, 'w')
			foreach preg_split("#(\r\n|\n|\r)#", trim($contents)) as $line
				CSV::put($stream, explode("\t", $line))
			fclose($stream)
		$stream = fopen($input, 'r')
		$headers = CSV::next($stream)
		if ! is_array($headers) || count($headers) < 3
			>msg("Entêtes du fichier importé manquantes ou partielles\n")
		else
			$encode = f° ($string)
				< $string;
			;
			if strpos(head($headers), "\xEF\xBB\xBF") === 0
				if ! CSV::BOM_UTF8
					$encode = 'utf8_decode';
			else
				if CSV::BOM_UTF8
					$encode = 'utf8_encode';
			if preg_match('#^(?:\xEF\xBB\xBF)?file$#', $headers[0]) && $headers[1] === 'key'
				$languages = array_slice($headers, 2)
				$csvFile = CSV::convert($languages)
				$diff = array()
				$files = array()
				$contents = array_map(f°
					< array()
				, array_combine($languages, $languages))
				$compare = fopen($csvFile, 'r')
				CSV::next($compare)
				$compactFields = array()
				$compactActual = array()
				while false !== ($fields = CSV::next($stream))
					$compactFields[$fields[0] . '.' . $fields[1]] = $fields
				while false !== ($actual = CSV::next($compare))
					$compactActual[$actual[0] . '.' . $actual[1]] = $actual
				$compactFields = array_merge($compactActual, $compactFields)
				$compactActual = array_merge($compactFields, $compactActual)
				fclose($compare)
				unlink($csvFile)
				foreach $compactFields as $key => $fields
					$fields = array_map($encode, $fields)
					$file = $fields[0]
					foreach $languages as $index => $language
						if ! isset($contents[$language][$file])
							$contents[$language][$file] = array()
						$contents[$language][$file][$fields[1]] = $fields[$index + 2]
					$actual = $compactActual[$key]
					if $fields !== $actual
						$diff[] = array($actual, $fields)
						if ! isset($files[$file])
							$files[$file] = array()
						foreach $languages as $index => $language
							if ! in_array($language, $files[$file]) && $fields[$index + 2] !== $actual[$index + 2]
								$files[$file][] = $language
				if $count = count($diff)
					>msg($count . " modifications :\n")
					foreach $diff as $data
						list($avant, $apres) = $data;
						>msg("   Avant : " . implode(', ', $avant) . "\n")
						>msg("   Apres : " . implode(', ', $apres) . "\n\n")
					>msg((count($files, true) - count($files)) . " fichiers à remplacer :\n")
					foreach $files as $file => $languages
						foreach $languages as $language
							>msg($language . '/' . $file . " : " . (
								$simulation || >putLangFile($language, $file, $contents[$language][$file]) ?
									"[OK]" :
									"/!\\ KO"
							) . "\n")
				else
					>msg("Aucune modification à importer (tous les fichiers sont à jour)\n")
			else
				>msg("Entêtes du fichier importé incorrectes (file ou key est absent)\n")
		< true


	+ output $output
		>msg("Export en cours...\n")
		$languages = >option('languages')
		if ! is_null($languages)
			$languages = array_map('trim', explode(',', $languages))
		$file = CSV::convert($languages)
		if ! is_null($output)
			>msg(rename($file, $output) ?
				"Export réussi\n" :
				"Export échoué\n")
		else
			>msg($file . "\n")


	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire

		try
			$input = >option('input')
			< is_null($input) ? >output(>option('output')) : >input($input)
		catch Exception $e
			echo $e->getFile() . ':' . $e->getLine() . "\n" . $e->getMessage() . "\n\n" . $e->getTraceAsString()