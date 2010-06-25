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
	 * @param   mixed   The name(s) of the `.less` files (no path or extension required)
	 * @param   string  The desired name of the target `.css` file
	 * @return  string  The full path to the compiled `.css` file
	 */
	public static function compile($source_names, $target = NULL)
	{
		// Get the config items for this module
		$config = Kohana::config('less');

		// Prepare sources and targets
		$source_names = (array) $source_names;
		$target = ($target === NULL) ? $source_names[0] : $target;

		// Get the relative paths to the files
		$target = rtrim($config->css_path, DIRECTORY_SEPARATOR.'/').'/'.$target.'.css';

		$sources = array();
		foreach ($source_names as $source)
		{
			$sources[] = rtrim($config->less_path, DIRECTORY_SEPARATOR.'/').'/'.$source.'.less';
		}

		// If we need to compile again, then let's compile!
		if ( ! $config->lock_css AND LESS::need_to_compile($sources, $target))
		{
			// Combine sources into one LESS string
			$less = '';
			foreach ($sources as $source)
			{
				// Get LESS content
				$less .= file_get_contents($source)."\n\n";
			}

			// Instantiate LESS compiler
			$compiler = new lessc();

			// Importing should be done by the module so that modified times can be compared
			$compiler->importDisabled = TRUE;

			// Parse the LESS file and convert to CSS
			$css = $compiler->parse($less);

			// Minify the CSS if configured to do so
			if ($config->minify)
			{
				$css = LESS::minify($css);
			}

			// Write the CSS to the target file
			file_put_contents($target, $css);
		}

		// Return the path of the target for use in `HTML::style()`
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

		// Remove any unncessary whitespace or punctuation
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
	 * @param   array    Paths to the source `.less` files
	 * @param   string   Path to the target `.css` file
	 * @return  boolean  Whether or not the source files needs to be compiled
	 */
	protected static function need_to_compile(array $sources, $target)
	{
		// Get the last modified time for the target file (if it exists)
		$target_modified = @filemtime($target);

		// If target doesn't exist, we know we need to compile
		$need_to_compile = $target_modified === FALSE;

		// Check the source files modified times
		foreach ($sources as $source)
		{
			// If we already know we need to compile, exit the loop
			if ($need_to_compile)
				break;

			// Get the last modified time for the source file
			$source_modified = @filemtime($source);
			if ($source_modified === FALSE)
				throw new Kohana_Exception('The "last modified time" of the LESS file, :file, could not be determined. It may not exist.',
					array(':file' => $source));

			// Check if the source file is newer than the target
			$need_to_compile = $source_modified > $target_modified;
		}

		return $need_to_compile;
	}

}
