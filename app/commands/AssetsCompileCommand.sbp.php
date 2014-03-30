<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

AssetsCompileCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'asset:compile'

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Compile/Recompile all the assets.'

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct()

	/**
	 * Get list of assets from a directory recursively.
	 *
	 * @return array $files
	 */
	- files $assetsDirectory, $directory
		$files = array()
		foreach scandir($assetsDirectory . '/' . $directory) as $file
			if substr($file, 0, 1) !== '.'
				$path = $directory . '/' . $file
				if is_file($assetsDirectory . '/' . $path)
					$files[] = $path
				else
					array_merge(**$files, >files($assetsDirectory, $path))
		< $files

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire

		checkAssets(true)
		$assetsDirectory = app_path().'/assets'

		foreach array('image', 'script', 'style') as $asset
			$count = 0
			$plural = $asset . 's'
			foreach >files($assetsDirectory, $plural) as $file
				echo "     $file\n"
				$asset($file)
				$count++
			echo $count . " fichiers $plural copiés\n\n"