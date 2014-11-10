HubUpdater
==========

Simple Github Updater for Web Projects [PHP]

is HubUpdater for me? [Checklist]
----------
- [ ] I have an little product/projekt on github. (~ 0-30MB)
- [ ] it uses PHP
- [ ] I want my users to update my Product with one click

Check for an update [simple]
----------
```php
<?php
require 'vendor/autoload.php';

$updater = new \Kanti\HubUpdater('kanti/test');
$updater->update();
```

how to provide an update?
----------
- Go to your Repository->
- click on ``releases``->
- ``Draft a new release``->
- Enter your details->
- click on ``Publish release``


Installing via Composer
----------

The recommended way to install hub-updater is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Guzzle:

```bash
composer require kanti/hub-updater dev-master
```

After installing, you need to require Composer's autoloader:

```php
<?php
require 'vendor/autoload.php';
```


Settings
----------
```php
$settings = array(
	"settingsKey" => 'value',
);
new \Kanti\HubUpdater($settings);
```
|Setting|Description|default|
|---|---|---|
|name|the name your Repository has |must be set|
|branch|the branch you like to watch. |``master``|
|cache|the directory you like to put the cache stuff |``./cache/``|
|save|the directory you like to put the content of the zip. |``./``|
|prerelease|would you like to download the prereleases? |``false``|

Check for an update [complete]
----------
```php
<?php
require 'vendor/autoload.php';

$updater = new \Kanti\HubUpdater([
	"name" => 'kanti/test',//Repository to watch
	"branch" => 'master',//wich branch to watch
	"cache" => 'cache/',//were to put the caching stuff
	"save" => 'save/',//there to put the downloaded Version[default ./]
	"prerelease" => true,//accept prereleases?
]);
if($updater->able())
{
	if(isset($_GET['update']))
	{
		$updater->update();
		echo '<p>updated :)</p>';
	}
	else
	{
		echo '<a href="?update">Update Me</a>';	//only update if they klick this link	
	}
}
else
{
	echo '<p>uptodate :)</p>';
}
```
Thanks:
----------
- ca_bundle.crt form [bagder/ca-bundle](https://github.com/bagder/ca-bundle)
