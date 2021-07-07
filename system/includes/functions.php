<?php
if (!defined('HTMLY')) die('HTMLy');

use \Michelf\MarkdownExtra;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

// Get all authors
function get_authors()
{
    $tmp = array();
    foreach (glob('config/users/*.ini', GLOB_NOSORT) as $key => $value) {
        if(preg_match('/config\/users\/(.*)\.ini/i', $value, $matches)) {

            $user = new stdClass;
            $user->username = $matches[1];
            $user->password = user('password', $matches[1]);
            $user->role = user('role', $matches[1]);
            $user->url = site_url() . 'author/' . $matches[1];
            $user->file = $value;
            
	        $filename = 'content/' . $matches[1] . '/author.md';
	        if (file_exists($filename)) {
		        $content = file_get_contents($filename);
		        $user->title = get_content_tag('t', $content, 'user');
		        $user->content = remove_html_comments($content);
	        } else {
		        $user->title = $matches[1];
		        $user->content = 'Just another HTMLy user.';
	        }

            $tmp[] = $user;
        }
    }
    return $tmp;
}

// Get author info
function get_author_info($author)
{
    $tmp = array();
    $value = 'config/users/' . $author . '.ini';
    if(preg_match('/config\/users\/(.*)\.ini/i', $value, $matches)) {

        $user = new stdClass;
        $user->username = $matches[1];
        $user->password = user('password', $matches[1]);
        $user->role = user('role', $matches[1]);
        $user->url = site_url() . 'author/' . $matches[1];
        $user->file = $value;
        
        $filename = 'content/' . $matches[1] . '/author.md';
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $user->title = get_content_tag('t', $content, 'user');
            $user->content = remove_html_comments($content);
        } else {
            $user->title = $matches[1];
            $user->content = 'Just another HTMLy user.';
        }

        $tmp[] = $user;
    }
    return $tmp;
}

// Get blog post path. Unsorted. Mostly used on widget.
function get_post_unsorted()
{
    static $_unsorted = array();

    if (empty($_unsorted)) {

        $url = 'cache/index/index-unsorted.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_unsorted = unserialize(file_get_contents($url));
    }
    return $_unsorted;
}

// Get blog post with more info about the path. Sorted by filename.
function get_post_sorted()
{
    static $_sorted = array();

    if (empty($_sorted)) {
        $url = 'cache/index/index-sorted.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_sorted = unserialize(file_get_contents($url));
    }
    return $_sorted;
}

// Get static page path. Unsorted.
function get_static_pages()
{
    static $_page = array();

    if (empty($_page)) {
        $url = 'cache/index/index-page.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_page = unserialize(file_get_contents($url));
    }
    return $_page;
}

// Get static page path. Unsorted.
function get_static_sub_pages($static = null)
{
    static $_sub_page = array();

    if (empty($_sub_page)) {
        $url = 'cache/index/index-sub-page.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_sub_page = unserialize(file_get_contents($url));
    }
    if ($static != null) {
        $stringLen = strlen($static);
        return array_filter($_sub_page, function ($sub_page) use ($static, $stringLen) {
            $x = explode("/", $sub_page);
            if ($x[count($x) - 2] == $static) {
                return true;
            }
            return false;
        });
    }
    return $_sub_page;
}

// Get author name. Unsorted.
function get_author_name()
{
    static $_author = array();

    if (empty($_author)) {
        $url = 'cache/index/index-author.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_author = unserialize(file_get_contents($url));
    }

    return $_author;
}

// Get backup file.
function get_zip_files()
{
    static $_zip = array();

    if (empty($_zip)) {

        // Get the names of all the
        // zip files.

        $_zip = glob('backup/*.zip');
    }

    return $_zip;
}

// Get user draft.
function get_draft_posts()
{
    static $_draft = array();
    if (empty($_draft)) {
        $tmp = array();
        $tmp = glob('content/*/*/*/draft/*.md', GLOB_NOSORT);
        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                $_draft[] = pathinfo($file);
            }
        }
        usort($_draft, "sortfile");
    }
    return $_draft;
}

// Get category info files.
function get_category_files()
{
    static $_desc = array();
    if (empty($_desc)) {
        $url = 'cache/index/index-category.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_desc = unserialize(file_get_contents($url));
    }
    return $_desc;
}

// Get category folder.
function get_category_folder()
{
    static $_dfolder = array();
    if (empty($_dfolder)) {
        $tmp = array();
        $tmp = glob('content/*/blog/*/', GLOB_ONLYDIR);
        if (is_array($tmp)) {
            foreach ($tmp as $dir) {
                $_dfolder[] = $dir;
            }
        }
    }
    return $_dfolder;
}

// usort function. Sort by filename.
function sortfile($a, $b)
{
    return $a['basename'] == $b['basename'] ? 0 : (($a['basename'] < $b['basename']) ? 1 : -1);
}

// usort function. Sort by date.
function sortdate($a, $b)
{
    return $a->date == $b->date ? 0 : (($a->date < $b->date) ? 1 : -1);
}

// Rebuilt cache index
function rebuilt_cache($type)
{
    $dir = 'cache/index';
    $posts_cache_sorted = array();
    $posts_cache_unsorted = array();
    $page_cache = array();
    $author_cache = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if ($type === 'posts') {
        $tmpu = array();
        $tmpu = glob('content/*/blog/*/*/*.md', GLOB_NOSORT);
         if (is_array($tmpu)) {
            foreach ($tmpu as $fileu) {
                if(strpos($fileu, '/draft/') === false) {
                    $posts_cache_unsorted[] = $fileu;
                }
            }
        }
        $string = serialize($posts_cache_unsorted);
        file_put_contents('cache/index/index-unsorted.txt', print_r($string, true));

        $tmp = array();
        $tmp = glob('content/*/blog/*/*/*.md', GLOB_NOSORT);

        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                if(strpos($file, '/draft/') === false) {
                    $posts_cache_sorted[] = pathinfo($file);
                }
            }
        }
        usort($posts_cache_sorted, "sortfile");
        $string = serialize($posts_cache_sorted);
        file_put_contents('cache/index/index-sorted.txt', print_r($string, true));
    } elseif ($type === 'page') {
        $page_cache = glob('content/static/*.md', GLOB_NOSORT);
        $string = serialize($page_cache);
        file_put_contents('cache/index/index-page.txt', print_r($string, true));
    } elseif ($type === 'subpage') {
        $page_cache = glob('content/static/*/*.md', GLOB_NOSORT);
        $string = serialize($page_cache);
        file_put_contents('cache/index/index-sub-page.txt', print_r($string, true));
    } elseif ($type === 'author') {
        $author_cache = glob('content/*/author.md', GLOB_NOSORT);
        $string = serialize($author_cache);
        file_put_contents('cache/index/index-author.txt', print_r($string, true));
    } elseif ($type === 'category') {
        $category_cache = glob('content/data/category/*.md', GLOB_NOSORT);
        $string = serialize($category_cache);
        file_put_contents('cache/index/index-category.txt', print_r($string, true));
    } elseif ($type === 'all') {
        rebuilt_cache('posts');
        rebuilt_cache('page');
        rebuilt_cache('subpage');
        rebuilt_cache('author');
        rebuilt_cache('category');
    }

    foreach (glob('cache/widget/*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

}

// Return blog posts.
function get_posts($posts, $page = 1, $perpage = 0)
{
    if (empty($posts)) {
        $posts = get_post_sorted();
    }

    $tmp = array();

    // Extract a specific page with results
    $posts = array_slice($posts, ($page - 1) * $perpage, $perpage);

    $catC = category_list(true);

    foreach ($posts as $index => $v) {

        $post = new stdClass;

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $author = $str[count($str) - 5];
        if($str[count($str) - 3] == 'uncategorized') {
            $category = default_category();
            $post->category = '<a href="' . $category->url . '">' . $category->title . '</a>';
            $post->categoryUrl = $category->url;
            $post->categoryb = '<a itemprop="item" href="' . $category->url . '"><span itemprop="name">' . $category->title . '</span></a>';
        } else {

            foreach ($catC as $k => $v) {
                if ($v['0'] === $str[count($str) - 3]) {
                    $post->category = '<a href="' . site_url() . 'category/' . $v['0'] . '">' . $v['1'] . '</a>';
                    $post->categoryUrl = site_url() . 'category/' . $v['0'];
                    $post->categoryb = '<a itemprop="item" href="' . site_url() . 'category/' . $v['0'] . '"><span itemprop="name">' . $v['1'] . '</span></a>';
                }
            }

        }
        $type = $str[count($str) - 2];
        $post->ct = $str[count($str) - 3];

        // The post author + author url
        $post->author = $author;
        $post->authorUrl = site_url() . 'author/' . $author;
		
        $profile = get_author($author);
        if (isset($profile[0])) {
            $post->authorName = $profile[0]->name;
            $post->authorAbout = $profile[0]->about;
        } else {
            $post->authorName = $author;
            $post->authorAbout = 'Just another HTMLy user';
        }

        $post->type = $type;

        $dt = str_replace($replaced, '', $arr[0]);
        $t = str_replace('-', '', $dt);
        $time = new DateTime($t);
        $date = $time->format("Y-m-d H:i:s");

        // The post date
        $post->date = strtotime($date);

        // The archive per day
        $post->archive = site_url() . 'archive/' . date('Y-m', $post->date);

        if (config('permalink.type') == 'post') {
            $post->url = site_url() . 'post/' . str_replace('.md', '', $arr[2]);
        } else {
            $post->url = site_url() . date('Y/m', $post->date) . '/' . str_replace('.md', '', $arr[2]);
        }

        $post->file = $filepath;

        $content = file_get_contents($filepath);

        // Extract the title and body
        $post->title = get_content_tag('t', $content, 'Untitled: ' . date('l jS \of F Y', $post->date));
        $post->image = get_content_tag('image', $content);
        $post->video = get_content_tag('video', $content);
        $post->link  = get_content_tag('link', $content);
        $post->quote  = get_content_tag('quote', $content);
        $post->audio  = get_content_tag('audio', $content);

        $tag = array();
        $url = array();
        $bc = array();
        $rel = array();

        $tagt = get_content_tag('tag', $content);
        $t = explode(',', rtrim($arr[1], ','));

        if(!empty($tagt)) {
            $tl = explode(',', rtrim($tagt, ','));
            $tCom = array_combine($t, $tl);
            foreach ($tCom as $key => $val) {
                if(!empty($val)) {
                    $tag[] = array($val, site_url() . 'tag/' . strtolower($key));
                } else {
                    $tag[] = array($key, site_url() . 'tag/' . strtolower($key));
                }
            }
        } else {
            foreach ($t as $tt) {
                $tag[] = array($tt, site_url() . 'tag/' . strtolower($tt));
            }
        }

        foreach ($tag as $a) {
            $url[] = '<a rel="tag" href="' . $a[1] . '">' . $a[0] . '</a>';
            $bc[] = '<span><a href="' . $a[1] . '">' . $a[0] . '</a></span>';
        }

        $post->tag = implode(' ', $url);

        $post->tagb = implode(' » ', $bc);

        $post->related = rtrim($arr[1], ',');

        $more = explode('<!--more-->', $content);
        if (isset($more['1'])) {
            $content = $more['0']  . '<a id="more"></a><br>' . "\n\n" . '<!--more-->' . $more['1'];
        }

        // Get the contents and convert it to HTML
        $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

        // Convert image tags to figures
        if (config('fig.captions') == 'true') {
            $post->body = preg_replace( '/<p>(<img .*?alt="(.*?)"\s*\/>)<\/p>/', '<figure>$1<figcaption>$2</figcaption></figure>', $post->body );
        }

        if (config('views.counter') == 'true') {
            $post->views = get_views($post->file);
        } else {
            $post->views = null;
        }

        $post->description = get_content_tag("d", $content, get_description($post->body));
		
        $word_count = str_word_count(strip_tags($post->body));
        $post->readTime = ceil($word_count / 200);

        $tmp[] = $post;
    }

    return $tmp;
}

// Find post by year, month and name, previous, and next.
function find_post($year, $month, $name)
{
    $posts = get_post_sorted();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if ((strpos($arr[0], "$year-$month") !== false && strtolower($arr[2]) === strtolower($name . '.md')) || ($year === NULL && strtolower($arr[2]) === strtolower($name . '.md'))) {

            // Use the get_posts method to return
            // a properly parsed object

            $ar = get_posts($posts, $index + 1, 1);
            $nx = get_posts($posts, $index, 1);
            $pr = get_posts($posts, $index + 2, 1);

            if ($index == 0) {
                if (isset($pr[0])) {
                    return array(
                        'current' => $ar[0],
                        'prev' => $pr[0]
                    );
                } else {
                    return array(
                        'current' => $ar[0],
                        'prev' => null
                    );
                }
            } elseif (count($posts) == $index + 1) {
                return array(
                    'current' => $ar[0],
                    'next' => $nx[0]
                );
            } else {
                return array(
                    'current' => $ar[0],
                    'next' => $nx[0],
                    'prev' => $pr[0]
                );
            }
        }
    }
}

// Find draft.
function find_draft($year, $month, $name)
{
    $posts = get_draft_posts();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if (strpos($arr[0], "$year-$month") !== false && strtolower($arr[2]) === strtolower($name . '.md') || strtolower($arr[2]) === strtolower($name . '.md')) {

            // Use the get_posts method to return
            // a properly parsed object

            $ar = get_posts($posts, $index + 1, 1);
            $nx = get_posts($posts, $index, 1);
            $pr = get_posts($posts, $index + 2, 1);

            if ($index == 0) {
                if (isset($pr[0])) {
                    return array(
                        'current' => $ar[0],
                        'prev' => $pr[0]
                    );
                } else {
                    return array(
                        'current' => $ar[0],
                        'prev' => null
                    );
                }
            } elseif (count($posts) == $index + 1) {
                return array(
                    'current' => $ar[0],
                    'next' => $nx[0]
                );
            } else {
                return array(
                    'current' => $ar[0],
                    'next' => $nx[0],
                    'prev' => $pr[0]
                );
            }
        }
    }
}

// Return category page.
function get_category($category, $page, $perpage)
{
    $posts = get_post_sorted();

    $tmp = array();

    if (empty($perpage)) {
        $perpage = 10;
    }

    foreach ($posts as $index => $v) {

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        $str = explode('/', $replaced);
        $cat = $str[count($str) - 3];

        if (strtolower($category) === strtolower($cat)) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    $tmp = array_unique($tmp, SORT_REGULAR);

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return category info.
function get_category_info($category)
{
    $posts = get_category_files();

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v, $category . '.md') !== false) {

                $desc = new stdClass;

                // Replaced string
                $replaced = substr($v, 0, strrpos($v, '/')) . '/';

                // The static page URL
                $url= str_replace($replaced, '', $v);

                $desc->url = site_url() . 'category/' . str_replace('.md', '', $url);

                $desc->md = str_replace('.md', '', $url);

                $desc->file = $v;

                // Get the contents and convert it to HTML
                $content = file_get_contents($v);

                // Extract the title and body
                $desc->title = get_content_tag('t', $content, $category);

                // Get the contents and convert it to HTML
                $desc->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                $desc->description = get_content_tag("d", $content, get_description($desc->body));

                $tmp[] = $desc;
            }
        }
    }

    if (strtolower($category) == 'uncategorized') {
        return default_category();
    }

    return $tmp;
}

// Return default category
function default_category()
{
    $tmp = array();
    $desc = new stdClass;

    $desc->title = i18n("Uncategorized");
    $desc->url = site_url() . 'category/uncategorized';
    $desc->body = '<p>Topics that don&#39;t need a category, or don&#39;t fit into any other existing category.</p>';

    $desc->description = 'Topics that don&#39;t need a category, or don&#39;t fit into any other existing category.';

    return $tmp[] = $desc;
}

// Return category list

function category_list($custom = null) {

    $dir = "cache/widget";
    $filename = "cache/widget/category.list.cache";
    $tmp = array();
    $cat = array();
    $list = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $cat = unserialize(file_get_contents($filename));
    } else {
        $arr = get_category_info(null);
        foreach ($arr as $a) {
            $cat[] = array($a->md, $a->title);
        }
        array_push($cat, array('uncategorized', i18n('Uncategorized')));
        asort($cat);
        $tmp = serialize($cat);
        file_put_contents($filename, print_r($tmp, true));
    }

    if(!empty($custom)) {
        return $cat;
    }

    echo '<ul>';

    foreach ($cat as $k => $v) {
        if (get_categorycount($v['0']) !== 0) {
            echo '<li><a href="' . site_url() . 'category/' . $v['0'] . '">' . $v['1']. '</a></li>';
        }
    }

    echo '</ul>';

}

// Return type page.
function get_type($type, $page, $perpage)
{
    $posts = get_post_sorted();

    $tmp = array();

    if (empty($perpage)) {
        $perpage = 10;
    }

    foreach ($posts as $index => $v) {

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        $str = explode('/', $replaced);
        $tp = $str[count($str) - 2];

        if (strtolower($type) === strtolower($tp)) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    $tmp = array_unique($tmp, SORT_REGULAR);

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return tag page.
function get_tag($tag, $page, $perpage, $random)
{
    $posts = get_post_sorted();

    if ($random === true) {
        shuffle($posts);
    }

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('_', $v['basename']);
        $mtag = explode(',', rtrim($str[1], ','));
        $etag = explode(',', $tag);
        foreach ($mtag as $t) {
            foreach ($etag as $e) {
                $e = trim($e);
                if (strtolower($t) === strtolower($e)) {
                    $tmp[] = $v;
                }
            }
        }
    }

    if (empty($tmp)) {
        return false;
    }

    $tmp = array_unique($tmp, SORT_REGULAR);

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return archive page.
function get_archive($req, $page, $perpage)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('_', $v['basename']);
        if (strpos($str[0], "$req") !== false) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return posts list on profile.
function get_profile_posts($name, $page, $perpage)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        $author = $str[count($str) - 4];
        if (strtolower($name) === strtolower($author)) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return draft list
function get_draft($profile, $page, $perpage)
{

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        $author = $str[count($str) - 4];
        if (strtolower($profile) === strtolower($author) || $role === 'admin') {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return author info.
function get_author($name)
{
    $names = get_author_name();

    $username = 'config/users/' . $name . '.ini';

    $tmp = array();

    if (!empty($names)) {

        foreach ($names as $index => $v) {

            $author = new stdClass;

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';

            // Author string
            $str = explode('/', $replaced);
            $profile = $str[count($str) - 2];

            if ($name === $profile) {
                // Profile URL
                $url = str_replace($replaced, '', $v);
                $author->url = site_url() . 'author/' . $profile;

                // Get the contents and convert it to HTML
                $content = file_get_contents($v);

                // Extract the title and body
                $author->name = get_content_tag('t', $content, $author);

                // Get the contents and convert it to HTML
                $author->about = MarkdownExtra::defaultTransform(remove_html_comments($content));

                $tmp[] = $author;
            }
        }
    }

    if (!empty($tmp) || file_exists($username)) {
        return $tmp;
    } else {
        return false;
    }
}

// Return default profile
function default_profile($name)
{
    $tmp = array();
    $author = new stdClass;

    $author->name = $name;
    $author->about = '<p>Just another HTMLy user.</p>';

    $author->description = 'Just another HTMLy user';

    return $tmp[] = $author;
}

// Return static page.
function get_static_post($static)
{
    $posts = get_static_pages();

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v, $static . '.md') !== false) {

                $post = new stdClass;

                // Replaced string
                $replaced = substr($v, 0, strrpos($v, '/')) . '/';

                // The static page URL
                $url = str_replace($replaced, '', $v);
                $post->url = site_url() . str_replace('.md', '', $url);

                $post->file = $v;

                // Get the contents and convert it to HTML
                $content = file_get_contents($v);

                // Extract the title and body
                $post->title = get_content_tag('t', $content, $static);

                // Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                if (config('views.counter') == 'true') {
                    $post->views = get_views($post->file);
                }

                $post->description = get_content_tag("d", $content, get_description($post->body));
				
                $word_count = str_word_count(strip_tags($post->body));
                $post->readTime = ceil($word_count / 200);

                $tmp[] = $post;
            }
        }
    }

    return $tmp;
}

// Return static page.
function get_static_sub_post($static, $sub_static)
{
    $posts = get_static_sub_pages($static);

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v, $sub_static . '.md') !== false) {

                $post = new stdClass;

                // Replaced string
                $replaced = substr($v, 0, strrpos($v, '/')) . '/';

                // The static page URL
                $url = str_replace($replaced, '', $v);
                $post->url = site_url() . $static . "/" . str_replace('.md', '', $url);

                $post->file = $v;

                // Get the contents and convert it to HTML
                $content = file_get_contents($v);

                // Extract the title and body
                $post->title = get_content_tag('t', $content, $sub_static);

                // Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                $post->views = get_views($post->file);

                $post->description = get_content_tag("d", $content, get_description($post->body));
				
                $word_count = str_word_count(strip_tags($post->body));
                $post->readTime = ceil($word_count / 200);

                $tmp[] = $post;
            }
        }
    }

    return $tmp;
}

// Return frontpage content
function get_frontpage()
{
    $front = new stdClass;

    $filename = 'content/data/frontpage/frontpage.md';

    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $front->title = get_content_tag('t', $content, 'Welcome');
        $front->url = site_url() . 'front';
        // Get the contents and convert it to HTML
        $front->body = MarkdownExtra::defaultTransform(remove_html_comments($content));
    } else {
        $front->title = 'Welcome';
        $front->url = site_url() . 'front';
        $front->body = 'Welcome to our website.';
    }

    return $front;
}

// Return search page.
function get_keyword($keyword, $page, $perpage)
{
    $posts = get_post_sorted();

    $tmp = array();

    $words = explode(' ', $keyword);

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        $filter = $arr[1] . ' ' . $arr[2];
        foreach ($words as $word) {
            if (stripos($filter, $word) !== false) {
                if (!in_array($v, $tmp)) {
                    $tmp[] = $v; 
                }
            }
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);

}

// Get related posts base on post tag.
function get_related($tag, $custom = null, $count = null)
{

    if (empty($count)) {
        $count = config('related.count');
        if (empty($count)) {
            $count = 3;
        }
    }

    $posts = get_tag($tag, 1, $count + 1, true);
    $tmp = array();
    $req = urldecode($_SERVER['REQUEST_URI']);

    foreach ($posts as $post) {
        $url = $post->url;
        if (stripos($url, $req) === false) {
            $tmp[] = $post;
        }
    }

    if (empty($custom)) {

        $total = count($tmp);

        if ($total >= 1) {

            $i = 1;
            echo '<ul>';
            foreach ($tmp as $post) {
                echo '<li><a href="' . $post->url . '">' . $post->title . '</a></li>';
                if ($i++ >= $count)
                    break;
            }
            echo '</ul>';

        } else {
            echo '<ul><li>' . i18n('No_related_post_found') . '</li></ul>';
        }

    } else {
        return $tmp;
    }

}

// Return post count. Matching $var and $str provided.
function get_count($var, $str)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v[$str]);
        $url = $arr[0];
        if (stripos($url, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

// Return category count. Matching $var and $str provided.
function get_categorycount($var)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {

         $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        $str = explode('/', $replaced);
        $cat = '/blog/' . $str[count($str) - 3];
        if (stripos($cat, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

// Return type count. Matching $var and $str provided.
function get_typecount($var)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {

         $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        $str = explode('/', $replaced);
        $tp = '/' . $str[count($str) - 2] . '/';
        if (stripos($tp, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}


// Return draft count. Matching $var and $str provided.
function get_draftcount($var)
{
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

         $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        $str = explode('/', $replaced);
        $cat = $str[count($str) - 5];

        if (stripos($cat, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

// Return tag count. Matching $var and $str provided.
function get_tagcount($var, $str)
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v[$str]);
        $mtag = explode(',', rtrim($arr[1], ','));
        foreach ($mtag as $t) {
            if (strtolower($t) === strtolower($var)) {
                $tmp[] = $v;
            }
        }
    }

    return count($tmp);
}

// Return search result count
function keyword_count($keyword)
{
    $posts = get_post_sorted();

    $tmp = array();

    $words = explode(' ', $keyword);

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        $filter = $arr[1] . ' ' . $arr[2];
        foreach ($words as $word) {
            if (stripos($filter, $word) !== false) {
                $tmp[] = $v;
            }
        }
    }

    $tmp = array_unique($tmp, SORT_REGULAR);

    return count($tmp);
}

// Return recent posts lists
function recent_posts($custom = null, $count = null)
{
    if (empty($count)) {
        $count = config('recent.count');
        if (empty($count)) {
            $count = 5;
        }
    }

    $dir = "cache/widget";
    $filename = "cache/widget/recent.cache";
    $tmp = array();
    $posts = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $posts = unserialize(file_get_contents($filename));
        if (count($posts) != $count) {
            $posts = get_posts(null, 1, $count);
            $tmp = serialize($posts);
            file_put_contents($filename, print_r($tmp, true));
        }
    } else {
       $posts = get_posts(null, 1, $count);
       $tmp = serialize($posts);
       file_put_contents($filename, print_r($tmp, true));
    }

    if (!empty($custom)) {
        return $posts;
    } else {

        echo '<ul>';
        foreach ($posts as $post) {
            echo '<li><a href="' . $post->url . '">' . $post->title . '</a></li>';
        }
        if (empty($posts)) {
            echo '<li>No recent posts found</li>';
        }
        echo '</ul>';
    }
}

// Return recent posts lists
function recent_type($type, $custom = null, $count = null)
{
    if (empty($count)) {
        $count = config('recent.count');
        if (empty($count)) {
            $count = 5;
        }
    }

    $dir = 'cache/widget';
    $filename = 'cache/widget/recent.' . $type . '.cache';
    $tmp = array();
    $posts = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $posts = unserialize(file_get_contents($filename));
        if (count($posts) != $count) {
            $posts = get_type($type, 1, $count);
            $tmp = serialize($posts);
            file_put_contents($filename, print_r($tmp, true));
        }
    } else {
       $posts = get_type($type, 1, $count);
       $tmp = serialize($posts);
       file_put_contents($filename, print_r($tmp, true));
    }

    if (!empty($custom)) {
        return $posts;
    } else {

        echo '<ul>';
        foreach ($posts as $post) {
            echo '<li><a href="' . $post->url . '">' . $post->title . '</a></li>';
        }
        if (empty($posts)) {
            echo '<li>No recent ' . $type . ' found</li>';
        }
        echo '</ul>';
    }
}

// Return popular posts lists
function popular_posts($custom = null, $count = null)
{

    static $_views = array();
    $tmp = array();

    if (empty($count)) {
        $count = config('popular.count');
        if (empty($count)) {
            $count = 5;
        }
    }

    if (config('views.counter') == 'true') {
        if (empty($_views)) {
            $filename = 'content/data/views.json';
            if (file_exists($filename)) {
                $_views = json_decode(file_get_contents($filename), true);
                if(is_array($_views)) {
                    arsort($_views);
                    $i = 1;
                    foreach ($_views as $key => $val) {
                        if (file_exists($key)) {
                            if (stripos($key, 'blog') !== false) {
                                $tmp[] = pathinfo($key);
                                if ($i++ >= $count)
                                break;
                            }
                        }
                    }

                    $dir = "cache/widget";
                    $filecache = "cache/widget/popular.cache";
                    $ar = array();
                    $posts = array();

                    if (is_dir($dir) === false) {
                        mkdir($dir, 0775, true);
                    }

                    if (file_exists($filecache)) {
                        $posts = unserialize(file_get_contents($filecache));
                        if (count($posts) != $count) {
                            $posts = get_posts($tmp, 1, $count);
                            $ar = serialize($posts);
                            file_put_contents($filecache, print_r($ar, true));
                        }
                    } else {
                        $posts = get_posts($tmp, 1, $count);
                        $ar = serialize($posts);
                        file_put_contents($filecache, print_r($ar, true));
                    }

                    if (empty($custom)) {
                        echo '<ul>';
                        foreach ($posts as $post) {
                            echo '<li><a href="' . $post->url . '">' . $post->title . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    else {
                        return $posts;
                    }
                } else {
                    if(empty($custom)) {
                        echo '<ul><li>No popular posts found</li></ul>';
                    } else {
                        return $tmp;
                    }
                }
            } else {
                if (empty($custom)) {
                    echo '<ul><li>No popular posts found</li></ul>';
                } else {
                    return $tmp;
                }
            }
        }
    } else {
        if (empty($custom)) {
            echo '<ul><li>No popular posts found</li></ul>';
        } else {
            return $tmp;
        }
    }
}

// Return an archive list, categorized by year and month.
function archive_list($custom = null)
{

    $dir = "cache/widget";
    $filename = "cache/widget/archive.cache";
    $ar = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    $posts = get_post_unsorted();
    $by_year = array();
    $col = array();

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v);

                // Replaced string
                $str = $arr[0];
                $replaced = substr($str, 0, strrpos($str, '/')) . '/';

                $date = str_replace($replaced, '', $arr[0]);
                $data = explode('-', $date);
                $col[] = $data;
            }

            foreach ($col as $row) {

                $y = $row['0'];
                $m = $row['1'];
                $by_year[$y][] = $m;
            }

            $ar = serialize($by_year);
            file_put_contents($filename, print_r($ar, true));

        } else {
            $by_year = unserialize(file_get_contents($filename));
        }

        # Most recent year first
        krsort($by_year);

        # Iterate for display
        $i = 0;
        $len = count($by_year);

        if (empty($custom)) {
            foreach ($by_year as $year => $months) {
                if ($i == 0) {
                    $class = 'expanded';
                    $arrow = '&#9660;';
                } else {
                   $class = 'collapsed';
                    $arrow = '&#9658;';
                }
                $i++;

                $by_month = array_count_values($months);
                # Sort the months
                krsort($by_month);

                $script = <<<EOF
                    if (this.parentNode.className.indexOf('expanded') > -1){this.parentNode.className = 'collapsed';this.innerHTML = '&#9658;';} else {this.parentNode.className = 'expanded';this.innerHTML = '&#9660;';}
EOF;
                echo '<ul class="archivegroup">';
                echo '<li class="' . $class . '">';
                echo '<a href="javascript:void(0)" class="toggle" onclick="' . $script . '">' . $arrow . '</a> ';
                echo '<a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a> ';
                echo '<span class="count">(' . count($months) . ')</span>';
                echo '<ul class="month">';

                foreach ($by_month as $month => $count) {
                    $name = strftime('%B', mktime(0, 0, 0, $month, 1, 2010));
                    echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '-' . $month . '">' . $name . '</a>';
                    echo ' <span class="count">(' . $count . ')</span></li>';
                }

                echo '</ul>';
                echo '</li>';
                echo '</ul>';
            }
        } else {
            return $by_year;
        }
    }
}

// Return tag cloud.
function tag_cloud($custom = null)
{

    $dir = "cache/widget";
    $filename = "cache/widget/tags.cache";
    $tg = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    $posts = get_post_unsorted();
    $tags = array();

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {
                $arr = explode('_', $v);
                $data = rtrim($arr[1], ',');
                $mtag = explode(',', $data);
                foreach ($mtag as $etag) {
                    $tags[] = strtolower($etag);
                }
            }
            $tag_collection = array_count_values($tags);
            ksort($tag_collection);
            $tg = serialize($tag_collection);
            file_put_contents($filename, print_r($tg, true));
        } else {
            $tag_collection = unserialize(file_get_contents($filename));
        }

        if(empty($custom)) {
            // Font sizes
            $max_size = 22; // max font size in %
            $min_size = 8; // min font size in %

            // Get the largest and smallest array values
            $max_qty = max(array_values($tag_collection));
            $min_qty = min(array_values($tag_collection));

            // Find the range of values
            $spread = $max_qty - $min_qty;
            if (0 == $spread) { // we don't want to divide by zero
                $spread = 1;
            }

            // Font-size increment
            // this is the increase per tag quantity (times used)
            $step = ($max_size - $min_size)/($spread);

            foreach ($tag_collection as $tag => $count) {
                $size = $min_size + (($count - $min_qty) * $step);
                echo ' <a class="tag-cloud-link" href="'. site_url(). 'tag/'. $tag .'" style="font-size:'. $size .'pt;">'.tag_i18n($tag).'</a> ';
            }            

        } else {
            return $tag_collection;
        }
    } else {
        if(empty($custom)) return;
        return $tags;
    }
}

// Helper function to determine whether
// to show the previous buttons
function has_prev($prev)
{
    if (!empty($prev)) {
        return array(
            'url' => $prev->url,
            'title' => $prev->title,
            'date' => $prev->date,
            'body' => $prev->body,
            'description' => $prev->description,
            'tag' => $prev->tag,
            'category' => $prev->category,
            'author' => $prev->author,
            'authorName' => $prev->authorName,
            'authorAbout' => $prev->authorAbout,
            'authorUrl' => $prev->authorUrl,
            'related' => $prev->related,
            'views' => $prev->views,
            'type' => $prev->type,
            'file' => $prev->file,
            'image' => $prev->image,
            'video' => $prev->video,
            'audio' => $prev->audio,
            'quote' => $prev->quote,
            'link' => $prev->link,
            'categoryUrl' => $prev->categoryUrl,
            'readTime' => $prev->readTime
        );
    }
}

// Helper function to determine whether
// to show the next buttons
function has_next($next)
{
    if (!empty($next)) {
        return array(
            'url' => $next->url,
            'title' => $next->title,
            'date' => $next->date,
            'body' => $next->body,
            'description' => $next->description,
            'tag' => $next->tag,
            'category' => $next->category,
            'author' => $next->author,
            'authorName' => $next->authorName,
            'authorAbout' => $next->authorAbout,
            'authorUrl' => $next->authorUrl,
            'related' => $next->related,
            'views' => $next->views,
            'type' => $next->type,
            'file' => $next->file,
            'image' => $next->image,
            'video' => $next->video,
            'audio' => $next->audio,
            'quote' => $next->quote,
            'link' => $next->link,
            'categoryUrl' => $next->categoryUrl,
            'readTime' => $next->readTime
        );
    }
}

// Helper function to determine whether
// to show the pagination buttons
function has_pagination($total, $perpage, $page = 1)
{
    if (!$total) {
        $total = count(get_post_unsorted());
    }
    $totalPage = ceil($total / $perpage);
    $number = 'Page '. $page . ' of ' . $totalPage;
    $pager = get_pagination($page, $total, $perpage, 2);
    return array(
        'prev' => $page > 1,
        'next' => $total > $page * $perpage,
        'pagenum' => $number,
        'html' => $pager,
        'items' => $total,
        'perpage' => $perpage
    );
}

//function to return the pagination string
function get_pagination($page = 1, $totalitems, $perpage = 10, $adjacents = 1, $pagestring = '?page=')
{
    //defaults
    if(!$adjacents) $adjacents = 1;
    if(!$perpage) $perpage = 10;
    if(!$page) $page = 1;

    //other vars
    $prev = $page - 1;                                    //previous page is page - 1
    $next = $page + 1;                                    //next page is page + 1
    $lastpage = ceil($totalitems / $perpage);             //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                                //last page minus 1

    /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
    */
    $pagination = '';
    if($lastpage > 1)
    {
        $pagination .= '<ul class="pagination">';

        //previous button
        if ($page > 1)
            $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $prev .'">« Prev</a></li>';
        else
            $pagination .= '<li class="page-item disabled"><span class="page-link">« Prev</span></li>';

        //pages
        if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
        {
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination .= '<li class="page-item active"><span class="page-link">'. $counter.'</span></li>';
                else
                    $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $counter .'">'. $counter .'</a></li>';
            }
        }
        elseif($lastpage >= 7 + ($adjacents * 2))    //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if($page < 1 + ($adjacents * 3))
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item active"><span class="page-link">'. $counter .'</span></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $counter .'">'. $counter .'</a></li>';
                }
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $lpm1 .'">'. $lpm1 .'</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $lastpage .'">'. $lastpage .'</a></li>';
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $pagestring .'1">1</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring .'2">2</a></li>';
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item active"><span class="page-link">'. $counter .'</span></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $counter .'">'. $counter .'</a></li>';
                }
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $lpm1 .'">'. $lpm1 .'</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $lastpage . '">'. $lastpage .'</a></li>';
            }
            //close to end; only hide early pages
            else
            {
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring .'1">1</a></li>';
                $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring .'2">2</a></li>';
                $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= '<li class="page-item active"><span class="page-link">'. $counter .'</span></li>';
                    else
                        $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $counter .'">'. $counter .'</a></li>';
                }
            }
        }

        //next button
        if ($page < $counter - 1)
            $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $next .'">Next »</a></li>';
        else
            $pagination .= '<li class="page-item disabled"><span class="page-link">Next »</span></li>';
        $pagination .= '</ul>';
    }

    return $pagination;

}

// Get the meta description
function get_description($string, $char = null)
{
    if(empty($char)) {
        $char = config('description.char');
        if(empty($char)) {
            $char = 150;
        }
    }
    if (strlen(strip_tags($string)) < $char) {
        return safe_html(strip_tags($string));
    } else {
        $string = safe_html(strip_tags($string));
        $string = substr($string, 0, $char);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string;
    }

}

// Get the teaser
function get_teaser($string, $url = null, $char = null)
{

    $teaserType = config('teaser.type');
    $more = config('read.more');

    if(empty($more)) {
        $more = 'Read more';
    }

    if(empty($char)) {
        $char = config('teaser.char');
        if(empty($char)) {
            $char = 200;
        }
    }

    if ($teaserType === 'full') {
        $readMore = explode('<!--more-->', $string);
        if (isset($readMore['1'])) {
            $patterns = array('<a id="more"></a><br>', '<p><a id="more"></a><br></p>');
            $string = str_replace($patterns, '', $readMore['0']);
            $string = replace_href($string, 'a', 'footnote-ref', $url);
            return $string . '<p class="jump-link"><a class="read-more btn btn-cta-secondary" href="'. $url .'#more">' . $more . '</a></p>';
        } else {
            return $string;
        }
    } elseif (strlen(strip_tags($string)) < $char) {
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        return $string;
    } else {
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        $string = substr($string, 0, $char);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string;
    }
}

// Get thumbnail from image and Youtube.
function get_thumbnail($text, $url = null)
{
    if (config('img.thumbnail') == 'true') {

        $teaserType = config('teaser.type');

        if (strlen(strip_tags($text)) > config('teaser.char') && $teaserType === 'trimmed') {

            libxml_use_internal_errors(true);
            $default = config('default.thumbnail');
            $dom = new DOMDocument();
            $dom->loadHtml($text);
            $imgTags = $dom->getElementsByTagName('img');
            $vidTags = $dom->getElementsByTagName('iframe');
            if ($imgTags->length > 0) {
                $imgElement = $imgTags->item(0);
                $imgSource = $imgElement->getAttribute('src');
                if (!empty($url)) {
                    return $imgSource;
                } else {
                    return '<div class="thumbnail" style="background-image:url(' . $imgSource . ');"></div>';
                }
            } elseif ($vidTags->length > 0) {
                $vidElement = $vidTags->item(0);
                $vidSource = $vidElement->getAttribute('src');
                $fetch = explode("embed/", $vidSource);
                if (isset($fetch[1])) {
                    $vidThumb = '//img.youtube.com/vi/' . $fetch[1] . '/default.jpg';
                    if (!empty($url)) {
                        return $vidThumb;
                    } else {
                        return '<div class="thumbnail" style="background-image:url(' . $vidThumb . ');"></div>';
                    }
                }
            } else {
                if (!empty($default)) {
                    if (!empty($url)) {
                        return $default;
                    } else {
                        return '<div class="thumbnail" style="background-image:url(' . $default . ');"></div>';
                    }
                }
            }
        } else {
            // Ignore
        }
    }
}

// Get image from post and Youtube thumbnail.
function get_image($text)
{
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHtml($text);
    $imgTags = $dom->getElementsByTagName('img');
    $vidTags = $dom->getElementsByTagName('iframe');
    if ($imgTags->length > 0) {
        $imgElement = $imgTags->item(0);
        $imgSource = $imgElement->getAttribute('src');
        return $imgSource;
    } elseif ($vidTags->length > 0) {
        $vidElement = $vidTags->item(0);
        $vidSource = $vidElement->getAttribute('src');
        $fetch = explode("embed/", $vidSource);
        if (isset($fetch[1])) {
            $vidThumb = '//img.youtube.com/vi/' . $fetch[1] . '/sddefault.jpg';
            return $vidThumb;
        }
    } else{
       return false;
    }
}

// Return edit tab on post
function tab($p)
{
    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (isset($p->author)) {
        if ($user === $p->author || $role === 'admin') {
            echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">View</a></li><li><a href="' . $p->url . '/edit?destination=post">Edit</a></li></ul></div>';
        }
    } else {
        echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">View</a></li><li><a href="' . $p->url . '/edit?destination=post">Edit</a></li></ul></div>';
    }
}

// Use base64 encode image to speed up page load time.
function base64_encode_image($filename = string, $filetype = string)
{
    if ($filename) {
        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
    }
}

// Social links. Deprecated
function social($imgDir = null)
{
    $twitter = config('social.twitter');
    $facebook = config('social.facebook');
    $tumblr = config('social.tumblr');
    $rss = site_url() . 'feed/rss';

    if ($imgDir === null) {
        $imgDir = "readable/img/";
    }

    if (!empty($twitter)) {
        echo '<a href="' . $twitter . '" target="_blank"><img src="' . site_url() . 'themes/' . $imgDir . 'twitter.png" width="32" height="32" alt="Twitter"/></a>';
    }

    if (!empty($facebook)) {
        echo '<a href="' . $facebook . '" target="_blank"><img src="' . site_url() . 'themes/' . $imgDir . 'facebook.png" width="32" height="32" alt="Facebook"/></a>';
    }

    if (!empty($tumblr)) {
        echo '<a href="' . $tumblr . '" target="_blank"><img src="' . site_url() . 'themes/' . $imgDir . 'tumblr.png" width="32" height="32" alt="Tumblr"/></a>';
    }

    echo '<a href="' . $rss . '" target="_blank"><img src="' . site_url() . 'themes/' . $imgDir . 'rss.png" width="32" height="32" alt="RSS Feed"/></a>';
}

// Copyright
function copyright()
{
    $blogcp = blog_copyright();
    $credit = 'Powered by <a href="http://www.htmly.com" target="_blank" rel="nofollow">HTMLy</a>';

    if (!empty($blogcp)) {
        return $copyright = '<span class="copyright">' . $blogcp . '</span> <span class="credit">' . $credit . '</span>';
    } else {
        return $credit = '<span class="credit">' . $credit . '</span>';
    }
}

// Disqus on post.
function disqus($title = null, $url = null)
{
    $comment = config('comment.system');
    $disqus = config('disqus.shortname');
    $script = <<<EOF
    <script type="text/javascript">
        var getAbsolutePath = function(href) {
            var link = document.createElement('a');
            link.href = href;
            return link.href;
        };
        var disqus_shortname = '{$disqus}';
        var disqus_title = '{$title}';
        var disqus_url = getAbsolutePath('{$url}');
        (function () {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
EOF;
    if (!empty($disqus) && $comment == 'disqus') {
        return $script;
    }
}

// Disqus comment count on teaser
function disqus_count()
{
    $comment = config('comment.system');
    $disqus = config('disqus.shortname');
    $script = <<<EOF
    <script type="text/javascript">
        var disqus_shortname = '{$disqus}';
        (function () {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/count.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
EOF;
    if (!empty($disqus) && $comment == 'disqus') {
        return $script;
    }
}

// Disqus recent comments
function recent_comments()
{
    $comment = config('comment.system');
    $disqus = config('disqus.shortname');
    $script = <<<EOF
        <script type="text/javascript" src="//{$disqus}.disqus.com/recent_comments_widget.js?num_items=5&hide_avatars=0&avatar_size=48&excerpt_length=200&hide_mods=0"></script>
EOF;
    if (!empty($disqus) && $comment == 'disqus') {
        return $script;
    }
}

// Facebook comments
function facebook()
{
    $comment = config('comment.system');
    $appid = config('fb.appid');
    $script = <<<EOF
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId={$appid}";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
    <style>.fb-comments, .fb_iframe_widget span, .fb-comments iframe {width: 100%!important;}</style>
EOF;

    if (!empty($appid) && $comment == 'facebook') {
        return $script;
    }
}

// Google Publisher (Google+ page).
function publisher()
{
    $publisher = config('google.publisher');
    if (!empty($publisher)) {
        return $publisher;
    }
}

// Google Analytics
function analytics()
{
    $analytics = config('google.analytics.id');
    $gtag = config('google.gtag.id');    
    $script = <<<EOF
    <script>
        (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '{$analytics}', 'auto');
        ga('send', 'pageview');
</script>
EOF;
    $gtagScript = <<<EOF
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$gtag}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{$gtag}');
</script>
EOF;
    if (!empty($gtag)) {
        return $gtagScript;
    } elseif (!empty($analytics)) {
        return $script;
    }
}

function slashUrl($url) {
    return rtrim($url, '/') . '/';
}

function parseNodes($nodes, $child = null, $class = null) {
    if (empty($child)) {
        $ul = '<ul class="nav navbar-nav '.$class.'">';
        foreach ($nodes as $node) {
            if (isset($node->children)) { 
                $ul .= parseNode($node, true);
            } else {
                $ul .= parseNode($node);
            }
        }
        $ul .= '</ul>';
        return $ul;
    } else {
        $ul = '<ul class="subnav dropdown-menu" role="menu">';
        foreach ($nodes as $node) {
            if (isset($node->children)) { 
                $ul .= parseNode($node, true);
            } else {
                $ul .= parseNode($node);
            }
        }
        $ul .= '</ul>';
        return $ul;
    }
}

function parseNode($node, $child = null) {
    $req = strtok($_SERVER["REQUEST_URI"],'?');
    $url = parse_url(slashUrl($node->slug));
    $su = parse_url(site_url());
    if (empty($child)) {

        if (isset($url['host']) && isset($su['host'])) {
            if ($url['host'] ==  $su['host']) {
                if (slashUrl($url['path']) == slashUrl($req)) {
                    $li = '<li class="item nav-item active '.$node->class.'">';
                } else  {                    
                    $li = '<li class="item nav-item '.$node->class.'">';
                }
            } else {
                $li = '<li class="item nav-item '.$node->class.'">'; // Link out
            }
        } else {
            if (slashUrl($node->slug) == slashUrl($req)) {
                $li = '<li class="item nav-item active '.$node->class.'">';
            } else {
                $li = '<li class="item nav-item '.$node->class.'">';
            }
        }
        
        $li .= '<a class="nav-link" href="'.htmlspecialchars(slashUrl($node->slug), FILTER_SANITIZE_URL).'">'.$node->name.'</a>';
        if (isset($node->children)) { 
            $li .= parseNodes($node->children, true, null);
        }
        $li .= '</li>';
        return $li;
    } else {
        
        if (isset($url['host']) && isset($su['host'])) {
            if ($url['host'] ==  $su['host']) {
                if (slashUrl($url['path']) == slashUrl($req)) {
                    $li = '<li class="item nav-item dropdown active '.$node->class.'">';
                } else  {                    
                    $li = '<li class="item nav-item dropdown '.$node->class.'">';
                }
            } else {
                $li = '<li class="item nav-item dropdown '.$node->class.'">'; // Link out
            }
        } else {
            if (slashUrl($node->slug) == slashUrl($req)) {
                $li = '<li class="item nav-item dropdown active '.$node->class.'">';
            } else {
                $li = '<li class="item nav-item dropdown '.$node->class.'">';
            }
        }
        
        $li .= '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="'.htmlspecialchars(slashUrl($node->slug), FILTER_SANITIZE_URL).'">'.$node->name.'<b class="caret"></b></a>';
        if (isset($node->children)) { 
            $li .= parseNodes($node->children, true, null);
        }
        $li .= '</li>';
        return $li;            
    }
}

// Menu
function menu($class = null)
{
    $filename = "content/data/menu.json";
    if (file_exists($filename)) {
        $json = json_decode(file_get_contents('content/data/menu.json', true));
        $nodes = json_decode($json);
        if (empty($nodes)) {
            get_menu($class);
        } else {
            $html = parseNodes($nodes, null, $class);
            libxml_use_internal_errors(true);
            $doc = new DOMDocument();
            $doc->loadHTML($html);

            $finder = new DOMXPath($doc);
            $elements = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' dropdown-menu ')]");

            // loop through all <ul> with dropdown-menu class
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                foreach ($nodes as $node) {
                    $class = $node->getAttribute('class');
                    if (stripos($class, 'active')) {
                        $parentClass = $element->parentNode->getAttribute('class') . ' active';
                        $element->parentNode->setAttribute('class', $parentClass);
                    } 
                }
            }
            
        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', utf8_decode($doc->saveHTML($doc->documentElement)));
            
        }
    } else {
        get_menu($class);    
    }
}

// Get the title from file
function get_title_from_file($v)
{
    // Get the contents and convert it to HTML
    $content = MarkdownExtra::defaultTransform(file_get_contents($v));

    $replaced = substr($v, 0, strrpos($v, '/')) . '/';
    $base = str_replace($replaced, '', $v);

    // Extract the title and body
    return get_content_tag('t', $content, str_replace('-', ' ', str_replace('.md', '', $base)));
}

// Auto generate menu from static page
function get_menu($custom)
{
    $posts = get_static_pages();
    $req = $_SERVER['REQUEST_URI'];

    if (!empty($posts)) {

        asort($posts);

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/' || stripos($req, site_path() . '/?page') !== false) {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }

        if (config('blog.enable') == 'true' ) {
            if ($req == site_path() . '/blog' || stripos($req, site_path() . '/blog?page') !== false) {
                echo '<li class="item active"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
            } else {
                echo '<li class="item"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
            }
        }

        $i = 0;
        $len = count($posts);

        foreach ($posts as $index => $v) {

            if ($i == $len - 1) {
                $class = 'item last';
            } else {
                $class = 'item';
            }
            $i++;

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';
            $base = str_replace($replaced, '', $v);
            $url = site_url() . str_replace('.md', '', $base);

            $title = get_title_from_file($v);

            if ($req == site_path() . "/" . str_replace('.md', '', $base) || stripos($req, site_path() . "/" . str_replace('.md', '', $base)) !== false) {
                $active = ' active';
                $reqBase = '';
            } else {
                $active = '';
            }

            $subPages = get_static_sub_pages(str_replace('.md', '', $base));
            if (!empty($subPages)) {
                asort($subPages);
                echo '<li class="' . $class . $active .' dropdown">';
                echo '<a class="dropdown-toggle" data-toggle="dropdown" href="' . $url . '">' . ucwords($title) . '<b class="caret"></b></a>';
                echo '<ul class="subnav dropdown-menu" role="menu">';
                $iSub = 0;
                $countSub = count($subPages);
                foreach ($subPages as $index => $sp) {
                    $classSub = "item";
                    if ($iSub == 0) {
                        $classSub .= " first";
                    }
                    if ($iSub == $countSub - 1) {
                        $classSub .= " last";
                    }
                    $replacedSub = substr($sp, 0, strrpos($sp, '/')) . '/';
                    $baseSub = str_replace($replacedSub, '', $sp);

                    if ($req == site_path() . "/" . str_replace('.md', '', $base) . "/" . str_replace('.md', '', $baseSub)) {
                        $classSub .= ' active';
                    }
                    $urlSub = $url . "/" . str_replace('.md', '', $baseSub);
                    echo '<li class="' . $classSub . '"><a href="' . $urlSub . '">' . get_title_from_file($sp) . '</a></li>';
                    $iSub++;
                }
                echo '</ul>';
            } else {
                echo '<li class="' . $class . $active .'">';
                echo '<a href="' . $url . '">' . ucwords($title) . '</a>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/') {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }
        if (config('blog.enable') == 'true' ) {
            if ($req == site_path() . '/blog' || stripos($req, site_path() . '/blog?page') !== false) {
                echo '<li class="item active"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
            } else {
                echo '<li class="item"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
            }
        }
        echo '</ul>';
    }
}

// Search form
function search($text = null)
{
    if(!empty($text)) {
        echo <<<EOF
    <form id="search-form" method="get">
        <input type="text" class="search-input" name="search" value="{$text}" onfocus="if (this.value == '{$text}') {this.value = '';}" onblur="if (this.value == '') {this.value = '{$text}';}">
        <input type="submit" value="{$text}" class="search-button">
    </form>
EOF;
    } else {
        $search = i18n('Search');
        echo <<<EOF
    <form id="search-form" method="get">
        <input type="text" class="search-input" name="search" value="{$search}" onfocus="if (this.value == '{$search}') {this.value = '';}" onblur="if (this.value == '') {this.value = '{$search}';}">
        <input type="submit" value="{$search}" class="search-button">
    </form>
EOF;
    }
    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
}

// The not found error
function not_found()
{
    $vroot = rtrim(config('views.root'), '/');
    $lt = $vroot . '/layout--404.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--404';
    } else {
        $layout = '';
    }
    
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    render('404', array(
        'title' => 'This page doesn\'t exist! - ' . blog_title(),
        'description' => '404 Not Found',
        'canonical' => site_url(),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; 404 Not Found',
        'bodyclass' => 'error-404',
        'is_404' => true,
    ), $layout);
    die();
}

// Turn an array of posts into an RSS feed
function generate_rss($posts)
{
    $feed = new Feed();
    $channel = new Channel();
    $rssLength = config('rss.char');

    $channel
        ->title(blog_title())
        ->description(blog_description())
        ->url(site_url())
        ->appendTo($feed);

    foreach ($posts as $p) {

        if (!empty($rssLength)) {
            if (strlen(strip_tags($p->body)) < config('rss.char')) {
                $string = preg_replace('/\s\s+/', ' ', strip_tags($p->body));
                $body = $string . '...';
            } else {
                $string = preg_replace('/\s\s+/', ' ', strip_tags($p->body));
                $string = substr($string, 0, config('rss.char'));
                $string = substr($string, 0, strrpos($string, ' '));
                $body = $string . '...';
            }
        } else {
            $body = $p->body;
        }

        $item = new Item();
        $item
            ->category(strip_tags($p->category));
        $item
            ->title($p->title)
            ->pubDate($p->date)
            ->description($body)
            ->url($p->url)
            ->appendTo($channel);
    }

    echo $feed;
}

// Return post, archive url for sitemap
function sitemap_post_path()
{
    $posts = get_post_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {

        $post = new stdClass;

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $author = $str[count($str) - 5];

        $post->authorUrl = site_url() . 'author/' . $author;

        $dt = str_replace($replaced, '', $arr[0]);
        $t = str_replace('-', '', $dt);
        $time = new DateTime($t);
        $date = $time->format("Y-m-d H:i:s");

        // The post date
        $post->date = strtotime($date);

        // The archive per day
        $post->archiveday = site_url() . 'archive/' . date('Y-m-d', $post->date);

        // The archive per day
        $post->archivemonth = site_url() . 'archive/' . date('Y-m', $post->date);

        // The archive per day
        $post->archiveyear = site_url() . 'archive/' . date('Y', $post->date);

        // The post URL
        if (config('permalink.type') == 'post') {
            $post->url = site_url() . 'post/' . str_replace('.md', '', $arr[2]);
        } else {
            $post->url = site_url() . date('Y/m', $post->date) . '/' . str_replace('.md', '', $arr[2]);
        }

        $tmp[] = $post;
    }

    return $tmp;
}

// Return static page path for sitemap
function sitemap_page_path()
{
    $posts = get_static_pages();

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {

            $post = new stdClass;

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';

            // The static page URL
            $url = str_replace($replaced, '', $v);
            $post->url = site_url() . str_replace('.md', '', $url);

            $tmp[] = $post;
        }
    }

    return $tmp;
}

// Generate sitemap.xml.
function generate_sitemap($str)
{
    $default_priority = '0.5';

    header('X-Robots-Tag: noindex');

    echo '<?xml version="1.0" encoding="UTF-8"?>';

    if ($str == 'index') {

        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if (config('sitemap.priority.base') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.base.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.post') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.post.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.static') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.static.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.category') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.category.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.tag') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.tag.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.archiveDay') !== 'false' || config('sitemap.priority.archiveMonth') !== 'false' || config('sitemap.priority.archiveYear') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.archive.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.author') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.author.xml</loc></sitemap>';
        }

        if (config('sitemap.priority.type') !== 'false') {
            echo '<sitemap><loc>' . site_url() . 'sitemap.type.xml</loc></sitemap>';
        }

        echo '</sitemapindex>';

    } elseif ($str == 'base') {

        $priority = (config('sitemap.priority.base')) ? config('sitemap.priority.base') : '1.0';

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($priority !== 'false') {
            echo '<url><loc>' . site_url() . '</loc><priority>' . $priority . '</priority></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'post') {

        $priority = (config('sitemap.priority.post')) ? config('sitemap.priority.post') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = sitemap_post_path();
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($posts as $p) {

            echo '<url><loc>' . $p->url . '</loc><priority>' . $priority . '</priority><lastmod>' . date('Y-m-d', $p->date) . '</lastmod></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'static') {

        $priority = (config('sitemap.priority.static')) ? config('sitemap.priority.static') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = sitemap_page_path();
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($posts as $p) {

            echo '<url><loc>' . $p->url . '</loc><priority>' . $priority . '</priority></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'tag') {

        $priority = (config('sitemap.priority.tag')) ? config('sitemap.priority.tag') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_post_unsorted();
        }

        $tags = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v);

                $data = $arr[1];
                $mtag = explode(',', $data);
                foreach ($mtag as $etag) {
                    $tags[] = strtolower($etag);
                }
            }

            foreach ($tags as $t) {
                $tag[] = site_url() . 'tag/' . strtolower($t);
            }

            if (isset($tag)) {

                $tag = array_unique($tag, SORT_REGULAR);

                foreach ($tag as $t) {
                    echo '<url><loc>' . $t . '</loc><priority>' . $priority . '</priority></url>';
                }
            }
        }

        echo '</urlset>';

    } elseif ($str == 'archive') {

        $priorityDay = (config('sitemap.priority.archiveDay')) ? config('sitemap.priority.archiveDay') : $default_priority;
        $priorityMonth = (config('sitemap.priority.archiveMonth')) ? config('sitemap.priority.archiveMonth') : $default_priority;
        $priorityYear = (config('sitemap.priority.archiveYear')) ? config('sitemap.priority.archiveYear') : $default_priority;

        $posts = sitemap_post_path();
        $day = array();
        $month = array();
        $year = array();

        foreach ($posts as $p) {
            $day[] = $p->archiveday;
            $month[] = $p->archivemonth;
            $year[] = $p->archiveyear;
        }

        $day = array_unique($day, SORT_REGULAR);
        $month = array_unique($month, SORT_REGULAR);
        $year = array_unique($year, SORT_REGULAR);

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($priorityDay !== 'false') {
            foreach ($day as $d) {
                echo '<url><loc>' . $d . '</loc><priority>' . $priorityDay . '</priority></url>';
            }
        }

        if ($priorityMonth !== 'false') {
            foreach ($month as $m) {
                echo '<url><loc>' . $m . '</loc><priority>' . $priorityMonth . '</priority></url>';
            }
        }

        if ($priorityYear !== 'false') {
            foreach ($year as $y) {
                echo '<url><loc>' . $y . '</loc><priority>' . $priorityYear . '</priority></url>';
            }
        }

        echo '</urlset>';

    } elseif ($str == 'author') {

        $priority = (config('sitemap.priority.author')) ? config('sitemap.priority.author') : $default_priority;

        $author = array();
        if ($priority !== 'false') {

            $posts = sitemap_post_path();

            foreach ($posts as $p) {
                $author[] = $p->authorUrl;
            }

            $author = array_unique($author, SORT_REGULAR);
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($priority !== 'false') {
            foreach ($author as $a) {
                echo '<url><loc>' . $a . '</loc><priority>' . $priority . '</priority></url>';
            }
        }

        echo '</urlset>';

    } elseif ($str == 'category') {

        $priority = (config('sitemap.priority.category')) ? config('sitemap.priority.category') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_post_unsorted();
        }

        $cats = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v);

                $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

                $str = explode('/', $replaced);

                $cats[] = $str[count($str) - 3];

            }

            foreach ($cats as $c) {
                $cat[] = site_url() . 'category/' . strtolower($c);
            }

            if (isset($cat)) {

                $cat = array_unique($cat, SORT_REGULAR);

                foreach ($cat as $c) {
                    echo '<url><loc>' . $c . '</loc><priority>' . $priority . '</priority></url>';
                }
            }
        }

        echo '</urlset>';

    } elseif ($str == 'type') {

        $priority = (config('sitemap.priority.type')) ? config('sitemap.priority.type') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_post_unsorted();
        }

        $cats = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v);

                $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

                $str = explode('/', $replaced);

                $types[] = $str[count($str) - 2];
            }

            foreach ($types as $t) {
                $type[] = site_url() . 'type/' . strtolower($t);
            }

            if (isset($type)) {

                $type = array_unique($type, SORT_REGULAR);

                foreach ($type as $t) {
                    echo '<url><loc>' . $t . '</loc><priority>' . $priority . '</priority></url>';
                }
            }
        }

        echo '</urlset>';
    }
}

// Function to generate OPML file
function generate_opml()
{
    $opml_data = array(
        'head' => array(
            'title' => blog_title() . ' OPML File',
            'ownerName' => blog_title(),
            'ownerId' => site_url()
        ),
        'body' => array(
            array(
                'text' => blog_title(),
                'description' => blog_description(),
                'htmlUrl' => site_url(),
                'language' => 'unknown',
                'title' => blog_title(),
                'type' => 'rss',
                'version' => 'RSS2',
                'xmlUrl' => site_url() . 'feed/rss'
            )
        )
    );

    $opml = new opml($opml_data);
    echo $opml->render();
}

// Turn an array of posts into a JSON
function generate_json($posts)
{
    return json_encode($posts);
}

// Create Zip files
function Zip($source, $destination, $include_dir = false)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    if (file_exists($destination)) {
        unlink($destination);
    }

    $zip = new ZipArchive();

    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    if (is_dir($source) === true) {

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                continue;

            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } elseif (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } elseif (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

// TRUE if the current page is an index page like frontpage, tag index, archive index and search index.
function is_index()
{
    $req = $_SERVER['REQUEST_URI'];
    if (stripos($req, '/category/') !== false || stripos($req, '/archive/') !== false || stripos($req, '/tag/') !== false || stripos($req, '/search/') !== false || stripos($req, '/type/') !== false || stripos($req, '/blog') !== false || $req == site_path() . '/' || stripos($req, site_path() . '/?page') !== false) {
        return true;
    } else {
        return false;
    }
}

// Return blog title
function blog_title()
{
    return config('blog.title');
}

// Return blog tagline
function blog_tagline()
{
    return config('blog.tagline');
}

// Return blog description
function blog_description()
{
    return config('blog.description');
}

// Return blog copyright
function blog_copyright()
{
    return config('blog.copyright');
}

// Return author info. Deprecated
function authorinfo($name = null, $about = null)
{
    if (config('author.info') == 'true') {
        return '<div class="author-info"><h4>by <strong>' . $name . '</strong></h4>' . $about . '</div>';
    }
}

// Output head contents
function head_contents()
{
    $output = '';
    $wmt_id = config('google.wmt.id');
    static $_version = array();

    $filename = "cache/installedVersion.json";
    if (file_exists($filename)) {
        $_version = json_decode(file_get_contents($filename), true);
    }

    if (isset($_version['tag_name'])) {
        $version = 'HTMLy ' . $_version['tag_name'];
    } else {
        $version = 'HTMLy';
    }

    $favicon = '<link rel="icon" type="image/x-icon" href="' . site_url() . 'favicon.ico" />';
    $charset = '<meta charset="utf-8" />';
    $generator = '<meta name="generator" content="' . $version . '" />';
    $xua = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
    $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1" />';
    $sitemap = '<link rel="sitemap" href="' . site_url() . 'sitemap.xml" />';
    $feed = '<link rel="alternate" type="application/rss+xml" title="' . blog_title() . ' Feed" href="' . site_url() . 'feed/rss" />';
    $webmasterTools = '';
    if (!empty($wmt_id)) {
        $webmasterTools = '<meta name="google-site-verification" content="' . $wmt_id . '" />';
    }

    $output .= $charset . "\n" . $xua . "\n" . $viewport . "\n" . $generator . "\n" . $favicon . "\n" . $sitemap . "\n" . $feed . "\n" . $webmasterTools . "\n";

    return $output;
}

// Return toolbar
function toolbar()
{
    $base = site_url();

    echo <<<EOF
    <link href="{$base}system/resources/css/toolbar.css" rel="stylesheet" />
EOF;
    echo '<div id="toolbar"><ul>';
    echo '<li class="tb-admin"><a href="' . $base . 'admin">' . i18n('Admin') . '</a></li>';
    echo '<li class="tb-addcontent"><a href="' . $base . 'admin/content">' . i18n('Add_content') . '</a></li>';
    if (is_admin()) {
        echo '<li class="tb-posts"><a href="' . $base . 'admin/posts">' . i18n('Posts') . '</a></li>';
        if (config('views.counter') == 'true') {
            echo '<li class="tb-popular"><a href="' . $base . 'admin/popular">Popular</a></li>';
        }
    }
    echo '<li class="tb-mine"><a href="' . $base . 'admin/pages">Pages</a></li>';
    echo '<li class="tb-draft"><a href="' . $base . 'admin/draft">' . i18n('Draft') . '</a></li>';
    if (is_admin()) {
        echo '<li class="tb-categories"><a href="' . $base . 'admin/categories">' . i18n('Categories') . '</a></li>';
        echo '<li class="tb-authors"><a href="' . $base . 'admin/authors">' . i18n('Authors') . '</a></li>';
    }
    echo '<li class="tb-import"><a href="' . $base . 'admin/menu">Menu</a></li>';
    if (is_admin()) {
      echo '<li class="tb-config"><a href="' . $base . 'admin/config">' . i18n('Config') . '</a></li>';
    }
    echo '<li class="tb-backup"><a href="' . $base . 'admin/backup">' . i18n('Backup') . '</a></li>';
    echo '<li class="tb-update"><a href="' . $base . 'admin/update">' . i18n('Update') . '</a></li>';
    echo '<li class="tb-clearcache"><a href="' . $base . 'admin/clear-cache">' . i18n('Clear_cache') . '</a></li>';
    echo '<li class="tb-editprofile"><a href="' . $base . 'edit/profile">' . i18n('Edit_profile') . '</a></li>';
    echo '<li class="tb-logout"><a href="' . $base . 'logout">' . i18n('Logout') . '</a></li>';

    echo '</ul></div>';
}

// File cache
function file_cache($request)
{
    if (config('cache.off') == 'true') return;

    $hour = str_replace(',', '.', config('cache.expiration'));
    if (empty($hour)) {
        $hour = 6;
    }

    $now   = time();

    $c = str_replace('/', '#', str_replace('?', '~', $request));
    $cachefile = 'cache/page/' . $c . '.cache';
    if (file_exists($cachefile)) {
        if ($now - filemtime($cachefile) >= 60 * 60 * $hour) {
            unlink($cachefile);
        } else {
            header('Content-type: text/html; charset=utf-8');
            readfile($cachefile);
            die;
        }
    }
}

// Generate csrf token
function generate_csrf_token()
{
    $_SESSION[config("site.url")]['csrf_token'] = sha1(microtime(true) . mt_rand(10000, 90000));
}

// Get csrf token
function get_csrf()
{
    if (!isset($_SESSION[config("site.url")]['csrf_token']) || empty($_SESSION[config("site.url")]['csrf_token'])) {
        generate_csrf_token();
    }
    return $_SESSION[config("site.url")]['csrf_token'];
}

// Check the csrf token
function is_csrf_proper($csrf_token)
{
    if ($csrf_token == get_csrf()) {
        return true;
    }
    return false;
}

// Add page views count
function add_view($page)
{
    $dir = 'content/data/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $filename = "content/data/views.json";
    $views = array();
    if (file_exists($filename)) {
        $views = json_decode(file_get_contents($filename), true);
    }
    if (isset($views[$page])) {
        $views[$page]++;
    } else {
        $views[$page] = 1;
    }
    file_put_contents($filename, json_encode($views, JSON_UNESCAPED_UNICODE));
}

// Get the page views count
function get_views($page)
{
    $_views = array();
    $filename = "content/data/views.json";
    if (file_exists($filename)) {
        $_views = json_decode(file_get_contents($filename), true);
    }
    if (isset($_views[$page])) {
        return $_views[$page];
    }
    return -1;
}

// Get tag inside the markdown files
function get_content_tag($tag, $string, $alt = null)
{
    $reg = '/\<!--' . $tag . '(.+)' . $tag . '--\>/';
    $ary = array();
    if (preg_match($reg, $string, $ary)) {
        if (isset($ary[1])) {
            $result = trim($ary[1]);
            if (!empty($result)) {
                return $result;
            }
        }
    }
    return $alt;
}

// Strip html comment
function remove_html_comments($content)
{
    $patterns = array('/(\s|)<!--t(.*)t-->(\s|)/', '/(\s|)<!--d(.*)d-->(\s|)/', '/(\s|)<!--tag(.*)tag-->(\s|)/', '/(\s|)<!--image(.*)image-->(\s|)/', '/(\s|)<!--video(.*)video-->(\s|)/', '/(\s|)<!--audio(.*)audio-->(\s|)/', '/(\s|)<!--link(.*)link-->(\s|)/', '/(\s|)<!--quote(.*)quote-->(\s|)/');
    return preg_replace($patterns, '', $content);
}

// Google recaptcha
function isCaptcha($reCaptchaResponse)
{
    if (config('google.reCaptcha') != 'true') {
        return true;
    }
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $options = array(
        "secret" => config("google.reCaptcha.private"),
        "response" => $reCaptchaResponse,
        "remoteip" => $_SERVER['REMOTE_ADDR'],
    );
    $fileContent = @file_get_contents($url . "?" . http_build_query($options));
    if ($fileContent === false) {
        return false;
    }
    $json = json_decode($fileContent, true);
    if ($json == false) {
        return false;
    }
    return ($json['success']);
}

// Get video ID
function get_video_id($url)
{
    if(empty($url)) {
       return;
    }

    $link = parse_url($url);
    
    if(!isset($link['host'])) {
        return $url;
    }

    if (stripos($link['host'], 'youtube.com') !== false || stripos($link['host'], 'youtu.be') !== false) { 
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1];
    } elseif (stripos($link['host'], 'vimeo.com') !== false) {
        preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $match);
        return $match[3];
    } else {
        return $url;
    }
}

// Shorten the string
function shorten($string = null, $char = null)
{
    if(empty($char) || empty($string)) {
        return;
    }

    if (strlen(strip_tags($string)) < $char) {
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        return $string;
    } else {
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        $string = substr($string, 0, $char);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string;
    }

}

// save the i18n tag
function save_tag_i18n($tag,$tagDisplay)
{

    $dir = 'content/data/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $filename = "content/data/tags.lang";
    $tags = array();
    $tmp = array();
    $views = array();

    $tt = explode(',', rtrim($tag, ','));
    $tl = explode(',', rtrim($tagDisplay, ','));
    $tags = array_combine($tt,$tl);

    if (file_exists($filename)) {
        $views = unserialize(file_get_contents($filename));
        foreach ($tags as $key => $val) {
            if (isset($views[$key])) {
                $views[$key] = $val;
            } else {
                $views[$key] = $val;
            }
        }
    } else {
        $views = $tags;
    }

    $tmp = serialize($views);
    file_put_contents($filename, print_r($tmp, true));

}

// translate tag to i18n
function tag_i18n($tag)
{
    static $tags = array();

    if (empty($tags)) {
        $filename = "content/data/tags.lang";
        if (file_exists($filename)) {
            $tags = unserialize(file_get_contents($filename));
        }
    }
    if (isset($tags[$tag])) {
        return $tags[$tag];
    }
    return $tag;
}

// return html safe string
function safe_html($string)
{
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = preg_replace('/\r\n|\r|\n/', ' ', $string);
    $string = preg_replace('/\s\s+/', ' ', $string);
    $string = ltrim(rtrim($string));
    return $string;
}

// return tag safe string
function safe_tag($string)
{
    $tags = array();
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = explode(',', $string);
    $string = array_map('trim', $string);
        foreach ($string as $str) {
            $tags[] = $str;
        }
    $string = implode(',', $tags);
    $string = preg_replace('/[\s_]/', '-', $string);
    return $string;

}

// rename category folder
function rename_category_folder($string, $old_url)
{

    $old = str_replace('.md', '/', $old_url);
    $url = substr($old, 0, strrpos($old, '/'));
    $ostr = explode('/', $url);
    $url = '/blog/' . $ostr[count($ostr) - 1];

    $dir = get_category_folder();

    $file = array();

    foreach ($dir as $index => $v) {
        if (stripos($v, $url) !== false) {
            $str = explode('/', $v);
            $n = $str[count($ostr) - 4] . '/' . $str[count($ostr) - 3] .'/'. $str[count($ostr) - 2] .'/'. $string . '/';
            $file[] = array($v, $n);
        }
    }

    foreach ($file as $f) {
        if(is_dir($f[0])) {
            rename($f[0], $f[1]);
        }
    }

}

function replace_href($string, $tag, $class, $url)
{

    libxml_use_internal_errors(true);

    // Load the HTML in DOM
    $doc = new DOMDocument();
    $doc->loadHTML($string);
    // Then select all anchor tags
    $all_anchor_tags = $doc->getElementsByTagName($tag);
    foreach ($all_anchor_tags as $_tag) {
        if ($_tag->getAttribute('class') == $class) {
            // If match class get the href value
            $old = $_tag->getAttribute('href');
            $new = $_tag->setAttribute('href', $url . utf8_decode($old));
        }
    }

    return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', utf8_decode($doc->saveHTML($doc->documentElement)));

}

function get_language()
{

    $langID = config('language');
    $langFile = 'lang/'. $langID . '.ini';
    $local = $langID;

    // Settings for the language
    if (!isset($langID) || config('language') === 'en' || config('language') === 'en_US' || !file_exists($langFile)) {
        i18n('source', 'lang/en_US.ini'); // Load the English language file
        setlocale(LC_ALL, 'en_US.utf8'); // Change locale to English
    } else {
        i18n('source', $langFile);
        setlocale(LC_ALL, $local . '.utf8');
    }

}

function format_date($date)
{

    $date_format = config('date.format');

    // Timeago
    if(config('timeago.format') == 'true') {
        $time = time() - $date;
        if ($time < 60) {
        return ( $time > 1 ) ? $time . ' ' . i18n('Seconds_ago') : i18n('A_second_ago');
        }
        elseif ($time < 3600) {
        $tmp = floor($time / 60);
        return ($tmp > 1) ? $tmp . ' ' . i18n('Minutes_ago') : i18n('A_minute_ago');
        }
        elseif ($time < 86400) {
        $tmp = floor($time / 3600);
        return ($tmp > 1) ? $tmp . ' ' . i18n('Hours_ago') : i18n('An_hour_ago');
        }
        elseif ($time < 2592000) {
        $tmp = floor($time / 86400);
        return ($tmp > 1) ? $tmp . ' ' . i18n('Days_ago') : i18n('Yesterday');
        }
        elseif ($time < 946080000) {
            if (!isset($date_format) || empty($date_format)) {
                return strftime('%e %B %Y', $date);
            } else {
                return strftime($date_format, $date);
            }
        }
        else {
        $tmp = floor($time / 946080000);
            if (!isset($date_format) || empty($date_format)) {
                return strftime('%e %B %Y', $date);
            } else {
                return strftime($date_format, $date);
            }
        }
    } else {
        // Default
        if (!isset($date_format) || empty($date_format)) {
            return strftime('%e %B %Y', $date);
        } else {
            return strftime($date_format, $date);
        }
    }
}

function valueMaker($value)
{
    if (is_string($value))
        return htmlspecialchars($value);

    if ($value === true)
        return "true";
    if ($value === false)
        return "false";

    if ($value == false)
        return "0";
    return (string)$value;
}