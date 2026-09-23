<?php
// One-time idempotent CLI migration: copy existing content/config/user/data
// files into the vfs SQLite table, without deleting the originals.
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

chdir(__DIR__);

require __DIR__ . '/system/includes/db.php';

$patterns = array(
    'content/*/blog/*/*/*.md',
    'content/*/*/*/*/scheduled/*.md',
    'content/*/*/*/draft/*.md',
    'content/static/*.md',
    'content/static/*/*.md',
    'content/static/draft/*.md',
    'content/static/*/draft/*.md',
    'content/*/author.md',
    'content/data/category/*.md',
    'content/data/frontpage/*.md',
    'content/data/field/*.json',
    'content/data/*.json',
    'content/data/*.lang',
    'config/*.ini',
    'config/themes/*.ini',
    'config/users/*.ini',
);

$count = 0;
foreach ($patterns as $pattern) {
    foreach (glob($pattern, GLOB_NOSORT) as $file) {
        if (!is_file($file)) {
            continue;
        }
        vfs_put($file, file_get_contents($file));
        $count++;
        echo "Migrated: {$file}\n";
    }
}

echo "Done. {$count} file(s) migrated into " . VFS_DB_FILE . "\n";
