# URLify for PHP

![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/jbroadway/urlify/Continuous%20Integration/master)
![Packagist License](https://img.shields.io/packagist/l/jbroadway/urlify)
![Packagist Version](https://img.shields.io/packagist/v/jbroadway/urlify)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/jbroadway/urlify)
![Packagist Downloads](https://img.shields.io/packagist/dt/jbroadway/urlify)

A fast PHP slug generator and transliteration library, started as a PHP port of
[URLify.js](https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js)
from the Django project.

Handles symbols from latin languages, Arabic, Azerbaijani, Bulgarian, Burmese, Croatian, Czech, Danish, Esperanto,
Estonian, Finnish, French, Switzerland (French), Austrian (French), Georgian, German, Switzerland (German),
Austrian (German), Greek, Hindi, Kazakh, Latvian, Lithuanian, Norwegian, Persian, Polish, Romanian, Russian, Swedish,
Serbian, Slovak, Turkish, Ukrainian and Vietnamese, and many other via `ASCII::to_transliterate()`.

Symbols it cannot transliterate it can omit or replace with a specified character.

## Installation

Install the latest version with:

```bash
$ composer require jbroadway/urlify
```

## Usage

First, include Composer's autoloader:

```php
require_once 'vendor/autoload.php';
```

To generate slugs for URLs:

```php
<?php

echo URLify::slug (' J\'étudie le français ');
// "jetudie-le-francais"

echo URLify::slug ('Lo siento, no hablo español.');
// "lo-siento-no-hablo-espanol"
```

To generate slugs for file names:

```php
<?php

echo URLify::filter ('фото.jpg', 60, "", true);
// "foto.jpg"
```

To simply transliterate characters:

```php
<?php

echo URLify::downcode ('J\'étudie le français');
// "J'etudie le francais"

echo URLify::downcode ('Lo siento, no hablo español.');
// "Lo siento, no hablo espanol."

/* Or use transliterate() alias: */

echo URLify::transliterate ('Lo siento, no hablo español.');
// "Lo siento, no hablo espanol."
```

To extend the character list:

```php
<?php

URLify::add_chars ([
	'¿' => '?', '®' => '(r)', '¼' => '1/4',
	'½' => '1/2', '¾' => '3/4', '¶' => 'P'
]);

echo URLify::downcode ('¿ ® ¼ ¼ ¾ ¶');
// "? (r) 1/2 1/2 3/4 P"
```

To extend the list of words to remove:

```php
<?php

URLify::remove_words (['remove', 'these', 'too']);
```

To prioritize a certain language map:

```php
<?php

echo URLify::filter ('Ägypten und Österreich besitzen wie üblich ein Übermaß an ähnlich öligen Attachés', 60, 'de');
// "aegypten-und-oesterreich-besitzen-wie-ueblich-ein-uebermass-aehnlich-oeligen-attaches"

echo URLify::filter ('Cağaloğlu, çalıştığı, müjde, lazım, mahkûm', 60, 'tr');
// "cagaloglu-calistigi-mujde-lazim-mahkum"
```

Please note that the "ü" is transliterated to "ue" in the first case, whereas it results in a simple "u" in the latter.
