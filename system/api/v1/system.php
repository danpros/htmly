<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Handle GET /api/v1/system/health
 */
function api_get_system_health()
{
    $posts = get_all_posts();
    $drafts = get_draft_posts();
    $pages = get_static_pages();

    $cacheDir = 'cache/';
    $cacheSize = 0;
    if (is_dir($cacheDir)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir)) as $file) {
            if ($file->isFile()) {
                $cacheSize += $file->getSize();
            }
        }
    }

    api_response(array(
        'success' => true,
        'system' => array(
            'status' => 'healthy',
            'htmly_version' => HTMLY_VERSION ?? '3.1.1',
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'disk_free_space_mb' => round(disk_free_space('.') / (1024 * 1024), 2)
        ),
        'statistics' => array(
            'total_published_posts' => count($posts),
            'total_drafts' => count($drafts),
            'total_pages' => count($pages),
            'cache_size_kb' => round($cacheSize / 1024, 2)
        ),
        'config' => array(
            'blog_title' => config('blog.title'),
            'blog_tagline' => config('blog.tagline'),
            'site_url' => site_url(),
            'language' => config('language')
        )
    ));
}
