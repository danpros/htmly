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
        echo '<a href="?update">Update Me</a>'; //only update if they klick this link   
    }
}
else
{
    echo '<p>uptodate :)</p>';
}