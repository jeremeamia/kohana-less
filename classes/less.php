<?php defined('SYSPATH') or die('No direct access allowed.');

class LESS {

	public static function compile($filename)
	{

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

	protected static function modified($filename)
	{

	}

}
