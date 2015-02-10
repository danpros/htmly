<?php

// Return username.ini value
function user($key, $user = null)
{
    $value = 'config/users/' . $user . '.ini';
    static $_config = array();
    if (file_exists($value)) {
        $_config = parse_ini_file($value, true);
        if (!empty($_config[$key])) {
            return $_config[$key];
        }
    }
}

function create_user($userName, $password)
{
    $file = 'config/users/' . $userName . '.ini';
    if (file_exists($file)) {
        return false;
    } else {
        file_put_contents($file, "password = " . hash("sha512", $password) . "\n" .
            "encryption = sha512\n" .
            "role = user\n");
        return true;
    }
}

// Create a session
function session($user, $pass)
{
    $user_file = 'config/users/' . $user . '.ini';
    $user_enc = user('encryption', $user);
    $user_pass = user('password', $user);
    $password = (strlen($user_enc) > 0 && $user_enc !== 'clear' && $user_enc !== 'none') ? hash($user_enc, $pass) : $pass;

    if (file_exists($user_file)) {
        if ($password === $user_pass) {
            $_SESSION[config("site.url")]['user'] = $user;
            header('location: admin');
        } else {
            return $str = '<li>Your username and password mismatch.</li>';
        }
    } else {
        return $str = '<li>Username not found in our record.</li>';
    }
}

// Clean URLs
function remove_accent($str)
{
    $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    $cyr = array('ж', 'ч', 'щ', 'ш', 'ю', 'а', 'б', 'в', 'г', 'д', 'e', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'Ж', 'Ч', 'Щ', 'Ш', 'Ю', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
    $lat = array('zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');
    $a = array_merge($a, $cyr);
    $b = array_merge($b, $lat);
    return str_replace($a, $b, $str);
}

// Edit blog posts
function edit_post($title, $tag, $url, $content, $oldfile, $destination = null, $description = null, $date = null, $fi, $vid)
{
    $oldurl = explode('_', $oldfile);
    if ($date !== null) {
        $oldurl[0] = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/' . date('Y-m-d-h-i-s', strtotime($date));
    }

    $post_title = $title;
    $post_fi = $fi;
    $post_vid = str_replace(["http://", "https://", "www.", "youtube", ".com", "/watch?v=", "/embed/"], "", $vid);
    $post_tag = preg_replace(array('/[^a-zA-Z0-9,.\-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($tag));
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if ($description !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    $post_content = '<!--fi ' . $post_fi . ' fi-->' . "\n\n" . '<!--vid ' . $post_vid . ' vid-->' . "\n\n" . $post_content;

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $newfile = $oldurl[0] . '_' . $post_tag . '_' . $post_url . '.md';
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
        }

        $replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';
        $dt = str_replace($replaced, '', $oldurl[0]);
        $t = str_replace('-', '', $dt);
        $time = new DateTime($t);
        $timestamp = $time->format("Y-m-d");

        // The post date
        $postdate = strtotime($timestamp);

        // The post URL
        $posturl = site_url() . date('Y/m', $postdate) . '/' . $post_url;

        rebuilt_cache('all');
        clear_post_cache($dt, $post_tag, $post_url, $newfile);

        if ($destination == 'post') {
            header("Location: $posturl");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Edit static page
function edit_page($title, $url, $content, $oldfile, $destination = null, $description = null)
{
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));

    $post_title = $title;
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if ($description !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = '';
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $newfile = $dir . '/' . $post_url . '.md';
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
        }

        $posturl = site_url() . $post_url;

        rebuilt_cache('all');
        clear_page_cache($post_url);
        if ($destination == 'post') {
            header("Location: $posturl");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Add blog post
function add_post($title, $tag, $url, $content, $user, $description = null, $fi, $vid)
{

    $post_date = date('Y-m-d-H-i-s');
    $post_title = $title;
    $post_fi = $fi;
    $post_vid = str_replace(["http://", "https://", "www.", "youtube", ".com", "/watch?v=", "/embed/"], "", $vid);
    $post_tag = preg_replace(array('/[^a-zA-Z0-9,.\-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($tag));
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if ($description !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    $post_content = '<!--fi ' . $post_fi . ' fi-->' . "\n\n" . '<!--vid ' . $post_vid . ' vid-->' . "\n\n" . $post_content;

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
        $dir = 'content/' . $user . '/blog/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0777, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_post_cache($post_date, $post_tag, $post_url, $dir . $filename);
        $redirect = site_url() . 'admin/mine';
        header("Location: $redirect");
    }
}

// Add static page
function add_page($title, $url, $content, $description = null)
{

    $post_title = $title;
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if ($description !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $filename = $post_url . '.md';
        $dir = 'content/static/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0777, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin';
        header("Location: $redirect");
    }
}

// Add static sub page
function add_sub_page($title, $url, $content, $static, $description = null)
{

    $post_title = $title;
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if ($description !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $filename = $post_url . '.md';
        $dir = 'content/static/' . $static . '/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0777, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin';
        header("Location: $redirect");
    }
}

// Delete blog post
function delete_post($file, $destination)
{
    if (!login())
        return null;
    $deleted_content = $file;

    // Get cache file
    $arr = explode('_', $file);
    $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';
    $dt = str_replace($replaced, '', $arr[0]);
    clear_post_cache($dt, $arr[1], str_replace('.md', '', $arr[2]), $file);

    if (!empty($deleted_content)) {
        unlink($deleted_content);
        rebuilt_cache('all');
        if ($destination == 'post') {
            $redirect = site_url();
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Delete static page
function delete_page($file, $destination)
{
    if (!login())
        return null;
    $deleted_content = $file;

    if (!empty($menu)) {
        foreach (glob('cache/page/*.cache', GLOB_NOSORT) as $file) {
            unlink($file);
        }
    } else {
        $replaced = substr($file, 0, strrpos($file, '/')) . '/';
        $url = str_replace($replaced, '', $file);
        clear_page_cache($url);
    }

    if (!empty($deleted_content)) {
        unlink($deleted_content);
        rebuilt_cache('all');
        if ($destination == 'post') {
            $redirect = site_url();
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Edit user profile
function edit_profile($title, $content, $user)
{
    $user_title = $title;
    $user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;

    if (!empty($user_title) && !empty($user_content)) {
        if (get_magic_quotes_gpc()) {
            $user_content = stripslashes($user_content);
        }
        $dir = 'content/' . $user . '/';
        $filename = 'content/' . $user . '/author.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($user_content, true));
        } else {
            mkdir($dir, 0777, true);
            file_put_contents($filename, print_r($user_content, true));
        }
        rebuilt_cache('all');
        $redirect = site_url() . 'author/' . $user;
        header("Location: $redirect");
    }
}

// Import RSS feed
function migrate($title, $time, $tags, $content, $url, $user, $source)
{
    $post_date = date('Y-m-d-H-i-s', $time);
    $post_title = $title;
    $post_tag = preg_replace(array('/[^a-zA-Z0-9,.\-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($tags));
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if (!empty($source)) {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content . "\n\n" . 'Source: <a target="_blank" href="' . $source . '">' . $title . '</a>';
    } else {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
    }
    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
        if (get_magic_quotes_gpc()) {
            $post_content = stripslashes($post_content);
        }
        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
        $dir = 'content/' . $user . '/blog/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0777, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        $redirect = site_url() . 'admin/clear-cache';
        header("Location: $redirect");
    }
}

// Fetch RSS feed
function get_feed($feed_url, $credit)
{
    $source = file_get_contents($feed_url);
    $feed = new SimpleXmlElement($source);
    if (!empty($feed->channel->item)) {
        foreach ($feed->channel->item as $entry) {
            $descriptionA = $entry->children('content', true);
            $descriptionB = $entry->description;
            if (!empty($descriptionA)) {
                $content = $descriptionA;
            } elseif (!empty($descriptionB)) {
                $content = preg_replace('#<br\s*/?>#i', "\n", $descriptionB);
            } else {
                return $str = '<li>Can not read the feed content.</li>';
            }
            $time = new DateTime($entry->pubDate);
            $timestamp = $time->format("Y-m-d H:i:s");
            $time = strtotime($timestamp);
            $tags = strip_tags(preg_replace(array('/[^a-zA-Z0-9,.\-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($entry->category)));
            $title = rtrim($entry->title, ' \,\.\-');
            $title = ltrim($title, ' \,\.\-');
            $user = $_SESSION[config("site.url")]['user'];
            $url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($title)));
            if ($credit == 'yes') {
                $source = $entry->link;
            } else {
                $source = null;
            }
            migrate($title, $time, $tags, $content, $url, $user, $source);
        }
    } else {
        return $str = '<li>Unsupported feed.</li>';
    }
}

// Get recent posts by user
function get_recent_posts()
{
    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = get_profile($_SESSION[config("site.url")]['user'], 1, 5);
        if (!empty($posts)) {
            echo '<table class="post-list">';
            echo '<tr class="head"><th>Title</th><th>Published</th>';
            if (config("views.counter") == "true")
                echo '<th>Views</th>';
            echo '<th>Tag</th><th>Operations</th></tr>';
            $i = 0;
            $len = count($posts);
            foreach ($posts as $p) {
                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;
                echo '<tr class="' . $class . '">';
                echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
                echo '<td>' . date('d F Y', $p->date) . '</td>';
                if (config("views.counter") == "true")
                    echo '<td>' . $p->views . '</td>';
                echo '<td>' . $p->tag . '</td>';
                echo '<td><a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}

// Get all static pages
function get_recent_pages()
{
    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = get_static_post(null);
        if (!empty($posts)) {
            krsort($posts);
            echo '<table class="post-list">';
            echo '<tr class="head"><th>Title</th>';
            if (config("views.counter") == "true")
                echo '<th>Views</th>';
            echo '<th>Operations</th></tr>';
            $i = 0;
            $len = count($posts);
            foreach ($posts as $p) {
                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;

                echo '<tr class="' . $class . '">';
                echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
                if (config("views.counter") == "true")
                    echo '<td>' . $p->views . '</td>';
                echo '<td><a href="' . $p->url . '/add?destination=admin">Add Sub</a> <a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
                echo '</tr>';

                $shortUrl = substr($p->url, strrpos($p->url, "/") + 1);
                $subPages = get_static_sub_post($shortUrl, null);

                foreach ($subPages as $sp) {
                    echo '<tr class="' . $class . '">';
                    echo '<td> &raquo;<a target="_blank" href="' . $sp->url . '">' . $sp->title . '</a></td>';
                    if (config("views.counter") == "true")
                        echo '<td>' . $sp->views . '</td>';
                    echo '<td><a href="' . $sp->url . '/edit?destination=admin">Edit</a> <a href="' . $sp->url . '/delete?destination=admin">Delete</a></td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
    }
}

// Get all available zip files
function get_backup_files()
{
    if (isset($_SESSION[config("site.url")]['user'])) {
        $files = get_zip_files();
        if (!empty($files)) {
            krsort($files);
            echo '<table class="backup-list">';
            echo '<tr class="head"><th>Filename</th><th>Date</th><th>Operations</th></tr>';
            $i = 0;
            $len = count($files);
            foreach ($files as $file) {

                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;

                // Extract the date
                $arr = explode('_', $file);

                // Replaced string
                $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

                $name = str_replace($replaced, '', $file);

                $date = str_replace('.zip', '', $arr[1]);
                $t = str_replace('-', '', $date);
                $time = new DateTime($t);
                $timestamp = $time->format("D, d F Y, H:i:s");

                $url = site_url() . $file;
                echo '<tr class="' . $class . '">';
                echo '<td>' . $name . '</td>';
                echo '<td>' . $timestamp . '</td>';
                echo '<td><a target="_blank" href="' . $url . '">Download</a> <form method="GET"><input type="hidden" name="file" value="' . $file . '"/><input type="submit" name="submit" value="Delete"/></form></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'No available backup!';
        }
    }
}

function clear_post_cache($post_date, $post_tag, $post_url, $filename)
{
    $b = str_replace('/', '#', site_path() . '/');
    $t = explode('-', $post_date);
    $c = explode(',', $post_tag);
    $p = 'cache/page/' . $b . $t[0] . '#' . $t[1] . '#' . $post_url . '.cache';

    // Delete post
    if (file_exists($p)) {
        unlink($p);
    }

    // Delete homepage
    $yd = 'cache/page/' . $b . '.cache';
    if (file_exists($yd)) {
        unlink($yd);
    }
    foreach (glob('cache/page/' . $b . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Delete year
    $yd = 'cache/page/' . $b . 'archive#' . $t[0] . '.cache';
    if (file_exists($yd)) {
        unlink($yd);
    }
    foreach (glob('cache/page/' . $b . 'archive#' . $t[0] . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Delete year-month
    $yd = 'cache/page/' . $b . 'archive#' . $t[0] . '-' . $t[1] . '.cache';
    if (file_exists($yd)) {
        unlink($yd);
    }
    foreach (glob('cache/page/' . $b . 'archive#' . $t[0] . '-' . $t[1] . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Delete year-month-day
    $yd = 'cache/page/' . $b . 'archive#' . $t[0] . '-' . $t[1] . '-' . $t[2] . '.cache';
    if (file_exists($yd)) {
        unlink($yd);
    }
    foreach (glob('cache/page/' . $b . 'archive#' . $t[0] . '-' . $t[1] . '-' . $t[2] . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Delete tag
    foreach ($c as $tag) {
        $yd = 'cache/page/' . $b . 'tag#' . $tag . '.cache';
        if (file_exists($yd)) {
            unlink($yd);
        }
        foreach (glob('cache/page/' . $b . 'tag#' . $tag . '~*.cache', GLOB_NOSORT) as $file) {
            unlink($file);
        }
    }

    // Delete search
    foreach (glob('cache/page/' . $b . 'search#*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }


    // Get cache post author
    $arr = explode('_', $filename);
    $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';
    $str = explode('/', $replaced);
    $author = $str[count($str) - 3];
    // Delete author post list cache
    $a = 'cache/page/' . $b . 'author#' . $author . '.cache';
    if (file_exists($a)) {
        unlink($a);
    }
    foreach (glob('cache/page/' . $b . 'author#' . $author . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }
}

function clear_page_cache($url)
{
    $b = str_replace('/', '#', site_path() . '/');
    $p = 'cache/page/' . $b . $url . '.cache';
    if (file_exists($p)) {
        unlink($p);
    }
}
