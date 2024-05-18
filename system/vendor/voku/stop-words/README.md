[![Build Status](https://travis-ci.org/voku/stop-words.svg?branch=master)](https://travis-ci.org/voku/stop-words)
[![Coverage Status](https://coveralls.io/repos/github/voku/stop-words/badge.svg?branch=master)](https://coveralls.io/github/voku/stop-words?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/stop-words/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/stop-words/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dabeb6d93ead41309e4bbf80c0ec984e)](https://www.codacy.com/app/voku/stop-words?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=voku/stop-words&amp;utm_campaign=Badge_Grade)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/316837f1-afb0-4ea5-938e-340527eeb4e6/mini.png)](https://insight.sensiolabs.com/projects/316837f1-afb0-4ea5-938e-340527eeb4e6)
[![Latest Stable Version](https://poser.pugx.org/voku/stop-words/v/stable)](https://packagist.org/packages/voku/stop-words) 
[![Total Downloads](https://poser.pugx.org/voku/stop-words/downloads)](https://packagist.org/packages/voku/stop-words) 
[![Latest Unstable Version](https://poser.pugx.org/voku/stop-words/v/unstable)](https://packagist.org/packages/voku/stop-words)
[![License](https://poser.pugx.org/voku/stop-words/license)](https://packagist.org/packages/voku/stop-words)

# Stop-Words

## Description

A collection of stop words stop words in various languages for e.g. search-functions.

* [Installation](#installation)
* [Usage](#usage)
* [History](#history)

## Installation

1. Install and use [composer](https://getcomposer.org/doc/00-intro.md) in your project.
2. Require this package via composer:

```sh
composer require voku/stop-words
```

## Usage

```php
$stopWords = new StopWords();
$stopWords->getStopWordsFromLanguage('de');
```

Available languages
-------------------
* Arabic (ar)
* Bulgarian (bg)
* Catalan (ca)
* Croatian (hr)
* Czech (cz)
* Danish (da)
* Dutch (nl)
* English (en)
* Esperanto (eo)
* Estonian (et)
* Finnish (fi)
* French (fr)
* Georgian (ka)
* German (de)
* Greek (el)
* Hindi (hi)
* Hungarian (hu)
* Indonesian (id)
* Italian (it)
* Latvian (lv)
* Lithuanian (lt)
* Norwegian (no)
* Polish (pl)
* Portuguese (pt)
* Romanian (ro)
* Russian (ru)
* Slovak (sk)
* Spanish (es)
* Swedish (sv)
* Turkish (tr)
* Ukrainian (uk)
* Vietnamese (vi)

## History
See [CHANGELOG](CHANGELOG.md) for the full history of changes.
