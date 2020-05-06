<?php

// This file passes the content of the Readme.md file in the same directory
// through the SmartyPants filter. You can adapt this sample code in any way
// you like.
//
// ! NOTE: This file requires Markdown to be available on the include path to
//         parse the readme file.

// Install PSR-0-compatible class autoloader
spl_autoload_register(function($class){
	require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});

// Get SmartyPants and Markdown classes
use \Michelf\SmartyPants;
use \Michelf\MarkdownExtra;

// Read file and pass content through the Markdown praser
$text = file_get_contents('Readme.md');
$html = MarkdownExtra::defaultTransform($text);
$html = SmartyPants::defaultTransform($html);

?>
<!DOCTYPE html>
<html>
    <head>
        <title>PHP Smartypants Lib - Readme</title>
    </head>
    <body>
		<?php
			# Put HTML content in the document
			echo $html;
		?>
    </body>
</html>
