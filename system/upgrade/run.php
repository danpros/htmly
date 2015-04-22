<?php

config('source', $config_file);

$updater = new Kanti\HubUpdater("danpros/htmly");
$info = $updater->getCurrentInfo();
$versionNumber = substr($info['tag_name'], 1);

function isGraterThan($string)
{
    global $versionNumber;
    return (version_compare($versionNumber, $string) > 0);
}

// http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    } else if (is_file($dir)) {
        unlink($dir);
    }
}

//run upgrade specific stuff
if (isGraterThan("2.3")) {// 2.4, 2.5, ...
    if (file_exists("vendor/")) {
        rrmdir("vendor/");
    }
}


if (!config("dev")) {
    file_put_contents("index.php", file_get_contents("system/upgrade/index.php"));
    rrmdir("system/upgrade/");
}
