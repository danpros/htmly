<?php
// Virtual filesystem shim: backs content/config/user/data paths with SQLite,
// while cache/ and http(s):// paths always pass through to native file functions.

define('VFS_DB_FILE', 'config/htmly.sqlite3');

function _vfs_pdo()
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . VFS_DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE IF NOT EXISTS vfs (path TEXT PRIMARY KEY, content TEXT, mtime INTEGER)');
    }
    return $pdo;
}

// cache/ (render/widget/index caches) and remote URLs always stay on native disk/network.
function _vfs_is_native($path)
{
    return strpos($path, 'cache/') === 0 || preg_match('#^https?://#i', $path) === 1;
}

function vfs_get($path)
{
    if (_vfs_is_native($path)) {
        return file_exists($path) ? file_get_contents($path) : false;
    }
    $stmt = _vfs_pdo()->prepare('SELECT content FROM vfs WHERE path = ?');
    $stmt->execute(array($path));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        return $row['content'];
    }
    return file_exists($path) ? file_get_contents($path) : false;
}

function vfs_put($path, $content)
{
    if (_vfs_is_native($path)) {
        return file_put_contents($path, $content, LOCK_EX);
    }
    $stmt = _vfs_pdo()->prepare('REPLACE INTO vfs (path, content, mtime) VALUES (?, ?, ?)');
    return $stmt->execute(array($path, $content, time()));
}

function vfs_exists($path)
{
    if (_vfs_is_native($path)) {
        return file_exists($path);
    }
    $stmt = _vfs_pdo()->prepare('SELECT 1 FROM vfs WHERE path = ?');
    $stmt->execute(array($path));
    if ($stmt->fetchColumn() !== false) {
        return true;
    }
    return file_exists($path);
}

function vfs_delete($path)
{
    if (_vfs_is_native($path)) {
        return @unlink($path);
    }
    $stmt = _vfs_pdo()->prepare('DELETE FROM vfs WHERE path = ?');
    $stmt->execute(array($path));
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return file_exists($path) ? @unlink($path) : false;
}

function vfs_mtime($path)
{
    if (_vfs_is_native($path)) {
        return file_exists($path) ? filemtime($path) : false;
    }
    $stmt = _vfs_pdo()->prepare('SELECT mtime FROM vfs WHERE path = ?');
    $stmt->execute(array($path));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
        return (int) $row['mtime'];
    }
    return file_exists($path) ? filemtime($path) : false;
}

function vfs_rename($old, $new)
{
    if (_vfs_is_native($old)) {
        return rename($old, $new);
    }
    $pdo = _vfs_pdo();
    $stmt = $pdo->prepare('UPDATE vfs SET path = ? WHERE path = ?');
    $stmt->execute(array($new, $old));
    if ($stmt->rowCount() > 0) {
        return true;
    }
    // No exact-path row: treat $old as a directory prefix and rename everything under it.
    $select = $pdo->prepare('SELECT path FROM vfs WHERE path GLOB ?');
    $select->execute(array($old . '/*'));
    $paths = $select->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($paths)) {
        $update = $pdo->prepare('UPDATE vfs SET path = ? WHERE path = ?');
        foreach ($paths as $p) {
            $update->execute(array($new . substr($p, strlen($old)), $p));
        }
        return true;
    }
    return file_exists($old) ? rename($old, $new) : false;
}

function vfs_glob($pattern)
{
    if (_vfs_is_native($pattern)) {
        return glob($pattern);
    }
    $stmt = _vfs_pdo()->prepare('SELECT path FROM vfs WHERE path GLOB ? ORDER BY path');
    $stmt->execute(array($pattern));
    $vfsMatches = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $nativeMatches = glob($pattern);
    if ($nativeMatches === false) {
        $nativeMatches = array();
    }
    return array_values(array_unique(array_merge($vfsMatches, $nativeMatches)));
}

function vfs_is_dir($path)
{
    if (is_dir($path)) {
        return true;
    }
    $stmt = _vfs_pdo()->prepare('SELECT 1 FROM vfs WHERE path GLOB ? LIMIT 1');
    $stmt->execute(array(rtrim($path, '/') . '/*'));
    return $stmt->fetchColumn() !== false;
}

function vfs_parse_ini($path, $sections = false)
{
    $content = vfs_get($path);
    if ($content === false) {
        return false;
    }
    return parse_ini_string($content, $sections);
}

// Pure string-based traversal guard: realpath() can't be used since virtual
// content has no real file and would return empty, failing closed incorrectly.
function vfs_is_safe_content_path($path, $base = 'content')
{
    $path = str_replace('\\', '/', $path);
    $segments = explode('/', $path);
    $clean = array();
    foreach ($segments as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            return false;
        }
        $clean[] = $segment;
    }
    return !empty($clean) && $clean[0] === $base;
}
