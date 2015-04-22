#HubUpdater
![https://github.com/Kanti/hub-updater/releases/latest](https://img.shields.io/github/release/kanti/hub-updater.svg?style=flat-square) ![https://packagist.org/packages/kanti/hub-updater](https://img.shields.io/packagist/dt/kanti/hub-updater.svg?style=flat-square)

Simple Github Updater for Web Projects [PHP]

## is HubUpdater for me? [Checklist]

- [ ] I have an little product/projekt on github. (~ <=30MB)
- [ ] it can run PHP and uses [composer](https://getcomposer.org/)s autoloader
- [ ] I want my users to update my Product with one click

## check for an update [simple]

```php
<?php
require 'vendor/autoload.php';

$updater = new \Kanti\HubUpdater('kanti/test');
$updater->update();
```

## how to provide an update?

- Go to your Repository on github.com &#x2023;&#x2023;
- click on the ``releases`` tab &#x2023;&#x2023;
- click on ``Draft a new release`` &#x2023;&#x2023;
- Enter your release details &#x2023;&#x2023;
- click on ``Publish release`` &#x2023;&#x2023;
- now you can use HubUpdater to update to the newest version. 
- _note: <sub>The timestamp of the release is used. NOT the version number!!</sub>_


## install via composer

The recommended way to install hub-updater is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of HubUpdater:

```bash
composer require kanti/hub-updater ~0.3
```

After installing, you need to require Composer's autoloader:

```php
<?php
require 'vendor/autoload.php';
```


## settings
```php
$settings = array(
	"settingsKey" => 'value',
);
new \Kanti\HubUpdater($settings);
```
|setting|description|default|
|---|---|---|
|name|the name your Repository has |**must be set**|
|branch|the branch you like to watch. |``master``|
|cache|the directory you like to put the cache stuff |``./cache/``|
|save|the directory you like to put the content of the zip |``./``|
|prerelease|would you like to download the prereleases? |``false``|
|cacheFile|name of the InformationCacheFile(in cacheDir)|``downloadInfo.json``|
|holdTime|time(seconds) the Cached-Information will be used|``43200``|
|versionFile|name of the InstalledVersionInformation is safed(in cacheDir)|``installedVersion.json``|
|zipFile|name of the temporary zip file(in cacheDir)|``tmpZipFile.zip``|
|updateignore|name of the updateignore file(in root of project)|``.updateignore``|
|exceptions|if true, will ``throw new \Exception`` on failure|``false``|

## Check for an update [complete]
```php
<?php
require 'vendor/autoload.php';

$updater = new \Kanti\HubUpdater(array(
    "cacheFile" => "downloadInfo.json",//name of the InformationCacheFile(in cacheDir)
    "holdTime" => 43200,//time(seconds) the Cached-Information will be used

    "versionFile" => "installedVersion.json",//name of the InstalledVersionInformation is safed(in cacheDir)
    "zipFile" => "tmpZipFile.zip",//name of the temporary zip file(in cacheDir)
    "updateignore" => ".updateignore",//name of the updateignore file(in root of project)

    "name" => 'kanti/test',//Repository to watch
    "branch" => 'master',//wich branch to watch
    "cache" => 'cache/',//were to put the caching stuff
    "save" => 'save/',//there to put the downloaded Version[default ./]
    "prerelease" => true,//accept prereleases?

    "exceptions" => true,//if true, will throw new \Exception on failure
));
if ($updater->able()) {
    if (isset($_GET['update'])) {
        $updater->update();
        echo '<p>updated :)</p>';
    } else {
        echo '<a href="?update">Update Me</a>'; //only update if they klick this link
    }
} else {
    echo '<p>uptodate :)</p>';
}

```
## the .updateignore file
### syntax:
put a file in one line and it will not be updated. _note <sub>put the .updateignore in your projects root directory</sub>_
```
.htaccess
favicon.ico
there/the/config.ini/is.ini
```


## Thanks:
- ca_bundle.crt form [bagder/ca-bundle](https://github.com/bagder/ca-bundle)
