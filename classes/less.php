<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * This module allows easy use of the lessphp library in Kohana 3
 *
 * @package  LESS
 * @author   Jeremy Lindblom
 */
class LESS {

	/**
	 * Compiles a `.less` file to a .css and returns the path to the compiled file.
	 *
	 * @param   string  The *name* of the `.less` file (no path or extension)
	 * @return  string  The full path to the compiled `.css` file
	 */
	public static function compile($filename)
	{
		// Get the config items for this module
		$config = Kohana::config('less');

		// Get the relative paths to the files
		$source = rtrim($config->less_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename.'.less';
		$target = rtrim($config->css_path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename.'.css';

		// If we need to compile again, then let's compile!
		if ( ! $config->lock_css AND LESS::need_to_compile($source, $target))
		{
			$less = new lessc($source);
			$css  = $less->parse();

			if ($config->minify)
			{
				$css = LESS::minify($css);
			}

			file_put_contents($target, $css);
		}

		return $target;
	}

	/**
	 * Minifies CSS using simple techniques
	 *
	 * @param   string  The css that needs to be minified
	 * @return  string  The minified version of the css
	 */
	public static function minify($css)
	{
		// Remove whitespace from ends
		$css = trim($css);

		// Remove any consecutive whitespace
		$css = preg_replace('#\s+#', ' ', $css);

		// Remove any CSS comments
		$css = preg_replace('#/\*.*?\*/#s', '', $css);

		// Remove any unncessary whitespace
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
		$css = str_replace(array_keys($replacements), array_values($replacements), $css);

		return $css;
	}

	/**
	 * Check if the `.less` file needs to be compiled, based on file
	 * modification times. Yes, I am using the `@` operator on `filemtime()`.
	 * I want it to return `FALSE` if it fails without sending an `E_WARNING`.
	 *
	 * @param   string   Path to the source `.less` file
	 * @param   string   Path to the target `.css` file
	 * @return  boolean  Whether or not the source file needs to be compiled
	 */
	protected static function need_to_compile($source, $target)
	{
		// Get the last modified time for the source file
		$source_modified = @filemtime($source);
		if ($source_modified === FALSE)
			throw new Kohana_Exception('The "last modified time" of the LESS file, :file, could not be determined. It may not exist.',
				array(':file' => $source));

		// Get the last modified time for the target file (if it exists)
		$target_modified = @filemtime($target);

		return ($target_modified === FALSE OR $source_modified > $target_modified);
	}

}
