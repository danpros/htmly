<?php
// One-time cleanup for artifacts made obsolete by the SQLite vfs migration.
// Safe by default (dry-run); pass --purge --yes to actually delete anything.
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

chdir(__DIR__);

$argv = $_SERVER['argv'];
$purge = in_array('--purge', $argv, true);
$confirmed = in_array('--yes', $argv, true);

// --- Step 1: legacy on-disk index caches. These are never read or written to
// anymore (get_blog_posts() etc. now query vfs_glob() directly), so they are
// always safe to remove regardless of --purge.
$legacyIndexFiles = glob('cache/index/*.txt', GLOB_NOSORT) ?: array();
foreach ($legacyIndexFiles as $file) {
    unlink($file);
    echo "Removed legacy index cache: {$file}\n";
}
if (is_dir('cache/index') && count(scandir('cache/index')) === 2) {
    rmdir('cache/index');
    echo "Removed empty directory: cache/index\n";
}

// --- Step 2: original content/config/user/data files that have already been
// migrated into the vfs SQLite table are now redundant. Only remove them when
// explicitly requested, since this is destructive and hard to reverse.
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

$redundant = array();
foreach ($patterns as $pattern) {
    foreach (glob($pattern, GLOB_NOSORT) as $file) {
        if (!is_file($file)) {
            continue;
        }
        $stmt = _vfs_pdo()->prepare('SELECT content FROM vfs WHERE path = ?');
        $stmt->execute(array($file));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false && $row['content'] === file_get_contents($file)) {
            $redundant[] = $file;
        }
    }
}

if (empty($redundant)) {
    echo "No redundant originals found.\n";
    exit;
}

if (!$purge) {
    echo count($redundant) . " file(s) already migrated into the vfs table and safe to remove:\n";
    foreach ($redundant as $file) {
        echo "  {$file}\n";
    }
    echo "Re-run with --purge --yes to delete these originals from disk.\n";
    exit;
}

if (!$confirmed) {
    die("Refusing to delete without --yes. Re-run with --purge --yes to confirm.\n");
}

foreach ($redundant as $file) {
    unlink($file);
    echo "Deleted original: {$file}\n";
}
echo "Done. " . count($redundant) . " redundant original file(s) removed.\n";
