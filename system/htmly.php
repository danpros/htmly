<?php

// Load the configuration file
config('source', $config_file);

// Set the timezone
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

// The front page of the blog
get('/index', function () {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
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
            'title' => blog_title() . $tagline,
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'noposts',
        ));

        die;
    }

    render('main', array(
        'title' => blog_title() . $tagline,
        'description' => blog_description(),
        'canonical' => site_url(),
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'infront',
        'breadcrumb' => '',
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// Get submitted login data
post('/login', function () {

    $proper = (is_csrf_proper(from($_REQUEST, 'csrf_token')));
    $captcha = isCaptcha(from($_REQUEST, 'g-recaptcha-response'));

    $user = from($_REQUEST, 'user');
    $pass = from($_REQUEST, 'password');
    if ($proper && $captcha && !empty($user) && !empty($pass)) {

        session($user, $pass);
        $log = session($user, $pass);

        if (!empty($log)) {

            config('views.root', 'system/admin/views');

            render('login', array(
                'title' => 'Login - ' . blog_title(),
                'description' => 'Login page on ' . blog_title(),
                'canonical' => site_url(),
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
        if (!$captcha) {
            $message['error'] .= '<li>reCaptcha not correct.</li>';
        }

        config('views.root', 'system/admin/views');

        render('login', array(
            'title' => 'Login - ' . blog_title(),
            'description' => 'Login page on ' . blog_title(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'username' => $user,
            'password' => $pass,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Show the author page
get('/author/:profile', function ($profile) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
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
            'title' => 'Profile for:  ' . $bio->title . ' - ' . blog_title(),
            'description' => 'Profile page and all posts by ' . $bio->title . ' on ' . blog_title() . '.',
            'canonical' => site_url() . 'author/' . $profile,
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
        'title' => 'Profile for:  ' . $bio->title . ' - ' . blog_title(),
        'description' => 'Profile page and all posts by ' . $bio->title . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'author/' . $profile,
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
get('/edit/profile', function () {

    if (login()) {

        config('views.root', 'system/admin/views');
        render('edit-profile', array(
            'title' => 'Edit profile - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted data from edit profile page
post('/edit/profile', function () {

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
            'title' => 'Edit profile - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile'
        ));
    }
});

// Show the "Add post" page
get('/add/post', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-post', array(
            'title' => 'Add post - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'addpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted add post data
post('/add/post', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $img = from($_REQUEST, 'img');
    $vid = from($_REQUEST, 'vid');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $user = $_SESSION[config("site.url")]['user'];
	$draft = from($_REQUEST, 'draft');
    if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
        if (!empty($url)) {
            add_post($title, $tag, $url, $content, $user, $description, $img, $vid, $draft);
        } else {
            $url = $title;
            add_post($title, $tag, $url, $content, $user, $description, $img, $vid, $draft);
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
            'title' => 'Add post- ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postImg' => $img,
            'postVid' => $vid,
            'postTag' => $tag,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'addpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    }
});

// Show the static add page
get('/add/page', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted static add page data
post('/add/page', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && login()) {
        if (!empty($url)) {
            add_page($title, $url, $content, $description);
        } else {
            $url = $title;
            add_page($title, $url, $content, $description);
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
            'title' => 'Add page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    }
});

// Show admin/posts 
get('/admin/posts', function () {

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (login()) {

        config('views.root', 'system/admin/views');
        if ($role === 'admin') {

            config('views.root', 'system/admin/views');
            $page = from($_GET, 'page');
            $page = $page ? (int)$page : 1;
            $perpage = 20;

            $posts = get_posts(null, $page, $perpage);

            $total = '';

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'title' => 'All blog posts - ' . blog_title(),
                    'description' => blog_description(),
                    'canonical' => site_url(),
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
                'title' => 'All blog posts - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'heading' => 'All blog posts',
                'page' => $page,
                'posts' => $posts,
                'bodyclass' => 'all-posts',
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => 'All blog posts - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/mine
get('/admin/mine', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        $profile = $_SESSION[config("site.url")]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
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
                'title' => 'My blog posts - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
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
            'title' => 'My blog posts - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
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

// Show admin/draft
get('/admin/draft', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        $profile = $_SESSION[config("site.url")]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_draft($profile, $page, $perpage);

        $total = get_count($profile, 'dirname');

        $bio = get_bio($profile);

        if (isset($bio[0])) {
            $bio = $bio[0];
        } else {
            $bio = default_profile($profile);
        }

        if (empty($posts) || $page < 1) {
            render('user-draft', array(
                'title' => 'My draft - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'page' => $page,
                'heading' => 'My draft',
                'posts' => null,
                'bio' => $bio->body,
                'name' => $bio->title,
                'bodyclass' => 'userdraft',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $bio->title,
            ));
            die;
        }

        render('user-draft', array(
            'title' => 'My draft - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'heading' => 'My draft',
            'page' => $page,
            'posts' => $posts,
            'bio' => $bio->body,
            'name' => $bio->title,
            'bodyclass' => 'userdraft',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $bio->title,
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show import page
get('/admin/import', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('import', array(
            'title' => 'Import feed - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'importfeed',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Import feed'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted import page data
post('/admin/import', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $url = from($_REQUEST, 'url');
    $credit = from($_REQUEST, 'credit');
    if (login() && !empty($url) && $proper) {

        get_feed($url, $credit);
        $log = get_feed($url, $credit);

        if (!empty($log)) {

            config('views.root', 'system/admin/views');

            render('import', array(
                'title' => 'Import feed - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
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
            'title' => 'Import feed - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'url' => $url,
            'bodyclass' => 'editprofile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Show Config page
get('/admin/config', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('config', array(
            'title' => 'Config - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'config',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Config'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted Config page data
post('/admin/config', function () {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $newKey = from($_REQUEST, 'newKey');
        $newValue = from($_REQUEST, 'newValue');

        $new_config = array();
        $new_Keys = array();
        if (!empty($newKey)) {
            $new_Keys[$newKey] = $newValue;
        }
        foreach ($_POST as $name => $value) {
            if (substr($name, 0, 8) == "-config-") {
                $name = str_replace("_", ".", substr($name, 8));
                $new_config[$name] = $value;
            }
        }
        save_config($new_config, $new_Keys);
        $login = site_url() . 'admin/config';
        header("location: $login");
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show Backup page
get('/admin/backup', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('backup', array(
            'title' => 'Backup content - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'backup',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Backup'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show Create backup page
get('/admin/backup-start', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('backup-start', array(
            'title' => 'Backup content started - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'startbackup',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Backup started'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show clear cache page
get('/admin/clear-cache', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('clear-cache', array(
            'title' => 'Clearing cache started - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'clearcache',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Clearing cache started'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show Update page
get('/admin/update', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('update', array(
            'title' => 'Check for Update - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'updatepage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Update HTMLy'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show the update now link
get('/admin/update/now/:csrf', function ($CSRF) {

    $proper = is_csrf_proper($CSRF);
    $updater = new \Kanti\HubUpdater(array(
        'name' => 'danpros/htmly',
        'prerelease' => !!config("prerelease"),
    ));
    if (login() && $proper && $updater->able()) {
        $updater->update();
        config('views.root', 'system/admin/views');
        render('updated-to', array(
            'title' => 'Updated - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'info' => $updater->getCurrentInfo(),
            'bodyclass' => 'updatepage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Update HTMLy'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show the tag page
get('/tag/:tag', function ($tag) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('tag.perpage');

    $posts = get_tag($tag, $page, $perpage, false);

    $total = get_count($tag, 'filename');

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }

    render('main', array(
        'title' => 'Posts tagged: ' . $tag . ' - ' . blog_title(),
        'description' => 'All posts tagged: ' . $tag . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'tag/' . $tag,
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'intag',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Posts tagged: ' . $tag,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// Show the archive page
get('/archive/:req', function ($req) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
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
    } elseif (isset($time[0]) && isset($time[1])) {
        $timestamp = date('F Y', $date);
    } else {
        $timestamp = $req;
    }

    if (!$date) {
        // a non-existing page
        not_found();
    }

    render('main', array(
        'title' => 'Archive for: ' . $timestamp . ' - ' . blog_title(),
        'description' => 'Archive page for: ' . $timestamp . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'archive/' . $req,
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'inarchive',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Archive for: ' . $timestamp,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// Show the search page
get('/search/:keyword', function ($keyword) {

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('search.perpage');

    $posts = get_keyword($keyword, $page, $perpage);

    $total = keyword_count($keyword);

    if (empty($posts) || $page < 1) {
        // a non-existing page
        render('404-search', null, false);
        die;
    }

    render('main', array(
        'title' => 'Search results for: ' . $keyword . ' - ' . blog_title(),
        'description' => 'Search results for: ' . $keyword . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'search/' . $keyword,
        'page' => $page,
        'posts' => $posts,
        'bodyclass' => 'insearch',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Search results for: ' . $keyword,
        'pagination' => has_pagination($total, $perpage, $page)
    ));
});

// The JSON API
get('/api/json', function () {

    header('Content-type: application/json');

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('json.count');

    echo generate_json(get_posts(null, $page, $perpage));
});

// Show the RSS feed
get('/feed/rss', function () {

    header('Content-Type: application/rss+xml');

    // Show an RSS feed with the 30 latest posts
    echo generate_rss(get_posts(null, 1, config('rss.count')));
});

// Generate OPML file
get('/feed/opml', function () {

    header('Content-Type: text/xml');

    // Generate OPML file for the RSS
    echo generate_opml();
});

// Show various page (top-level), admin, login, sitemap, static page.
get('/:static', function ($static) {

    if ($static === 'sitemap.xml' || $static === 'sitemap.base.xml' || $static === 'sitemap.post.xml' || $static === 'sitemap.static.xml' || $static === 'sitemap.tag.xml' || $static === 'sitemap.archive.xml' || $static === 'sitemap.author.xml') {

        header('Content-Type: text/xml');

        if ($static === 'sitemap.xml') {
            generate_sitemap('index');
        } elseif ($static === 'sitemap.base.xml') {
            generate_sitemap('base');
        } elseif ($static === 'sitemap.post.xml') {
            generate_sitemap('post');
        } elseif ($static === 'sitemap.static.xml') {
            generate_sitemap('static');
        } elseif ($static === 'sitemap.tag.xml') {
            generate_sitemap('tag');
        } elseif ($static === 'sitemap.archive.xml') {
            generate_sitemap('archive');
        } elseif ($static === 'sitemap.author.xml') {
            generate_sitemap('author');
        }

        die;
    } elseif ($static === 'admin') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('main', array(
                'title' => 'Admin - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
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
            'title' => 'Login - ' . blog_title(),
            'description' => 'Login page from ' . blog_title() . '.',
            'canonical' => site_url() . '/login',
            'bodyclass' => 'inlogin',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
        die;
    } elseif ($static === 'logout') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('logout', array(
                'title' => 'Logout - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'bodyclass' => 'inlogout',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Logout'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } else {

        if (config("views.counter") != "true") {
            if (!login()) {
                file_cache($_SERVER['REQUEST_URI']);
            }
        }

        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        if (config("views.counter") == "true") {
            add_view($post->file);
            if (!login()) {
                file_cache($_SERVER['REQUEST_URI']);
            }
        }

        render('static', array(
            'title' => $post->title . ' - ' . blog_title(),
            'description' => $post->description,
            'canonical' => $post->url,
            'bodyclass' => 'inpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticpage',
        ));
    }
});

// Show the add sub static page
get('/:static/add', function ($static) {

    if (login()) {

        config('views.root', 'system/admin/views');

        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a> Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from add sub static page
post('/:static/add', function ($static) {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && login()) {
        if (!empty($url)) {
            add_sub_page($title, $url, $content, $static, $description);
        } else {
            $url = $title;
            add_sub_page($title, $url, $content, $static, $description);
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
            'title' => 'Add page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'addpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a> Add page'
        ));
    }
});

// Show edit the static page
get('/:static/edit', function ($static) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
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

// Get edited data from static page
post('/:static/edit', function () {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            edit_page($title, $url, $content, $oldfile, $destination, $description);
        } else {
            $url = $title;
            edit_page($title, $url, $content, $oldfile, $destination, $description);
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
            'title' => 'Edit page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
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
get('/:static/delete', function ($static) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('delete-page', array(
            'title' => 'Delete page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
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
post('/:static/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
});

// Show the sb static page
get('/:static/:sub', function ($static, $sub) {

    $father_post = get_static_post($static);
    if (!$father_post) {
        not_found();
    }
    $post = get_static_sub_post($static, $sub);
    if (!$post) {
        not_found();
    }
    $post = $post[0];

    if (config("views.counter") == "true") {
        add_view($post->file);
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    render('static', array(
        'title' => $post->title . ' - ' . blog_title(),
        'description' => $post->description,
        'canonical' => $post->url,
        'bodyclass' => 'inpage',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $father_post[0]->url . '">' . $father_post[0]->title . '</a> &#187; ' . $post->title,
        'p' => $post,
        'type' => 'staticpage',
    ));
});

// Edit the sub static page
get('/:static/:sub/edit', function ($static, $sub) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        $page = get_static_sub_post($static, $sub);

        if (!$page) {
            not_found();
        }

        $page = $page[0];

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'editpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a> &#187; ',
            'p' => $page,
            'type' => 'staticpage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from edit sub static page
post('/:static/:sub/edit', function ($static, $sub) {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    if ($destination === null) {
        $destination = $static . "/" . $sub;
    }
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            edit_page($title, $url, $content, $oldfile, $destination, $description);
        } else {
            $url = $title;
            edit_page($title, $url, $content, $oldfile, $destination, $description);
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
            'title' => 'Edit page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
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

// Delete sub static page
get('/:static/:sub/delete', function ($static, $sub) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_static_post($static);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        $page = get_static_sub_post($static, $sub);

        if (!$page) {
            not_found();
        }

        $page = $page[0];

        render('delete-page', array(
            'title' => 'Delete page - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'bodyclass' => 'deletepage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a>' . $page->title,
            'p' => $page,
            'type' => 'staticpage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from delete sub static page
post('/:static/:sub/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
});

// Show blog post page
get('/:year/:month/:name', function ($year, $month, $name) {

    if (config("views.counter") != "true") {
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
    }

    $post = find_post($year, $month, $name);

    $current = $post['current'];

    if (!$current) {
        not_found();
    }

    if (config("views.counter") == "true") {
        add_view($current->file);

        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
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
        'title' => $current->title . ' - ' . blog_title(),
        'description' => $current->description,
        'canonical' => $current->url,
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
get('/:year/:month/:name/edit', function ($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            $post = find_draft($year, $month, $name);
            if (!$post) {
			    not_found();
            }
        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'admin') {
            render('edit-post', array(
                'title' => 'Edit post - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'editpost',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => 'Edit post - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
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

// Get edited data from blog post
post('/:year/:month/:name/edit', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $img = from($_REQUEST, 'img');
    $vid = from($_REQUEST, 'vid');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $date = from($_REQUEST, 'date');
    $time = from($_REQUEST, 'time');
    $dateTime = null;
	$revertPost = from($_REQUEST, 'revertpost');
	$publishDraft = from($_REQUEST, 'publishdraft');
    if ($date !== null && $time !== null) {
        $dateTime = $date . ' ' . $time;
    }

    if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_post($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $img, $vid, $revertPost, $publishDraft);
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
            'title' => 'Edit post - ' . blog_title(),
            'description' => blog_description(),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postImg' => $img,
            'postVid' => $vid,
            'postTag' => $tag,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'editpost',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit post'
        ));
    }
});

// Delete blog post
get('/:year/:month/:name/delete', function ($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            $post = find_draft($year, $month, $name);
            if (!$post) {
			    not_found();
            }
        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'admin') {
            render('delete-post', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'deletepost',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => blog_description(),
                'canonical' => site_url(),
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

// Get deleted data from blog post
post('/:year/:month/:name/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_post($file, $destination);
    }
});

// If we get here, it means that
// nothing has been matched above
get('.*', function () {
    not_found();
});

// Serve the blog
dispatch();
