PHP SmartyPants
===============

PHP SmartyPants Lib 1.8.1 - 12 Dec 2016

by Michel Fortin  
<https://michelf.ca/>

Original SmartyPants by John Gruber  
<https://daringfireball.net/>


Introduction
------------

This is a library package that includes the PHP SmartyPants and its
sibling PHP SmartyPants Typographer with additional features.

SmartyPants is a free web typography prettifyier tool for web writers. It
easily translates plain ASCII punctuation characters into "smart" typographic 
punctuation HTML entities.

PHP SmartyPants is a port to PHP of the original SmartyPants written 
in Perl by John Gruber.

SmartyPants can perform the following transformations:

*   Straight quotes (`"` and `'`) into “curly” quote HTML entities
*   Backtick-style quotes (` ``like this'' `) into “curly” quote HTML
    entities
*   Dashes (`--` and `---`) into en- and em-dash entities
*   Three consecutive dots (`...`) into an ellipsis entity

SmartyPants Typographer can perform additional transformations:

*	French guillemets done using (`<<` and `>>`) into true « guillemets »
	HTML entities.
*	Comma-style quotes (` ,,like this`` ` or ` ''like this,, `) into their 
	curly equivalent.
*	Replace existing spaces with non-break spaces around punctuation marks 
	where appropriate, can also add or remove them if configured to.
*	Replace existing spaces with non-break spaces for spaces used as 
	a thousand separator and between a number and the unit symbol that 
	follows it (for most common units).

This means you can write, edit, and save using plain old ASCII straight 
quotes, plain dashes, and plain dots, but your published posts (and 
final HTML output) will appear with smart quotes, em-dashes, proper
ellipses, and proper no-break spaces (with Typographer).

SmartyPants does not modify characters within `<pre>`, `<code>`,
`<kbd>`, or `<script>` tag blocks. Typically, these tags are used to
display text where smart quotes and other "smart punctuation" would not
be appropriate, such as source code or example markup.


### Backslash Escapes ###

If you need to use literal straight quotes (or plain hyphens and
periods), SmartyPants accepts the following backslash escape sequences
to force non-smart punctuation. It does so by transforming the escape
sequence into a decimal-encoded HTML entity:


    Escape  Value  Character
    ------  -----  ---------
      \\    &#92;    \
      \"    &#34;    "
      \'    &#39;    '
      \.    &#46;    .
      \-    &#45;    -
      \`    &#96;    `


This is useful, for example, when you want to use straight quotes as
foot and inch marks:

    6\'2\" tall

translates into:

    6&#39;2&#34; tall

in SmartyPants's HTML output. Which, when rendered by a web browser,
looks like:

    6'2" tall


Requirements
------------

This library package requires PHP 5.3 or later.

Note: The older plugin/library hybrid package for PHP SmartyPants and
PHP SmartyPants Typographer is still will work with PHP 4.0.5 and later.


Usage
-----

This library package is meant to be used with class autoloading. For autoloading 
to work, your project needs have setup a PSR-0-compatible autoloader. See the 
included Readme.php file for a minimal autoloader setup. (If you don't want to 
use autoloading you can do a classic `require_once` to manually include the 
files prior use instead.)

With class autoloading in place, putting the 'Michelf' folder in your 
include path should be enough for this to work:

	use \Michelf\SmartyPants;
	$html_output = SmartyPants::defaultTransform($html_input);

SmartyPants Typographer is also available the same way:

	use \Michelf\SmartyPantsTypographer;
	$html_output = SmartyPantsTypographer::defaultTransform($html_input);

If you are using PHP SmartyPants with another text filter function that 
generates HTML such as Markdown, you should filter the text *after* the 
the HTML-generating filter. This is an example with [PHP Markdown][pmd]:

	use \Michelf\Markdown, \Michelf\SmartyPants;
	$my_html = Markdown::defaultTransform($my_text);
	$my_html = SmartyPants::defaultTransform($my_html);

To learn more about configuration options, see the full list of
[configuration variables].

 [configuration variables]: https://michelf.ca/projects/php-smartypants/configuration/
 [pmd]: https://michelf.ca/projects/php-markdown/


### Usage Without an Autoloader ###

If you cannot use class autoloading, you can still use include or require to 
access the parser. To load the \Michelf\SmartyPants parser, do it this way:

	require_once 'Michelf/SmartyPants.inc.php';
	
Or, if you need the \Michelf\SmartyPantsTypographer parser:

	require_once 'Michelf/SmartyPantsTypographer.inc.php';

While the plain `.php` files depend on autoloading to work correctly, using the 
`.inc.php` files instead will eagerly load the dependencies that would be loaded 
on demand if you were using autoloading.


Algorithmic Shortcomings
------------------------

One situation in which quotes will get curled the wrong way is when
apostrophes are used at the start of leading contractions. For example:

    'Twas the night before Christmas.

In the case above, SmartyPants will turn the apostrophe into an opening
single-quote, when in fact it should be a closing one. I don't think
this problem can be solved in the general case -- every word processor
I've tried gets this wrong as well. In such cases, it's best to use the
proper HTML entity for closing single-quotes (`&#8217;` or `&rsquo;`) by
hand.


Bugs
----

To file bug reports or feature requests (other than topics listed in the
Caveats section above) please send email to:

<michel.fortin@michelf.ca>

If the bug involves quotes being curled the wrong way, please send
example text to illustrate.


Version History
---------------

PHP SmartyPants Lib 1.8.1 (12 Dec 2016)

*	Fixed an issue introduced in 1.8.0 where backtick quotes were broken.


PHP SmartyPants Lib 1.8.0 (13 Nov 2016)

*	Can now set replacement characters for all transformations using 
	configuration variables, including ellipses and dashes.

*	Relocated replacement quotes configuration variables from
	`SmartyPantsTyppographer` to `SmartyPants`. Also relocated
	`decodeEntitiesInConfiguration()` to follow the configuration variables.

*	Added conversion of apostrophe and double quote to Hebrew Geresh 
	and Gershayim when the apostrophe or double quote is surrounded on
	both sides by a Hebrew character. For instance:

		input:  צה"ל / צ'ארלס
		output: צה״ל / צ׳ארלס

	You can still put quotes around Hebrew words and they'll become curled 
	quotation marks (if that is enabled). This new transform only applies 
	in the middle of a word, and only to words in Hebrew.


PHP SmartyPants Lib 1.7.1 (16 Oct 2016)

*	Fixing bug where `decodeEntitiesInConfiguration()` would cause the 
	configuration to set the space for units to an empty string.


PHP SmartyPants Lib 1.7.0 (15 Oct 2016)

*	Made `public` some configuration variables that were documented
	were documented as `public` but were actually `protected`.

*	Added the `decodeEntitiesInConfiguration()` method on 
	`SmartyPantsTypographer` to quickly convert HTML entities in configuration 
	variables to their corresponding UTF-8 character.


PHP SmartyPants Lib 1.6.0 (10 Oct 2016)

This is the first release of PHP SmartyPants Lib. This package requires PHP
version 5.3 or later and is designed to work with PSR-0 autoloading and,
optionally with Composer. Here is a list of the changes since
PHP SmartyPants 1.5.1f:

*	Plugin interface for Wordpress and Smarty is no longer present in
	the Lib package. The classic package is still available if you need it:
	<https://michelf.ca/projects/php-markdown/classic/>

*	SmartyPants parser is now encapsulated in its own class, with methods and
	configuration variables `public` and `protected` protection attributes.
	This has been available in unreleased versions since a few years, but now 
	it's official.

*	SmartyPants now works great with PSR-0 autoloading and Composer. If
	however you prefer to more directly `require_once` the files, the
	".inc.php" variants of the file will make sure everything is included.

*	For those of you who cannot use class autoloading, you can now
	include `Michelf/SmartyPants.inc.php` or
	`Michelf/SmartyPantsTypographer.inc.php` (note the `.inc.php` extension)
	to automatically include other files required by the parser.
