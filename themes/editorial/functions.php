<?php if (!defined('HTMLY')) die('HTMLy');

/**
 * Editorial theme helper functions.
 * HTMLy auto-includes this file when it exists inside the active theme
 * folder. All function names are prefixed with "editorial_" so they never
 * collide with core or other themes/plugins.
 */

/**
 * Cache-busting asset version.
 * HTMLy caches whole rendered pages, so a stylesheet/script <link> URL
 * only changes when we deliberately change it. Using the file's own
 * modified time means visitors always get the current CSS/JS the moment
 * you update the theme, without ever needing to purge HTMLy's page cache
 * just to see a style change, and without hurting long-term browser
 * caching in the meantime.
 */
if (!function_exists('editorial_ver')) {
    function editorial_ver($relativePath)
    {
        $full = __DIR__ . '/' . ltrim($relativePath, '/');
        return is_file($full) ? substr((string) filemtime($full), -6) : '1';
    }
}

/**
 * Safe excerpt builder.
 * Falls back gracefully: explicit description -> teaser from body -> empty.
 */
if (!function_exists('editorial_excerpt')) {
    function editorial_excerpt($description, $body, $url, $length = 190)
    {
        $text = trim((string) $description);
        if ($text === '' && function_exists('get_teaser')) {
            $text = trim(strip_tags(get_teaser($body, $url)));
        }
        if ($text === '') {
            $text = trim(strip_tags((string) $body));
        }
        if (function_exists('mb_strlen') && mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '…';
        } elseif (strlen($text) > $length) {
            $text = substr($text, 0, $length) . '…';
        }
        return $text;
    }
}

/**
 * One or two-letter initials used for the CSS-only avatar badge,
 * so the theme never depends on an external Gravatar request.
 */
if (!function_exists('editorial_initials')) {
    function editorial_initials($name)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '?';
        }
        $parts = preg_split('/\s+/u', $name);
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $chr = function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
            $initials .= function_exists('mb_strtoupper') ? mb_strtoupper($chr) : strtoupper($chr);
        }
        return $initials !== '' ? $initials : '?';
    }
}

/**
 * Try to pull a usable thumbnail: the post's declared image first,
 * then the first image found inside the markdown body, else null so
 * the template can render the gradient monogram fallback instead.
 */
if (!function_exists('editorial_thumb')) {
    function editorial_thumb($post)
    {
        if (!empty($post->image)) {
            return $post->image;
        }
        if (function_exists('get_image')) {
            $img = get_image($post->body);
            if (!empty($img)) {
                return $img;
            }
        }
        return null;
    }
}

/**
 * Repair HTMLy core's breadcrumb output server-side, before it's ever
 * sent to the browser.
 *
 * Core renders the breadcrumb's separator ("»") and the current page's
 * own title as bare content sitting directly inside the <ol> — not
 * wrapped in <li>, which is invalid (a list may only directly contain
 * <li>, <script>, <template>). It also sometimes injects a <style>
 * block, which isn't valid inside <nav> either. The theme has no hook
 * into that core markup to fix it at the source, so this rebuilds a
 * valid version of whatever core handed us:
 *   - any <style> tag found gets removed (the theme owns styling)
 *   - any stray text/element sitting directly in a <ol>/<ul> gets
 *     merged into the <li> that follows it (so "» Blog" reads as one
 *     breadcrumb entry, not two), or wrapped in a new <li> of its own
 *     if there's no following <li> (the dangling final page title).
 *
 * Falls back to returning the original string untouched if the DOM
 * extension isn't available on this server, or if anything about the
 * input doesn't parse — this must never be able to break the page.
 */
if (!function_exists('editorial_fix_breadcrumb')) {
    function editorial_fix_breadcrumb($html)
    {
        if (trim((string) $html) === '' || !class_exists('DOMDocument')) {
            return $html;
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $ok = $doc->loadHTML(
            '<?xml encoding="UTF-8"?><div id="__editorial_wrap__">' . $html . '</div>',
            LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        if (!$ok) {
            return $html;
        }

        $xpath = new DOMXPath($doc);

        foreach (iterator_to_array($xpath->query('//style')) as $styleNode) {
            $styleNode->parentNode->removeChild($styleNode);
        }

        foreach (iterator_to_array($xpath->query('//ol | //ul')) as $list) {
            foreach (iterator_to_array($list->childNodes) as $child) {
                $isStrayText = $child->nodeType === XML_TEXT_NODE && trim($child->textContent) !== '';
                $isStrayElement = $child->nodeType === XML_ELEMENT_NODE
                    && !in_array(strtoupper($child->nodeName), array('LI', 'SCRIPT', 'TEMPLATE'), true);
                if (!$isStrayText && !$isStrayElement) {
                    continue;
                }

                $next = $child->nextSibling;
                while ($next && $next->nodeType === XML_TEXT_NODE && trim($next->textContent) === '') {
                    $next = $next->nextSibling;
                }

                if ($next && $next->nodeType === XML_ELEMENT_NODE && strtoupper($next->nodeName) === 'LI') {
                    $next->insertBefore($child, $next->firstChild);
                } else {
                    $li = $doc->createElement('li');
                    $list->insertBefore($li, $child);
                    $li->appendChild($child);
                }
            }
        }

        $wrap = $xpath->query('//div[@id="__editorial_wrap__"]')->item(0);
        if (!$wrap) {
            return $html;
        }
        $out = '';
        foreach ($wrap->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return $out;
    }
}

/**
 * Wraps HTMLy core's search() so calling it more than once per page
 * (header + sidebar widget, or sidebar + 404-search) doesn't produce
 * duplicate `id="search-form"` — core hardcodes that id on every form
 * it renders, and HTML requires ids to be unique per page. Only the
 * theme's own repeated calls are touched; the first call on any given
 * page is left exactly as core renders it.
 */
if (!function_exists('editorial_search')) {
    function editorial_search($label = null)
    {
        static $count = 0;
        $count++;
        $html = search($label);
        if ($count > 1 && is_string($html)) {
            $html = preg_replace('/\bid="search-form"/', 'id="search-form-' . $count . '"', $html, 1);
        }
        return $html;
    }
}

/**
 * Visibility toggles from theme.json are all named "hide_*" and default
 * to unchecked/empty — meaning "not hidden" — so a fresh install (or a
 * page visited before anyone has saved the theme's settings form even
 * once) looks exactly like it always has, rather than silently hiding
 * everything until every checkbox is manually re-ticked.
 */
if (!function_exists('editorial_visible')) {
    function editorial_visible($hideSettingName)
    {
        if (!function_exists('theme_config')) {
            return true;
        }
        $value = theme_config($hideSettingName);
        return empty($value) || $value === '0' || $value === 'false';
    }
}

/**
 * Returns a configured ad snippet, or '' if ads are off globally, the
 * specific slot has no code saved, or theme_config() isn't available
 * (older HTMLy without the 3.1.1 theme-settings feature).
 */
if (!function_exists('editorial_ad_code')) {
    function editorial_ad_code($settingName)
    {
        if (!function_exists('theme_config')) {
            return '';
        }
        if (!editorial_ad_master_enabled()) {
            return '';
        }
        return trim((string) theme_config($settingName));
    }
}

if (!function_exists('editorial_ad_master_enabled')) {
    function editorial_ad_master_enabled()
    {
        if (!function_exists('theme_config')) {
            return false;
        }
        $value = theme_config('ads_enabled');
        return !empty($value) && $value !== '0' && $value !== 'false';
    }
}

/**
 * Returns the configured, enabled sponsor slots (1-3) as an array of
 * ['name' => ..., 'logo' => ..., 'link' => ...], skipping any slot
 * that's disabled or missing its logo/link. Used by the sidebar so it
 * doesn't need to repeat the same three-slot boilerplate itself.
 */
if (!function_exists('editorial_sponsors')) {
    function editorial_sponsors()
    {
        if (!function_exists('theme_config')) {
            return array();
        }
        $slots = array();
        for ($i = 1; $i <= 3; $i++) {
            $enabled = theme_config('sponsor' . $i . '_enabled');
            if (empty($enabled) || $enabled === '0' || $enabled === 'false') {
                continue;
            }
            $logo = trim((string) theme_config('sponsor' . $i . '_logo'));
            $link = trim((string) theme_config('sponsor' . $i . '_link'));
            if ($logo === '' || $link === '') {
                continue;
            }
            $slots[] = array(
                'name' => trim((string) theme_config('sponsor' . $i . '_name')) ?: 'Sponsor',
                'logo' => $logo,
                'link' => $link,
            );
        }
        return $slots;
    }
}


