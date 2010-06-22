<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(

	// The path (starting from DOCROOT) to where the Less files are
	'less_path' => 'assets/css/less',

	// The path (starting from DOCROOT) to where the CSS files are
	'css_path'  => 'assets/css',

	// Lock the CSS file and do not attempt to recompile (Use in production)
	'lock_css'  => FALSE,

	// Minify the CSS before saving to the CSS file
	'minify'    => FALSE,

);
