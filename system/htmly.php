<?php

// Change this to your timezone
date_default_timezone_set('Asia/Jakarta');

// Explicitly including the dispatch framework,
// and our functions.php file
require 'system/includes/dispatch.php';
require 'system/includes/updater.php';
require 'system/includes/functions.php';
require 'system/admin/admin.php';
require 'system/includes/session.php';
include 'system/includes/opml.php';

// Load the configuration file
config('source', 'config/config.ini');

// The front page of the blog.
// This will match the root url
get('/index', function () {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('posts.perpage');

    $posts = get_posts(null, $page, $perpage);

    $total = '';

    $tl = blog_tagline();

    if ($tl) {
        $tagline = ' - ' . $tl;
    } else {
        $tagline = '';
    }

    if (empty($posts) || $page < 1) {

        // a non-existing page
        render('no-posts', array(
            'head_contents' => head_contents(blog_title() . $tagline, blog_description(), site_url()),
            'bodyclass' => 'noposts',
        ));

        die;
    }

    render('main', array(
        'head_contents' => head_contents(blog_title() . $tagline, blog_description(), site_url()),
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'infront',
        'breadcrumb' => '',
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// Get submitted login data
post('/login', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $user = from($_REQUEST, 'user');
    $pass = from($_REQUEST, 'password');
    if ($proper && !empty($user) && !empty($pass)) {

        session($user, $pass, null);
        $log = session($user, $pass, null);

        if (!empty($log)) {

            config('views.root', 'system/admin/views');

            render('login', array(
                'head_contents' => head_contents('Login - ' . blog_title(), 'Login page on ' . blog_title(), site_url()),
                'error' => '<ul>' . $log . '</ul>',
                'bodyclass' => 'editprofile',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
            ));
        }
    } else {
        $message['error'] = '';
        if (empty($user)) {
            $message['error'] .= '<li>User field is required.</li>';
        }
        if (empty($pass)) {
            $message['error'] .= '<li>Password field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }

        config('views.root', 'system/admin/views');

        render('login', array(
            'head_contents' => head_contents('Login - ' . blog_title(), 'Login page on ' . blog_title(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'username' => $user,
            'password' => $pass,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// The blog post page
get('/:year/:month/:name', function($year, $month, $name) {


    $post = find_post($year, $month, $name);

    $current = $post['current'];

    if (!$current) {
        not_found();
    }
    
    add_view($current->file);
    
    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $bio = get_bio($current->author);

    if (isset($bio[0])) {
        $bio = $bio[0];
    } else {
        $bio = default_profile($current->author);
    }

    if (array_key_exists('prev', $post)) {
        $prev = $post['prev'];
    } else {
        $prev = array();
    }

    if (array_key_exists('next', $post)) {
        $next = $post['next'];
    } else {
        $next = array();
    }

    render('post', array(
        'head_contents' => head_contents($current->title . ' - ' . blog_title(), $description = get_description($current->body), $current->url),
        'p' => $current,
        'authorinfo' => authorinfo($bio->title, $bio->body),
        'bodyclass' => 'inpost',
        'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title,
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => 'blogpost',
    ));
});

// Edit blog post
get('/:year/:month/:name/edit', function($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            not_found();
        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'admin') {
            render('edit-post', array(
                'head_contents' => head_contents('Edit post - ' . blog_title(), blog_description(), site_url()),
                'p' => $current,
                'bodyclass' => 'editpost',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'head_contents' => head_contents('Edit post - ' . blog_title(), blog_description(), site_url()),
                'p' => $current,
                'bodyclass' => 'denied',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data for blog post
post('/:year/:month/:name/edit', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
        if (!empty($url)) {
            edit_post($title, $tag, $url, $content, $oldfile, $destination);
        } else {
            $url = $title;
            edit_post($title, $tag, $url, $content, $oldfile, $destination);
        }
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($tag)) {
            $message['error'] .= '<li>Tag field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'system/admin/views');

        render('edit-post', array(
            'head_contents' => head_contents('Edit post - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postTag' => $tag,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'editpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit post'
        ));
    }
});

// Delete blog post
get('/:year/:month/:name/delete', function($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            not_found();
        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'admin') {
            render('delete-post', array(
                'head_contents' => head_contents('Delete post - ' . blog_title(), blog_description(), site_url()),
                'p' => $current,
                'bodyclass' => 'deletepost',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'head_contents' => head_contents('Delete post - ' . blog_title(), blog_description(), site_url()),
                'p' => $current,
                'bodyclass' => 'deletepost',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted data for blog post
post('/:year/:month/:name/delete', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_post($file, $destination);
    }
});

// The author page
get('/author/:profile', function($profile) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('profile.perpage');

    $posts = get_profile($profile, $page, $perpage);

    $total = get_count($profile, 'dirname');

    $bio = get_bio($profile);

    if (isset($bio[0])) {
        $bio = $bio[0];
    } else {
        $bio = default_profile($profile);
    }

    if (empty($posts) || $page < 1) {
        render('profile', array(
            'head_contents' => head_contents('Profile for:  ' . $bio->title . ' - ' . blog_title(), 'Profile page and all posts by ' . $bio->title . ' on ' . blog_title() . '.', site_url() . 'author/' . $profile),
            'page' => $page,
            'posts' => null,
            'bio' => $bio->body,
            'name' => $bio->title,
            'bodyclass' => 'inprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $bio->title,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
        die;
    }

    render('profile', array(
        'head_contents' => head_contents('Profile for:  ' . $bio->title . ' - ' . blog_title(), 'Profile page and all posts by ' . $bio->title . ' on ' . blog_title() . '.', site_url() . 'author/' . $profile),
        'page' => $page,
        'posts' => $posts,
        'bio' => $bio->body,
        'name' => $bio->title,
        'bodyclass' => 'inprofile',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $bio->title,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// Edit the profile
get('/edit/profile', function() {

    if (login()) {

        config('views.root', 'system/admin/views');
        render('edit-profile', array(
            'head_contents' => head_contents('Edit profile - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data for static page
post('/edit/profile', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $user = $_SESSION[config("site.url")]['user'];
    $title = from($_REQUEST, 'title');
    $content = from($_REQUEST, 'content');
    if ($proper && !empty($title) && !empty($content)) {
        edit_profile($title, $content, $user);
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'system/admin/views');

        render('edit-profile', array(
            'head_contents' => head_contents('Edit profile - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile'
        ));
    }
});

get('/admin/posts', function () {

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (login()) {

        config('views.root', 'system/admin/views');
        if ($role === 'admin') {

            config('views.root', 'system/admin/views');
            $page = from($_GET, 'page');
            $page = $page ? (int) $page : 1;
            $perpage = 20;

            $posts = get_posts(null, $page, $perpage);

            $total = '';

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'head_contents' => head_contents('All blog posts - ' . blog_title(), blog_description(), site_url()),
                    'bodyclass' => 'noposts',
                ));

                die;
            }

            $tl = blog_tagline();

            if ($tl) {
                $tagline = ' - ' . $tl;
            } else {
                $tagline = '';
            }

            render('posts-list', array(
                'head_contents' => head_contents('All blog posts - ' . blog_title(), blog_description(), site_url()),
                'heading' => 'All blog posts',
                'page' => $page,
                'posts' => $posts,
                'bodyclass' => 'all-posts',
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'head_contents' => head_contents('All blog posts - ' . blog_title(), blog_description(), site_url()),
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// The author page
get('/admin/mine', function() {

    if (login()) {

        config('views.root', 'system/admin/views');

        $profile = $_SESSION[config("site.url")]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int) $page : 1;
        $perpage = config('profile.perpage');

        $posts = get_profile($profile, $page, $perpage);

        $total = get_count($profile, 'dirname');

        $bio = get_bio($profile);

        if (isset($bio[0])) {
            $bio = $bio[0];
        } else {
            $bio = default_profile($profile);
        }

        if (empty($posts) || $page < 1) {
            render('user-posts', array(
                'head_contents' => head_contents('My blog posts - ' . blog_title(), blog_description(), site_url()),
                'page' => $page,
                'heading' => 'My posts',
                'posts' => null,
                'bio' => $bio->body,
                'name' => $bio->title,
                'bodyclass' => 'userposts',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $bio->title,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('user-posts', array(
            'head_contents' => head_contents('My blog posts - ' . blog_title(), blog_description(), site_url()),
            'heading' => 'My posts',
            'page' => $page,
            'posts' => $posts,
            'bio' => $bio->body,
            'name' => $bio->title,
            'bodyclass' => 'userposts',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $bio->title,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// The static page
get('/:static', function($static) {

    if ($static === 'sitemap.xml' || $static === 'sitemap.base.xml' || $static === 'sitemap.post.xml' || $static === 'sitemap.static.xml' || $static === 'sitemap.tag.xml' || $static === 'sitemap.archive.xml' || $static === 'sitemap.author.xml') {

        header('Content-Type: text/xml');

        if ($static === 'sitemap.xml') {
            generate_sitemap('index');
        } else if ($static === 'sitemap.base.xml') {
            generate_sitemap('base');
        } else if ($static === 'sitemap.post.xml') {
            generate_sitemap('post');
        } else if ($static === 'sitemap.static.xml') {
            generate_sitemap('static');
        } else if ($static === 'sitemap.tag.xml') {
            generate_sitemap('tag');
        } else if ($static === 'sitemap.archive.xml') {
            generate_sitemap('archive');
        } else if ($static === 'sitemap.author.xml') {
            generate_sitemap('author');
        }

        die;
    } elseif ($static === 'admin') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('main', array(
                'head_contents' => head_contents('Admin - ' . blog_title(), blog_description(), site_url()),
                'bodyclass' => 'adminfront',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Admin'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === 'login') {
        config('views.root', 'system/admin/views');
        render('login', array(
            'head_contents' => head_contents('Login - ' . blog_title(), 'Login page from ' . blog_title() . '.', site_url() . '/login'),
            'bodyclass' => 'inlogin',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
        die;
    } elseif ($static === 'logout') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('logout', array(
                'head_contents' => head_contents('Logout - ' . blog_title(), blog_description(), site_url()),
                'bodyclass' => 'inlogout',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Logout'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } else {
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];
        
        add_view($post->file);

        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }

        render('static', array(
            'head_contents' => head_contents($post->title . ' - ' . blog_title(), $description = get_description($post->body), $post->url),
            'bodyclass' => 'inpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticpage',
        ));
    }
});

// Edit the static page
get('/:static/edit', function($static) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('edit-page', array(
            'head_contents' => head_contents('Edit page - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'editpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticpage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data for static page
post('/:static/edit', function() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            edit_page($title, $url, $content, $oldfile, $destination);
        } else {
            $url = $title;
            edit_page($title, $url, $content, $oldfile, $destination);
        }
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'system/admin/views');

        render('edit-page', array(
            'head_contents' => head_contents('Edit page - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'editpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit page'
        ));
    }
});

// Deleted the static page
get('/:static/delete', function($static) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('delete-page', array(
            'head_contents' => head_contents('Delete page - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'deletepage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticpage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted data for static page
post('/:static/delete', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
});

// Add blog post
get('/add/post', function() {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-post', array(
            'head_contents' => head_contents('Add post - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'addpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted blog post data
post('/add/post', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $user = $_SESSION[config("site.url")]['user'];
    if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
        if (!empty($url)) {
            add_post($title, $tag, $url, $content, $user);
        } else {
            $url = $title;
            add_post($title, $tag, $url, $content, $user);
        }
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($tag)) {
            $message['error'] .= '<li>Tag field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'system/admin/views');
        render('add-post', array(
            'head_contents' => head_contents('Add post - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postTag' => $tag,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'addpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    }
});

// Add the static page
get('/add/page', function() {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-page', array(
            'head_contents' => head_contents('Add page - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted static page data
post('/add/page', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            add_page($title, $url, $content);
        } else {
            $url = $title;
            add_page($title, $url, $content);
        }
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'system/admin/views');
        render('add-page', array(
            'head_contents' => head_contents('Add page - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    }
});

// Import page
get('/admin/import', function() {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('import', array(
            'head_contents' => head_contents('Import feed - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'importfeed',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Import feed'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Get import post
post('/admin/import', function() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $url = from($_REQUEST, 'url');
    $credit = from($_REQUEST, 'credit');
    if (!empty($url)) {

        get_feed($url, $credit, null);
        $log = get_feed($url, $credit, null);

        if (!empty($log)) {

            config('views.root', 'system/admin/views');

            render('import', array(
                'head_contents' => head_contents('Import feed - ' . blog_title(), blog_description(), site_url()),
                'error' => '<ul>' . $log . '</ul>',
                'bodyclass' => 'editprofile',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Import feed'
            ));
        }
    } else {
        $message['error'] = '';
        if (empty($url)) {
            $message['error'] .= '<li>You need to specify the feed url.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }

        config('views.root', 'system/admin/views');

        render('import', array(
            'head_contents' => head_contents('Import feed - ' . blog_title(), blog_description(), site_url()),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'url' => $url,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Backup page
get('/admin/backup', function() {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('backup', array(
            'head_contents' => head_contents('Backup content - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'backup',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Backup'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Create Zip file
get('/admin/backup-start', function() {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('backup-start', array(
            'head_contents' => head_contents('Backup content started - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'startbackup',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Backup started'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Delete all cache
get('/admin/clear-cache', function() {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('clear-cache', array(
            'head_contents' => head_contents('Clearing cache started - ' . blog_title(), blog_description(), site_url()),
            'bodyclass' => 'clearcache',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Clearing cache started'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});


// The tag page
get('/tag/:tag', function($tag) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('tag.perpage');

    $posts = get_tag($tag, $page, $perpage, false);

    $total = get_count($tag, 'filename');

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }

    render('main', array(
        'head_contents' => head_contents('Posts tagged: ' . $tag . ' - ' . blog_title(), 'All posts tagged: ' . $tag . ' on ' . blog_title() . '.', site_url() . 'tag/' . $tag),
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'intag',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Posts tagged: ' . $tag,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// The archive page
get('/archive/:req', function($req) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('archive.perpage');

    $posts = get_archive($req, $page, $perpage);

    $total = get_count($req, 'filename');

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }

    $time = explode('-', $req);
    $date = strtotime($req);

    if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
        $timestamp = date('d F Y', $date);
    } else if (isset($time[0]) && isset($time[1])) {
        $timestamp = date('F Y', $date);
    } else {
        $timestamp = $req;
    }

    if (!$date) {
        // a non-existing page
        not_found();
    }

    render('main', array(
        'head_contents' => head_contents('Archive for: ' . $timestamp . ' - ' . blog_title(), 'Archive page for: ' . $timestamp . ' on ' . blog_title() . '.', site_url() . 'archive/' . $req),
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'inarchive',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Archive for: ' . $timestamp,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// The search page
get('/search/:keyword', function($keyword) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('search.perpage');

    $posts = get_keyword($keyword, $page, $perpage);

    $total = keyword_count($keyword);

    if (empty($posts) || $page < 1) {
        // a non-existing page
        render('404-search', null, false);
        die;
    }

    render('main', array(
        'head_contents' => head_contents('Search results for: ' . $keyword . ' - ' . blog_title(), 'Search results for: ' . $keyword . ' on ' . blog_title() . '.', site_url() . 'search/' . $keyword),
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'insearch',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Search results for: ' . $keyword,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// The JSON API
get('/api/json', function() {

    header('Content-type: application/json');

    // Print the 10 latest posts as JSON
    echo generate_json(get_posts(null, 1, config('json.count')));
});

// Show the RSS feed
get('/feed/rss', function() {

    header('Content-Type: application/rss+xml');

    // Show an RSS feed with the 30 latest posts
    echo generate_rss(get_posts(null, 1, config('rss.count')));
});

// Generate OPML file
get('/feed/opml', function() {

    header('Content-Type: text/xml');

    // Generate OPML file for the RSS
    echo generate_opml();
});

get('/admin/update/now/:csrf', function($CSRF) {

    $proper = is_csrf_proper($CSRF);
    $updater = new Updater;
    if (login() && $proper && $updater->updateAble()) {
        $updater->update();
        config('views.root', 'system/admin/views');
        render('updated-to', array(
            'head_contents' => head_contents('Updated - ' . blog_title(), blog_description(), site_url()),
            'updater' => $updater,
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});


get('/:static/:sub', function($static,$sub) {
    
    $father_post = get_static_post($static);
    if (!$father_post) {
        not_found();
    }
    $post = get_static_sub_post($static,$sub);
    if (!$post) {
        not_found();
    }
    $post = $post[0];
    
    add_view($post->file);

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    render('static-sub', array(
        'head_contents' => head_contents($post->title . ' - ' . blog_title(), $description = get_description($post->body), $post->url),
        'bodyclass' => 'inpage',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $father_post[0]->url . '">' . $father_post[0]->title . '</a> &#187; ' . $post->title,
        'p' => $post,
        'type' => 'staticpage',
    ));
});

// If we get here, it means that
// nothing has been matched above

get('.*', function() {
    not_found();
});

// Serve the blog
dispatch();
