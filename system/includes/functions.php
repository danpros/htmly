<?php
if (!defined('HTMLY')) die('HTMLy');

use \Michelf\MarkdownExtra;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

// Get blog post with more info about the path. Sorted by filename.
function get_blog_posts()
{
    static $_posts = array();

    if (empty($_posts)) {
        $url = 'cache/index/index-posts.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_posts = unserialize(file_get_contents($url));
    }
    return $_posts;
}

// Get static page path.
function get_static_pages()
{
    static $_page = array();

    if (empty($_page)) {
        $url = 'cache/index/index-pages.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_page = unserialize(file_get_contents($url));
    }
    return $_page;
}

// Get static subpage path.
function get_static_subpages($static = null)
{
    static $_sub_page = array();

    if (empty($_sub_page)) {
        $url = 'cache/index/index-subpages.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_sub_page = unserialize(file_get_contents($url));
    }
    if ($static != null) {
        $stringLen = strlen($static);
        return array_filter($_sub_page, function ($sub_page) use ($static, $stringLen) {
            $x = explode("/", $sub_page['dirname']);
            if ($x[2] == $static) {
                return true;
            }
            return false;
        });
    }
    return $_sub_page;
}

// Get author name.
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

// Get posts draft.
function get_draft_posts()
{
    static $_draft = array();
    if (empty($_draft)) {
        $url = 'cache/index/index-draft.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_draft = unserialize(file_get_contents($url));
    }
    return $_draft;
}

// Get static page draft.
function get_draft_pages()
{
    static $_draftPage = array();
    if (empty($_draftPage)) {
        $tmp = array();
        $tmp = glob('content/static/draft/*.md', GLOB_NOSORT);
        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                $_draftPage[] = pathinfo($file);
            }
        }
        usort($_draftPage, "sortfile_a");
    }
    return $_draftPage;
}

// Get static subpage draft.
function get_draft_subpages($static = null)
{
    static $_draftSubpage = array();
    if (empty($_draftSubpage)) {
        $tmp = array();
        $tmp = glob('content/static/*/draft/*.md', GLOB_NOSORT);
        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                $_draftSubpage[] = pathinfo($file);
            }
        }
        usort($_draftSubpage, "sortfile_a");
    }
    if ($static != null) {
        $stringLen = strlen($static);
        return array_filter($_draftSubpage, function ($sub_page) use ($static, $stringLen) {
            $x = explode("/", $sub_page['dirname']);
            if ($x[2] == $static) {
                return true;
            }
            return false;
        });
    }    
    return $_draftSubpage;
}

// Get scheduled posts.
function get_scheduled_posts()
{
    static $_scheduled = array();
    if (empty($_scheduled)) {
        $url = 'cache/index/index-scheduled.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_scheduled = unserialize(file_get_contents($url));
    }
    return $_scheduled;
}

// Get category info files.
function get_category_files()
{
    static $_desc = array();
    if (empty($_desc)) {
        $url = 'cache/index/index-category-files.txt';
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

// Get category slug.
function get_category_slug()
{
    static $_cslug = array();
    if (empty($_cslug)) {
        $url = 'cache/index/index-category.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_cslug = unserialize(file_get_contents($url));
    }
    return $_cslug;
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

// Get images in content/images folder
function get_gallery() {
    static $_gallery = array();
    if (empty($_gallery)) {
        $tmp = array();
        $tmp = glob('content/images/*', GLOB_NOSORT);
        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                $_gallery[] = pathinfo($file);
            }
        }
        usort($_gallery, "sortfile_d");
    }
    return $_gallery;
}

// usort function. Sort by filename.
function sortfile_a($a, $b)
{
    return $a['basename'] == $b['basename'] ? 0 : (($a['basename'] > $b['basename']) ? 1 : -1);
}

// usort function. 
function sortfile_d($a, $b)
{
    return $a['basename'] == $b['basename'] ? 0 : (($a['basename'] < $b['basename']) ? 1 : -1);
}

// usort function. Sort by date.
function sortdate($a, $b)
{
    return $a->date == $b->date ? 0 : (($a->date < $b->date) ? 1 : -1);
}

// Rebuilt cache index
function rebuilt_cache($type = null)
{
    $dir = 'cache/index';
    $posts_cache = array();
    $page_cache = array();
    $subpage_cache = array();
    $author_cache = array();
    $scheduled_cache = array();
    $category_cache = array();
    $draft_cache = array();

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    // Rebuilt posts index
    $tmp = array();
    $ctmp = array();
    $tmp = glob('content/*/blog/*/*/*.md', GLOB_NOSORT);
    if (is_array($tmp)) {
        foreach ($tmp as $file) {
            if(strpos($file, '/draft/') === false) {
                $posts_cache[] = pathinfo($file);
                $pc = explode('/', $file);
                $ctmp[] = $pc[3];
            }
        }
    }
    usort($posts_cache, "sortfile_d");
    $posts_string = serialize($posts_cache);
    file_put_contents('cache/index/index-posts.txt', print_r($posts_string, true));   

    // Rebuilt scheduled posts index
    $stmp = array();
    $stmp = glob('content/*/*/*/*/scheduled/*.md', GLOB_NOSORT);
    if (is_array($stmp)) {
        foreach ($stmp as $file) {
            $scheduled_cache[] = pathinfo($file);
            $ss = explode('/', $file);
            $ctmp[] = $ss[3];
        }
    }
    usort($scheduled_cache, "sortfile_d");
    $scheduled_string = serialize($scheduled_cache);
    file_put_contents('cache/index/index-scheduled.txt', print_r($scheduled_string, true));
    
    // Rebuilt draft posts index
    $drf = array();
    $drf = glob('content/*/*/*/draft/*.md', GLOB_NOSORT);
    if (is_array($drf)) {
        foreach ($drf as $file) {
            $draft_cache[] = pathinfo($file);
            $dd = explode('/', $file);
            $ctmp[] = $dd[3];
        }
    }
    usort($draft_cache, "sortfile_d");
    $draft_string = serialize($draft_cache);
    file_put_contents('cache/index/index-draft.txt', print_r($draft_string, true));
    
    // Rebuilt category files index
    $ftmp = array();
    $ftmp =  glob('content/data/category/*.md', GLOB_NOSORT);
    if (is_array($ftmp)) {
        foreach ($ftmp as $file) {
            $category_cache[] = pathinfo($file);
            $ctmp[] = pathinfo($file, PATHINFO_FILENAME);            
        }
    }
    usort($category_cache, "sortfile_a");
    $category_string = serialize($category_cache);
    file_put_contents('cache/index/index-category-files.txt', print_r($category_string, true));
    
    // Rebuilt category slug index
    $dirc = array();
    $dirc = array_unique($ctmp, SORT_REGULAR); 
    file_put_contents('cache/index/index-category.txt', print_r(serialize($dirc), true));
    
    // Rebuilt static page index
    $ptmp = array();
    $ptmp =  glob('content/static/*.md', GLOB_NOSORT);
    if (is_array($ptmp)) {
        foreach ($ptmp as $file) {
            if(strpos($file, '/draft/') === false) {
                $page_cache[] = pathinfo($file);
            }
        }
    }
    usort($page_cache, "sortfile_a");
    $page_string = serialize($page_cache);
    file_put_contents('cache/index/index-pages.txt', print_r($page_string, true));

    // Rebuilt subpage index
    $sptmp = array();
    $sptmp =  glob('content/static/*/*.md', GLOB_NOSORT);
    if (is_array($sptmp)) {
        foreach ($sptmp as $file) {
            if(strpos($file, '/draft/') === false) {
                $subpage_cache[] = pathinfo($file);
            }
        }
    }
    usort($subpage_cache, "sortfile_a");
    $subpage_string = serialize($subpage_cache);
    file_put_contents('cache/index/index-subpages.txt', print_r($subpage_string, true));

    // Rebuilt user profile index
    $atmp = array();
    $atmp =  glob('content/*/author.md', GLOB_NOSORT);
    if (is_array($atmp)) {
        foreach ($atmp as $file) {
            $author_cache[] = pathinfo($file);
        }
    }
    usort($author_cache, "sortfile_a");
    $author_string = serialize($author_cache);
    file_put_contents('cache/index/index-author.txt', print_r($author_string, true));

    // Remove the widget cache
    foreach (glob('cache/widget/*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

}

// Return blog posts.
function get_posts($posts, $page = 1, $perpage = 0)
{
    if (empty($posts)) {
        $posts = get_blog_posts();
    }

    $tmp = array();

    // Extract a specific page with results
    $posts = array_slice($posts, ($page - 1) * $perpage, $perpage);

    $cList = category_list(true);

    foreach ($posts as $index => $v) {

        $post = new stdClass;

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $v['basename']);

        // dirname string
        $dirname = $v['dirname'];

        // Author string
        $str = explode('/', $dirname);
        $author = $str[1];
        
        foreach ($cList as $a => $t) {
            if ($t['0'] === $str[3]) {
                $post->category = '<a href="' . site_url() . 'category/' . $t['0'] . '">' . $t['1'] . '</a>';
                $post->categoryUrl = site_url() . 'category/' . $t['0'];
                $post->categoryCount = $t['2'];
                $post->categorySlug = $t['0'];
                $post->categoryMd = $t['0'];
                $post->categoryTitle = $t['1'];
                $post->categoryb = '<a itemprop="item" href="' . site_url() . 'category/' . $t['0'] . '"><span itemprop="name">' . $t['1'] . '</span></a>';
            }
        }

        $type = $str[4];
        $post->ct = $str[3];

        // The post author + author url
        $post->author = $author;
        $post->authorUrl = site_url() . 'author/' . $author;

        $profile = get_author($author);
        if (isset($profile[0])) {
            $post->authorName = $profile[0]->name;
            $post->authorAbout = $profile[0]->about;
        } else {
            $post->authorName = $author;
            $post->authorAbout = i18n('Author_Description');
        }

        $post->type = $type;
        $dt = str_replace($dirname, '', $arr[0]);
        $t = str_replace('-', '', $dt);
        $time = new DateTime($t);
        $timestamp = $time->format("Y-m-d H:i:s");

        // The post date
        $post->date = strtotime($timestamp);
        $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($filepath)));

        // The archive per day
        $post->archive = site_url() . 'archive/' . date('Y-m', $post->date);

        if (config('permalink.type') == 'post') {
            $post->url = site_url() . 'post/' . str_replace('.md', '', $arr[2]);
        } else {
            $post->url = site_url() . date('Y/m', $post->date) . '/' . str_replace('.md', '', $arr[2]);
        }

        $post->slug = str_replace('.md', '', $arr[2]);

        $post->file = $filepath;

        $content = file_get_contents($filepath);

        // Extract the title and body
        $post->title = get_content_tag('t', $content, 'Untitled post: ' . format_date($post->lastMod, 'l, j F Y, H:i'));
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
            if (count($tl) == count($t)) {
                $tCom = array_combine($t, $tl);
            } else {
                $tCom = array_combine($t, $t);    
            }
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

        $post->related = $post->categorySlug. ',' .$post->url;

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

function get_pages($pages, $page = 1, $perpage = 0) 
{
    if (empty($pages)) {
        $pages = get_static_pages();
    }

    $tmp = array();

    // Extract a specific page with results
    $pages = array_slice($pages, ($page - 1) * $perpage, $perpage);
    
    foreach ($pages as $index => $v) {
        $post = new stdClass;

        // The static page URL
        $url= $v['filename'];
        
        $post->url = site_url() . $url;

        $post->file = $v['dirname'] . '/' . $v['basename'];
        $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($post->file)));
        
        $post->md = $url;
        $post->slug = $url;
        $post->parent = null;

        // Get the contents and convert it to HTML
        $content = file_get_contents($post->file);

        // Extract the title and body
        $post->title = get_content_tag('t', $content, 'Untitled static page: ' . format_date($post->lastMod, 'l, j F Y, H:i'));

        // Get the contents and convert it to HTML
        $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

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

function get_subpages($sub_pages, $page = 1, $perpage = 0) 
{
    if (empty($sub_pages)) {
        $sub_pages = get_static_subpages();
    }

    $tmp = array();

    // Extract a specific page with results
    $sub_pages = array_slice($sub_pages, ($page - 1) * $perpage, $perpage);
    
    foreach ($sub_pages as $index => $v) {
        
        $post = new stdClass;
        
        $static = str_replace(dirname($v['dirname']) . '/', '', $v['dirname']);

        // The static page URL
        $url= $v['filename'];
        $post->url = site_url() . $static . "/" . $url;

        $post->file = $v['dirname'] . '/' . $v['basename'];
        $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($post->file)));
        
        $post->md = $url;
        $post->slug = $url;
        $post->parent = $static;

        // Get the contents and convert it to HTML
        $content = file_get_contents($post->file);

        // Extract the title and body
        $post->title = get_content_tag('t', $content, 'Untitled static subpage: ' . format_date($post->lastMod, 'l, j F Y, H:i'));

        // Get the contents and convert it to HTML
        $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

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
    $posts = get_blog_posts();

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

// Return static page.
function find_page($static = null)
{
    $pages = get_static_pages();
    
    $tmp = array();

    if (!empty($pages)) {

        foreach ($pages as $index => $v) {
            if (is_null($static)) {
                $post = new stdClass;

                // The static page URL
                $url= $v['filename'];
                
                $post->url = site_url() . $url;

                $post->file = $v['dirname'] . '/' . $v['basename'];
                $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($post->file)));
                
                $post->md = $url;
                $post->slug = $url;
                $post->parent = null;

                // Get the contents and convert it to HTML
                $content = file_get_contents($post->file);

                // Extract the title and body
                $post->title = get_content_tag('t', $content, 'Untitled static page: ' . format_date($post->lastMod, 'l, j F Y, H:i'));

                // Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                if (config('views.counter') == 'true') {
                    $post->views = get_views($post->file);
                } else {
                    $post->views = null;
                }

                $post->description = get_content_tag("d", $content, get_description($post->body));

                $word_count = str_word_count(strip_tags($post->body));
                $post->readTime = ceil($word_count / 200);

                $tmp[] = $post;         
                
            } elseif (stripos($v['basename'], $static . '.md') !== false) {

                // Use the get_posts method to return
                // a properly parsed object

                $ar = get_pages($pages, $index + 1, 1);
                $nx = get_pages($pages, $index, 1);
                $pr = get_pages($pages, $index + 2, 1);

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
                } elseif (count($pages) == $index + 1) {
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
    
    return $tmp;
}

// Return static subpage.
function find_subpage($static, $sub_static = null)
{
    $sub_pages = array_values(get_static_subpages($static));

    $tmp = array();

    if (!empty($sub_pages)) {

        foreach ($sub_pages as $index => $v) {
            
            if (is_null($sub_static)) {
                
                $post = new stdClass;

                // The static page URL
                $url= $v['filename'];
                $post->url = site_url() . $static . "/" . $url;

                $post->file = $v['dirname'] . '/' . $v['basename'];
                $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($post->file)));
                
                $post->md = $url;
                $post->slug = $url;
                $post->parent = $static;

                // Get the contents and convert it to HTML
                $content = file_get_contents($post->file);

                // Extract the title and body
                $post->title = get_content_tag('t', $content, 'Untitled static subpage: ' . format_date($post->lastMod, 'l, j F Y, H:i'));

                // Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                if (config('views.counter') == 'true') {
                    $post->views = get_views($post->file);
                } else {
                    $post->views = null;
                }

                $post->description = get_content_tag("d", $content, get_description($post->body));

                $word_count = str_word_count(strip_tags($post->body));
                $post->readTime = ceil($word_count / 200);

                $tmp[] = $post;
                
            } elseif (stripos($v['basename'], $sub_static . '.md') !== false) {

                // Use the get_posts method to return
                // a properly parsed object

                $ar = get_subpages($sub_pages, $index + 1, 1);
                $nx = get_subpages($sub_pages, $index, 1);
                $pr = get_subpages($sub_pages, $index + 2, 1);

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
                } elseif (count($sub_pages) == $index + 1) {
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

    return $tmp;
}

// Return category page.
function get_category($category, $page, $perpage, $random = null)
{
    $posts = get_blog_posts();
    
    if ($random === true) {
        shuffle($posts);
    }

    $tmp = array();

    if (empty($perpage)) {
        $perpage = 10;
    }

    foreach ($posts as $index => $v) {

        // dirname string
        $dirname = $v['dirname'];

        $str = explode('/', $dirname);

        if (strtolower($category) === strtolower($str[3])) {
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
function get_category_info($category = null)
{

    $tmp = array();
    $cslug= get_category_slug();
    if (!empty($cslug)) {
        asort($cslug);
        if (is_null($category)) {
            foreach ($cslug as $key => $c){
                $ctmp = read_category_info($c);
                if (!empty($ctmp[0])) {
                    $tmp[] = $ctmp[0];
                } else {
                    $tmp[] = default_category($c);
                }            
            }
        } else {
            foreach ($cslug as $key => $c){
                if ($c === $category) {
                    $ctmp = read_category_info($category);
                    if (!empty($ctmp[0])) {
                        $tmp[] = $ctmp[0];
                    } else {
                        $tmp[] = default_category($category);
                    }
                }
            }    
        }
    }
    return $tmp;
}

function read_category_info($category) 
{
    $tmp = array();
    $cFiles = get_category_files();

    if (!empty($cFiles)) {
        foreach ($cFiles as $index => $v) {
            if (stripos($v['basename'], $category . '.md') !== false) {

                $desc = new stdClass;

                // The filename
                $filename = $v['dirname'] . '/' . $v['basename'];
                
                $url= $v['filename'];

                $desc->url = site_url() . 'category/' . $url;

                $desc->md = $url;
                
                $desc->slug = $url;

                $desc->count = get_categorycount($url);

                $desc->file = $filename;

                // Get the contents and convert it to HTML
                $content = file_get_contents($desc->file);

                // Extract the title and body
                $desc->title = get_content_tag('t', $content, $category);

                // Get the contents and convert it to HTML
                $desc->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                $desc->description = get_content_tag("d", $content, get_description($desc->body));

                $tmp[] = $desc;
            } 
        }
    }
    return $tmp;    
}

// Return default category
function default_category($category = null)
{
    $tmp = array();
    $desc = new stdClass;

    if ($category == 'uncategorized') {
        $desc->title = i18n("Uncategorized");
        $desc->url = site_url() . 'category/uncategorized';
        $desc->slug = 'uncategorized';
        $desc->body = '<p>' . i18n('Uncategorized_comment') . '</p>';
        $desc->md = 'uncategorized';
        $desc->description = i18n('Uncategorized_comment');
        $desc->file = '';
        $desc->count = get_categorycount($desc->md);
    } else {
        $desc->title = $category;
        $desc->url = site_url() . 'category/' . $category;
        $desc->slug = $category;
        $desc->body = '<p>' . i18n('All_blog_posts') . ': ' . $category . '</p>';
        $desc->md = $category;
        $desc->description = i18n('All_blog_posts') . ': ' . $category;
        $desc->file = '';
        $desc->count = get_categorycount($category);        
    }

    return $tmp[] = $desc;
}

// Return category list
function category_list($custom = null) {

    $dir = "cache/widget";
    $filename = "cache/widget/category.list.cache";
    $tmp = array();
    $cat = array();
    $list = array();

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $cat = unserialize(file_get_contents($filename));
    } else {
        $arr = get_category_info(null);
        foreach ($arr as $i => $a) {
            $cat[] = array($a->md, $a->title, $a->count, $a->description);
        }

        $tmp = serialize($cat);
        file_put_contents($filename, print_r($tmp, true));
    }

    if(!empty($custom)) {
        return $cat;
    }

    echo '<ul>';

    foreach ($cat as $k => $v) {
        if ($v['2'] !== 0) {
            echo '<li><a href="' . site_url() . 'category/' . $v['0'] . '">' . $v['1'] . '</a> <span>('. $v['2'] .')</span></li>';
        }
    }

    echo '</ul>';

}

// Return type page.
function get_type($type, $page, $perpage)
{
    $posts = get_blog_posts();

    $tmp = array();

    if (empty($perpage)) {
        $perpage = 10;
    }

    foreach ($posts as $index => $v) {

        // dirname string
        $dirname = $v['dirname'];

        $str = explode('/', $dirname);

        if (strtolower($type) === strtolower($str[4])) {
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
function get_tag($tag, $page, $perpage, $random = null)
{
    $posts = get_blog_posts();

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
    $posts = get_blog_posts();

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
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        if (strtolower($name) === strtolower($str[1])) {
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

    $tmp = array();

    if (!empty($names)) {

        foreach ($names as $index => $v) {

            $author = new stdClass;

            // dirname string
            $dirname = $v['dirname'];

            // Author string
            $str = explode('/', $dirname);
            $profile = $str[1];

            if ($name === $profile) {
                // Profile URL
                $filename = $v['dirname'] . '/' . $v['basename'];
                
                $author->file = $filename;

                $author->url = site_url() . 'author/' . $profile;
                $author->slug = $profile;

                // Get the contents and convert it to HTML
                $content = file_get_contents($author->file);

                // Extract the title and body
                $author->name = get_content_tag('t', $content, $author);

                // Get the contents and convert it to HTML
                $author->about = MarkdownExtra::defaultTransform(remove_html_comments($content));
                
                $author->body = $author->about;
                
                $author->title = $author->name;
                
                $author->description = strip_tags($author->about);

                $tmp[] = $author;
            }
        }
    }

    if (!empty($tmp)) {
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
    $author->title = $name;
    $author->about = '<p>' . i18n('Author_Description') . '</p>';
    $author->body = '<p>' . i18n('Author_Description') . '</p>';
    $author->description = i18n('Author_Description');
    $author->url = site_url(). 'author/' . $name;
    $author->slug = $name;
    $author->file = '';

    return $tmp[] = $author;
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
    $posts = get_blog_posts();

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

// Get related posts base on post category.
function get_related($tag, $custom = null, $count = null)
{

    if (empty($count)) {
        $count = config('related.count');
        if (empty($count)) {
            $count = 3;
        }
    }

    $tmp = array();
    $exp = explode(',', $tag);
    $posts = get_category($exp[0], 1, $count + 1, true);

    foreach ($posts as $post) {
        if ($post->url !== $exp[1]) {
            $tmp[] = $post;
        }
    }

    if (empty($custom)) {
        if (!empty($tmp)) {
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
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        if ($str === 'basename') {
            $arr = explode('_', $v[$str]);
            $url = $arr[0];
            if (stripos($url, "$var") !== false) {
                $tmp[] = $v;
            }
        } else {
            if (stripos($v[$str], $var) !== false) {
                $tmp[] = $v;
            }            
        }
    }

    return count($tmp);
}

// Return category count. Matching $var
function get_categorycount($var)
{
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

        if (stripos($v['dirname'], '/' . $var . '/') !== false) {
            $tmp[] = $v;
        }
        
    }

    return count($tmp);
}

// Return type count. Matching $var
function get_typecount($var)
{
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

        if (stripos($v['dirname'], '/' . $var) !== false) {
            $tmp[] = $v;
        }
        
    }

    return count($tmp);
}


// Return draft count. Matching $var
function get_draftcount($var)
{
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

        if (stripos($v['dirname'], '/' . $var . '/') !== false) {
            $tmp[] = $v;
        }
        
    }
    return count($tmp);
}

// Return scheduled post count. Matching $var
function get_scheduledcount($var)
{
    $posts = get_scheduled_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

        if (stripos($v['dirname'], '/' . $var . '/') !== false) {
            $tmp[] = $v;
        }
        
    }
    
    return count($tmp);
    
}

// Return tag count. Matching $var
function get_tagcount($var)
{
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
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
    $posts = get_blog_posts();

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

    if (!is_dir($dir)) {
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
            echo '<li>' . i18n('No_posts_found') . '</li>';
        }
        echo '</ul>';
    }
}

// Return recent type lists
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

    if (!is_dir($dir)) {
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
                            if (stripos($key, '/blog/') !== false && stripos($key, '/scheduled/') == false && stripos($key, '/draft/') == false) {
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

                    if (!is_dir($dir)) {
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

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $posts = get_blog_posts();
    $by_year = array();
    $col = array();

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v['filename']);
                
                $date = $arr[0];
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
                    $name = format_date(mktime(0, 0, 0, $month, 1, 2010), 'F');
                    echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '-' . $month . '">' . $name . '</a>';
                    echo ' <span class="count">(' . $count . ')</span></li>';
                }

                echo '</ul>';
                echo '</li>';
                echo '</ul>';
            }
        } elseif ($custom === 'month-year') {
            foreach ($by_year as $year => $months) {
                $by_month = array_count_values($months);
                # Sort the months
                krsort($by_month);
                foreach ($by_month as $month => $count) {
                $name = format_date(mktime(0, 0, 0, $month, 1, 2010), 'F');
                echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '-' . $month . '">' . $name . ' ' . $year .'</a> ('.$count.')</li>';
                }
            }
        } elseif ($custom === 'year') {
            foreach ($by_year as $year => $months) {
                $by_month = array_count_values($months);
                # Sort the months
                krsort($by_month);
                echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '">' . $year .'</a> ('. count($months) .')</li>';
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

    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $posts = get_blog_posts();
    $tags = array();
    
    $tagcloud_count = config('tagcloud.count');
    if(empty($tagcloud_count)) {
        $tagcloud_count = 40;
    }

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {
                $arr = explode('_', $v['filename']);
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


            arsort($tag_collection);
            $sliced_tags = array_slice($tag_collection, 0, $tagcloud_count, true);
            ksort($sliced_tags);
            foreach ($sliced_tags as $tag => $count) {
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
            'slug' => $prev->slug,
            'category' => $prev->category,
            'categoryUrl' => $prev->categoryUrl,
            'categoryCount' => $prev->categoryCount,
            'categorySlug' => $prev->categorySlug,
            'categoryMd' => $prev->categoryMd,
            'categoryTitle' => $prev->categoryTitle,
            'readTime' => $prev->readTime,
            'lastMod' => $prev->lastMod
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
            'slug' => $next->slug,
            'category' => $next->category,
            'categoryUrl' => $next->categoryUrl,
            'categoryCount' => $next->categoryCount,
            'categorySlug' => $next->categorySlug,
            'categoryMd' => $next->categoryMd,
            'categoryTitle' => $next->categoryTitle,
            'readTime' => $next->readTime,
            'lastMod' => $next->lastMod
        );
    }
}

function static_prev($prev) 
{
    if (!empty($prev)) {
        return array(
            'url' => $prev->url,
            'title' => $prev->title,
            'body' => $prev->body,
            'description' => $prev->description,
            'views' => $prev->views,
            'md' => $prev->md,
            'slug' => $prev->slug,
            'parent' => $prev->parent,
            'file' => $prev->file,
            'readTime' => $prev->readTime,
            'lastMod' => $prev->lastMod
        );
    }    
}

function static_next($next) 
{
    if (!empty($next)) {
        return array(
            'url' => $next->url,
            'title' => $next->title,
            'body' => $next->body,
            'description' => $next->description,
            'views' => $next->views,
            'md' => $next->md,
            'slug' => $next->slug,
            'parent' => $next->parent,
            'file' => $next->file,
            'readTime' => $next->readTime,
            'lastMod' => $next->lastMod
        );
    }    
}

// Helper function to determine whether
// to show the pagination buttons
function has_pagination($total, $perpage, $page = 1)
{
    if (!$total) {
        $total = count(get_blog_posts());
    }
    $totalPage = ceil($total / $perpage);
    $number = i18n('Page') . ' ' . $page . ' ' . i18n('of') . ' ' . $totalPage;
    $pager = get_pagination($total, $page, $perpage, 2);
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
function get_pagination($totalitems, $page = 1, $perpage = 10, $adjacents = 1, $pagestring = '?page=')
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

        //newer button
        if ($page > 1)
            $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $prev .'">« '. i18n('Newer') .'</a></li>';
        else
            $pagination .= '<li class="page-item disabled"><span class="page-link">« '. i18n('Newer') . '</span></li>';

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

        //older button
        if ($page < $counter - 1)
            $pagination .= '<li class="page-item"><a class="page-link" href="'. $pagestring . $next .'">'. i18n('Older') .' »</a></li>';
        else
            $pagination .= '<li class="page-item disabled"><span class="page-link">'. i18n('Older') .' »</span></li>';
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
            $dom->loadHtml('<meta charset="utf8">' . $text);
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
    $dom->loadHtml('<meta charset="utf8">' . $text);
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
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (isset($p->author)) {
        if ($user === $p->author || $role === 'admin') {
            echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">' . i18n('View') .'</a></li><li><a href="' . $p->url . '/edit?destination=post">'. i18n('Edit') .'</a></li></ul></div>';
        }
    } else {
        if ($p->url) {
            echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">' . i18n('View') .'</a></li><li><a href="' . $p->url . '/edit?destination=post">'. i18n('Edit') .'</a></li></ul></div>';
        }
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
            $doc->loadHTML('<meta charset="utf8">' . $html);

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

        return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', mb_convert_encoding($doc->saveHTML($doc->documentElement), 'UTF-8'));

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

    $filename= pathinfo($v, PATHINFO_FILENAME);

    // Extract the title and body
    return get_content_tag('t', $content, str_replace('-', ' ', $filename));
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

            // Filename string
            $filename= $v['filename'];
            $url = site_url() . $filename;
            $parent_file = $v['dirname'] . '/' . $v['basename'];

            $title = get_title_from_file($parent_file);

            if ($req == site_path() . "/" . $filename || stripos($req, site_path() . "/" . $filename) !== false) {
                $active = ' active';
                $reqBase = '';
            } else {
                $active = '';
            }

            $subPages = get_static_subpages($filename);
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

                    $baseSub= $sp['filename'];
                    $child_file = $sp['dirname'] . '/' . $sp['basename'];
                    if ($req == site_path() . "/" . $filename . "/" . $baseSub) {
                        $classSub .= ' active';
                    }
                    $urlSub = $url . "/" . $baseSub;
                    echo '<li class="' . $classSub . '"><a href="' . $urlSub . '">' . get_title_from_file($child_file) . '</a></li>';
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
    if (!config('views.root')) die('HTMLy is not installed!');
    $vroot = rtrim(config('views.root'), '/');
    $lt = $vroot . '/layout--404.html.php';
    if (file_exists($lt)) {
        $layout = 'layout--404';
    } else {
        $layout = '';
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    render('404', array(
        'title' => i18n('This_page_doesnt_exist') . ' - ' . blog_title(),
        'description' => i18n('This_page_doesnt_exist'),
        'canonical' => site_url(),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('This_page_doesnt_exist'),
        'bodyclass' => 'error-404',
        'is_404' => true,
    ), $layout);
    die();
}

// Turn an array of posts into an RSS feed
function generate_rss($posts, $data = null)
{
    $feed = new Feed();
    $channel = new Channel();
    $rssLength = config('rss.char');
    $data = $data;
    
    if (is_null($data)) {
    $channel
        ->title(blog_title())
        ->description(blog_description())
        ->url(site_url())
        ->appendTo($feed);
    } else {
    $channel
        ->title($data->title)
        ->description(strip_tags($data->body))
        ->url($data->url)
        ->appendTo($feed);        
    }
    if ($posts) {
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
    }

    return $feed;
}

// Return post, archive url for sitemap
function sitemap_post_path()
{
    $posts = get_blog_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {

        $post = new stdClass;

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $v['basename']);

        // dirname string
        $dirname = $v['dirname'];

        // Author string
        $str = explode('/', $dirname);
        $author = $str[1];

        $post->authorUrl = site_url() . 'author/' . $author;

        $dt = str_replace($dirname, '', $arr[0]);
        $t = str_replace('-', '', $dt);
        $time = new DateTime($t);
        $timestamp = $time->format("Y-m-d H:i:s");

        // The post date
        $post->date = strtotime($timestamp);
        $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($filepath)));

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

            // Filename
            $filename= $v['filename'];
            $file = $v['dirname'] . '/' . $v['basename'];
            $post->url = site_url() . $filename;
            $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($file)));

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

    if ($str == 'index.xml') {

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

    } elseif ($str == 'base.xml') {

        $priority = (config('sitemap.priority.base')) ? config('sitemap.priority.base') : '1.0';

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($priority !== 'false') {
            echo '<url><loc>' . site_url() . '</loc><priority>' . $priority . '</priority></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'post.xml') {

        $priority = (config('sitemap.priority.post')) ? config('sitemap.priority.post') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = sitemap_post_path();
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($posts as $p) {

            echo '<url><loc>' . $p->url . '</loc><priority>' . $priority . '</priority><lastmod>' . date('Y-m-d\TH:i:sP', $p->lastMod) . '</lastmod></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'static.xml') {

        $priority = (config('sitemap.priority.static')) ? config('sitemap.priority.static') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = sitemap_page_path();
        }

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($posts as $p) {

            echo '<url><loc>' . $p->url . '</loc><priority>' . $priority . '</priority><lastmod>' . date('Y-m-d\TH:i:sP', $p->lastMod) . '</lastmod></url>';
        }

        echo '</urlset>';

    } elseif ($str == 'tag.xml') {

        $priority = (config('sitemap.priority.tag')) ? config('sitemap.priority.tag') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_blog_posts();
        }

        $tags = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $arr = explode('_', $v['filename']);
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

    } elseif ($str == 'archive.xml') {

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

    } elseif ($str == 'author.xml') {

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

    } elseif ($str == 'category.xml') {

        $priority = (config('sitemap.priority.category')) ? config('sitemap.priority.category') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_blog_posts();
        }

        $cats = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $dirname = $v['dirname'];
                $str = explode('/', $dirname);
                $cats[] = $str[3];

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

    } elseif ($str == 'type.xml') {

        $priority = (config('sitemap.priority.type')) ? config('sitemap.priority.type') : $default_priority;

        $posts = array();
        if ($priority !== 'false') {
            $posts = get_blog_posts();
        }

        $cats = array();

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if($posts) {
            foreach ($posts as $index => $v) {

                $dirname = $v['dirname'];
                $str = explode('/', $dirname);
                $types[] = $str[4];
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

// Return blog language
function blog_language()
{
    return str_replace('_', '-', config('language'));
}

// Output head contents
function head_contents()
{
    $output = '';
    $wmt_id = config('google.wmt.id');
    $version = 'HTMLy ' . constant('HTMLY_VERSION');

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

// File cache
function file_cache($request)
{
    if (config('cache.off') == 'true') return;
    $hour = config('cache.expiration');
    if (empty($hour)) {
        $hour = 6;
    }

    $now   = time();
    $c = str_replace('/', '#', str_replace('?', '~', rawurldecode($request)));
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
    $patterns = array('/(\s|)<!--t(.*)t-->(\s|)/', '/(\s|)<!--d(.*)d-->(\s|)/', '/(\s|)<!--tag(.*)tag-->(\s|)/', '/(\s|)<!--image(.*)image-->(\s|)/', '/(\s|)<!--video(.*)video-->(\s|)/', '/(\s|)<!--audio(.*)audio-->(\s|)/', '/(\s|)<!--link(.*)link-->(\s|)/', '/(\s|)<!--quote(.*)quote-->(\s|)/', '/(\s|)<!--post(.*)post-->(\s|)/');
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

// Replace href
function replace_href($string, $tag, $class, $url)
{

    libxml_use_internal_errors(true);

    // Load the HTML in DOM
    $doc = new DOMDocument();
    $doc->loadHTML('<meta charset="utf8">' . $string);
    // Then select all anchor tags
    $all_anchor_tags = $doc->getElementsByTagName($tag);
    foreach ($all_anchor_tags as $_tag) {
        if ($_tag->getAttribute('class') == $class) {
            // If match class get the href value
            $old = $_tag->getAttribute('href');
            $new = $_tag->setAttribute('href', $url . mb_convert_encoding($old, 'UTF-8'));
        }
    }

    return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', mb_convert_encoding($doc->saveHTML($doc->documentElement), 'UTF-8'));

}

// Format the date
function format_date($date, $dateFormat = null)
{
    if (empty($dateFormat)) {
        $dateFormat = config('date.format');
    }
    if (extension_loaded('intl')) {
        $format_map = array('s' => 'ss', 'i' => 'mm', 'H' => 'HH', 'G' => 'H', 'd' => 'dd', 'j' => 'd', 'D' => 'EE', 'l' => 'EEEE', 'm' => 'MM', 'M' => 'MMM', 'F' => 'MMMM', 'Y' => 'yyyy');
        $intlFormat = strtr($dateFormat, $format_map);
        $formatter = new IntlDateFormatter(config('language'), IntlDateFormatter::NONE, IntlDateFormatter::NONE, config('timezone'), IntlDateFormatter::GREGORIAN, $intlFormat);
        return $formatter->format($date); 
    } else {
        return date($dateFormat, $date);
    }
}

// Publish scheduled post
function publish_scheduled() 
{
    $posts = get_scheduled_posts();
    if (!empty($posts)) {
        foreach ($posts as $index => $v) {
            $str = explode('_', $v['basename']);
            $old =  $v['dirname'] . '/' . $v['basename'];
            $new = dirname($v['dirname']) . '/' . $v['basename'];
            $t = str_replace('-', '', $str[0]);
            $time = new DateTime($t);
            $timestamp = $time->format("Y m d H:i:s");
            if (date('Y m d H:i:s') >= $timestamp) {
                rename($old, $new);
                rebuilt_cache('all');
                clear_cache();
            }
        }
    }
}
