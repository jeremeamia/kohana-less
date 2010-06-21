<?php defined('SYSPATH') or die('No direct access allowed.');

class LESS {

	public static function compile($filename)
	{
		$config = Kohana::config('less');

		$source = rtrim($config->less_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename.'.less';
		$target = rtrim($config->css_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename.'.css';

		if ( ! $config->lock_css AND LESS::modified($source, $target))
		{
			echo 'Recompiling!';

			$less = new lessc($source);
			$css  = $less->parse();

			if ($config->minify)
			{
				$css = LESS::minify($css);
			}

			touch($source);
			file_put_contents($target, $css);
		}

		return $target;
	}

	public static function minify($css)
	{
		$replacements = array
		(
			'; ' => ';',
			': ' => ':',
			' {' => '{',
			'{ ' => '{',
			', ' => ',',
			'} ' => '}',
			';}' => '}',
		);

		$css = preg_replace('#\s+#', ' ', trim($css));
		$css = preg_replace('#/\*.*?\*/#s', '', $css);
		$css = str_replace(array_keys($replacements), array_values($replacements), $css);

		return $css;
	}

	protected static function modified($source, $target)
	{
		$source_modified = filemtime(realpath(DOCROOT.$source));
		$target_modified = filemtime(realpath(DOCROOT.$target));

		return abs($source_modified - $target_modified) > 30;
	}

}
