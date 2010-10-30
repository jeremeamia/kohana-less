LESS for Kohana
===============

This module allows Kohana developers who like to use LESS syntax to easily
include the [lessphp library](http://github.com/leafo/lessphp) into their 
project and begin writing CSS with LESS right away. It is similar to the
[LESS module by mongeslani](http://github.com/mongeslani/kohana-less) but
simpler, and includes lessphp via a submodule.

## Note

I use SASS now, so I don't plan on updating this module anymore. You should 
fork and maintain your own copy if you want to continue using this.

## How to Use

The `LESS::compile()` function is all you need. It takes the name of a `.less`
file (which should be located in the path you specify in the config file) and
returns the path to the `.css` file compiled from LESS. For example:

	<head>
		<title>LESS Test</title>
		<?php echo HTML::style(LESS::compile('test')) ?>
	</head>

Not too hard, right? The LESS files are only recompiled if their last modified
times are more recent the the last modified time of the CSS file that was 
generated. You can also pass an array of files to the compile method, and it 
combine them together. You can use the second parameter to name the CSS file:

	<head>
		<title>LESS Test</title>
		<?php echo HTML::style(LESS::compile(array('colors', 'fonts', 'layout'), 'styles')) ?>
	</head>

Ordering is important in LESS, so you will want to load any files with 
variables first.

You can also configure the compiler to do some basic CSS minification as well,
but this is turned off by default.