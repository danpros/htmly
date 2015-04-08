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
