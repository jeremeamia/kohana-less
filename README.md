LESS for Kohana
===============

This module allows Kohana developers who like to use LESS syntax to easily
include the [lessphp library](http://github.com/leafo/lessphp) into their 
project and begin writing CSS with LESS right away. It is similar to the
[LESS module by mongeslani](http://github.com/mongeslani/kohana-less) but
simpler, and includes lessphp via a submodule.

## How to Use

The `LESS::compile()` function is all you need. It takes the name of a `.less`
file (which should be located in the path you specify in the config file) and
returns the path to the `.css` file compiled from LESS. For example:

	<head>
		<title>LESS Test</title>
		<?php echo HTML::style(LESS::compile('test')) ?>
	</head>

Not too hard, right?

You can also configure the compiler to do some basic CSS minification as well,
but this is turned off by default.