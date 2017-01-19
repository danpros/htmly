# \Suin\RSSWriter

`\Suin\RSSWriter` is yet another simple RSS writer library for PHP 5.3 or later. This component is Licensed under MIT license.

This library can also be used to publish Podcasts.

The build status of the current master branch is tracked by Travis CI: [![Build Status](https://secure.travis-ci.org/suin/php-rss-writer.png?branch=master)](http://travis-ci.org/suin/php-rss-writer)


Implementation:

```php
<?php
$feed = new Feed();

$channel = new Channel();
$channel
	->title("Channel Title")
	->description("Channel Description")
	->url('http://blog.example.com')
	->appendTo($feed);

// RSS item
$item = new Item();
$item
	->title("Blog Entry Title")
	->description("<div>Blog body</div>")
	->url('http://blog.example.com/2012/08/21/blog-entry/')
	->appendTo($channel);

// Podcast item
$item = new Item();
$item
	->title("Some Podcast Entry")
	->description("<div>Podcast body</div>")
	->url('http://podcast.example.com/2012/08/21/podcast-entry/')
    ->enclosure('http://link-to-audio-file.com/2013/08/21/podcast.mp3', 4889, 'audio/mpeg')
	->appendTo($channel);


echo $feed;
```

Output:

```xml
<?xml version="1.0"?>
<rss version="2.0">
  <channel>
    <title>Channel Title</title>
    <link>http://blog.example.com</link>
    <description>Channel Description</description>
    <item>
      <title>Blog Entry Title</title>
      <link>http://blog.example.com/2012/08/21/blog-entry/</link>
      <description>&lt;div&gt;Blog body&lt;/div&gt;</description>
    </item>
  </channel>
</rss>
```

## Installation

### Easy installation
You can install directly via [Composer](https://getcomposer.org/):
```bash
$ composer require suin/php-rss-writer
```

### Manual installation
Add the following code to your `composer.json` file:

```json
{
	"require": {
		"suin/php-rss-writer": ">=1.0"
	}
}
```

...and run composer to install it:
```bash
$ composer install
```

Finally, include `vendor/autoload.php` in your product:
```php
<?php
require_once 'vendor/autoload.php';
```

## How to use

The [`examples`](examples) directory contains usage examples for RSSWriter.

If you want to know APIs, please see [`FeedInterface`](src/Suin/RSSWriter/FeedInterface.php), [`ChannelInterface`](src/Suin/RSSWriter/ChannelInterface.php) and [`ItemInterface`](src/Suin/RSSWriter/ItemInterface.php).

## License

MIT license