<?php
if (!defined('HTMLY')) die('HTMLy');

use PragmaRX\Google2FA\Google2FA;

// Load the configuration file
config('source', $config_file);

// Load the language file
get_language();

// Set the timezone
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

// Publish scheduled post
publish_scheduled();

// The front page of the blog
get('/index', function () {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--front.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--front';
    } else {
        $layout = '';
    }
    
    if (config('static.frontpage') == 'true') {
        
        $front = get_frontpage();
        
        $pv = $vroot . '/static--front.html.php'; 
        if (file_exists($pv)) {
            $pview = 'static--front';
        } else {
            $pview = 'static';
        }
            
        render($pview, array(
            'title' => generate_title('is_front', null),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'p' => $front,
            'static' => $front,
            'type' => 'is_frontpage',
            'is_front' => true
        ), $layout);
        
        
    } else {
    
        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('posts.perpage');

        $posts = get_posts(null, $page, $perpage);

        $total = '';
        
        $pv = $vroot . '/main--front.html.php'; 
        if (file_exists($pv)) {
            $pview = 'main--front';
        } else {
            $pview = 'main';
        }

        if (empty($posts) || $page < 1) {

            // a non-existing page
            render('no-posts', array(
                'title' => generate_title('is_front', null),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'breadcrumb' => '',
                'bodyclass' => 'no-posts',
                'type' => 'is_frontpage',
                'is_front' => true
            ), $layout);

            die;
        }
        
        if ($page > 1) {
            $CanonicalPageNum = '?page=' . $page;
        } else {
            $CanonicalPageNum = NULL;
        }

        render($pview, array(
            'title' => generate_title('is_front', null),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url() . $CanonicalPageNum,
            'metatags' => generate_meta(null, null),
            'page' => $page,
            'posts' => $posts,
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'pagination' => has_pagination($total, $perpage, $page),
            'type' => 'is_frontpage',
            'is_front' => true
        ), $layout);
    
    }
});

// Get submitted login data
post('/login', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $captcha = config('login.protect.system');
    if (is_null($captcha) || $captcha === 'disabled') {
        $captcha = true;
    } elseif ($captcha === 'cloudflare') {
        $captcha = isTurnstile(from($_REQUEST, 'cf-turnstile-response'));
    } elseif ($captcha === 'google') {
        $captcha = isCaptcha(from($_REQUEST, 'g-recaptcha-response'));
    }

    $user = from($_REQUEST, 'user');
    $pass = from($_REQUEST, 'password');
    $mfa_secret = user('mfa_secret', $user);
    if ($proper && $captcha && !empty($user) && !empty($pass)) {    
        if (!is_null($mfa_secret) && $mfa_secret !== "disabled") {
            $mfacode = from($_REQUEST, 'mfacode');
            $google2fa = new Google2FA();
            if ($google2fa->verifyKey($mfa_secret, $mfacode, '1')) {
                session($user, $pass);
                $log = session($user, $pass);

                if (!empty($log)) {

                    config('views.root', 'system/admin/views');

                    render('login', array(
                        'title' => generate_title('is_default', i18n('Login')),
                        'description' => i18n('Login') . ' ' . blog_title(),
                        'canonical' => site_url(),
                        'metatags' => generate_meta(null, null),
                        'error' => '<ul>' . $log . '</ul>',
                        'type' => 'is_login',
                        'is_login' => true,
                        'bodyclass' => 'in-login',
                        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Login')
                    ));
                }
            } else {
                $message['error'] = '';
                $message['error'] .= '<li class="alert alert-danger">' . i18n('MFA_Error') . '</li>';
                config('views.root', 'system/admin/views');

                render('login', array(
                    'title' => generate_title('is_default', i18n('Login')),
                    'description' => i18n('Login') . ' ' . blog_title(),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $message['error'] . '</ul>',
                    'username' => $user,
                    'password' => $pass,
                    'type' => 'is_login',
                    'is_login' => true,
                    'bodyclass' => 'in-login',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Login')
                ));
            }
        } else {
            session($user, $pass);
            $log = session($user, $pass);

            if (!empty($log)) {

                config('views.root', 'system/admin/views');

                render('login', array(
                    'title' => generate_title('is_default', i18n('Login')),
                    'description' => i18n('Login') . ' ' . blog_title(),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $log . '</ul>',
                    'type' => 'is_login',
                    'is_login' => true,
                    'bodyclass' => 'in-login',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Login')
                ));
            }
        }
    } else {
        $message['error'] = '';
        if (empty($user)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('User_Error') . '</li>';
        }
        if (empty($pass)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('Pass_Error') . '</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
        }
        if (!$captcha) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('Captcha_Error') . '</li>';
        }

        config('views.root', 'system/admin/views');

        render('login', array(
            'title' => generate_title('is_default', i18n('Login')),
            'description' => i18n('Login') . ' ' . blog_title(),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'username' => $user,
            'password' => $pass,
            'type' => 'is_login',
            'is_login' => true,
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Login')
        ));
    }
});

// Show the author page
get('/author/:name', function ($name) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('profile.perpage');

    $posts = get_profile_posts($name, $page, $perpage);

    $total = get_count('/'.$name.'/', 'dirname');

    if ($total === 0) {
        not_found();
    }

    $author = get_author($name);

    if (isset($author[0])) {
        $author = $author[0];
    } else {
        $author = default_profile($name);
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--profile--' . strtolower($name) . '.html.php'; 
    $ls = $vroot . '/layout--profile.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--profile--' . strtolower($name);
    } else if (file_exists($ls)) {
        $layout = 'layout--profile';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/profile--'. strtolower($name) .'.html.php'; 
    if (file_exists($pv)) {
        $pview = 'profile--'. strtolower($name);
    } else {
        $pview = 'profile';
    }

    if (empty($posts) || $page < 1) {
        render($pview, array(
            'title' => generate_title('is_profile', $author),
            'description' => $author->description,
            'canonical' => $author->url,
            'metatags' => generate_meta('is_profile', $author),
            'page' => $page,
            'posts' => null,
            'about' => $author->about,
            'name' => $author->name,
            'author' => $author,
            'type' => 'is_profile',
            'bodyclass' => 'in-profile author-' . $name,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Profile_for') . ' ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page),
            'is_profile' => true
        ), $layout);
        die;
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }

    render($pview, array(
        'title' => generate_title('is_profile', $author),
        'description' => $author->description,
        'canonical' => $author->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_profile', $author),
        'page' => $page,
        'posts' => $posts,
        'about' => $author->about,
        'name' => $author->name,
        'author' => $author,
        'type' => 'is_profile',
        'bodyclass' => 'in-profile author-' . $name,
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Profile_for') . ' ' . $author->name,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_profile' => true
    ), $layout);
});

// Show the RSS feed
get('/author/:name/feed', function ($name) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_profile_posts($name, 1, config('rss.count'));

    $author = get_author($name);

    if (isset($author[0])) {
        $author = $author[0];
    } else {
        $author = default_profile($name);
    }

    // Show an RSS feed
    echo generate_rss($posts, $author);
});

// Edit the profile
get('/edit/profile', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('edit-page', array(
            'title' => generate_title('is_default', i18n('Edit_profile')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; '. i18n('Edit_profile'),
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted data from edit profile page
post('/edit/profile', function () {
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $user = $_SESSION[site_url()]['user'];
    $title = from($_REQUEST, 'title');
    $description = from($_REQUEST, 'description');
    $image = from($_REQUEST, 'image');
    $content = from($_REQUEST, 'content');
    if ($proper && !empty($title) && !empty($content)) {
        edit_profile($title, $content, $user, $description, $image);
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
        }
        config('views.root', 'system/admin/views');

        render('edit-page', array(
            'title' => generate_title('is_default', 'Edit profile'),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'postImage' => $image,
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile'
        ));
    }
});

get('/edit/password', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('edit-password', array(
            'title' => generate_title('is_default', i18n('change_password')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-password',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; '. i18n('change_password'),
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/edit/password', function() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $username = from($_REQUEST, 'username');
        $new_password = from($_REQUEST, 'password');
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        $mfa = user('mfa_secret', $user);
        $old_password = user('password', $username);
        if ($user === $username) {
            $file = 'config/users/' . $user . '.ini';
            if (file_exists($file)) {
                if (!empty($new_password)) {
                    update_user($user, $new_password, $role, $mfa);
                }
            }
            $redir = site_url() . 'admin';
            header("location: $redir");  
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }    
});

get('/edit/mfa', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('edit-mfa', array(
            'title' => generate_title('is_default', i18n('config_mfa')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-mfa',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; '. i18n('config_mfa'),
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/edit/mfa', function() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $username = from($_REQUEST, 'username');
        $mfa_secret = from($_REQUEST, 'mfa_secret');
        $mfacode = from($_REQUEST, 'mfacode');
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        $old_password = user('password', $user);
        $password = from($_REQUEST, 'password');
        $message['error'] = '';
        if ($user === $username) {
            if (!is_null($mfa_secret) && $mfa_secret !== "disabled") {
                $google2fa = new Google2FA();
                if ($google2fa->verifyKey($mfa_secret, $mfacode)) {
                    if (password_verify($password, $old_password)) {
                        if (!empty($mfa_secret)) {
                            update_user($user, $password, $role, $mfa_secret);
                        }
                    } else {
                        $message['error'] .= '<li class="alert alert-danger">' . i18n('Pass_Error') . '</li>';
                    }
                } else {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('MFA_Error') . '</li>';
                }
                config('views.root', 'system/admin/views');
                render('edit-mfa', array(
                    'title' => generate_title('is_default', i18n('config_mfa')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $message['error'] . '</ul>',
                    'type' => 'is_profile',
                    'is_admin' => true,
                    'bodyclass' => 'edit-mfa',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; '. i18n('config_mfa'),
                ));
            } else {
                if (password_verify($password, $old_password)) {
                    update_user($user, $password, $role, 'disabled');
                } else {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('Pass_Error') . '</li>';
                }
                config('views.root', 'system/admin/views');
                render('edit-mfa', array(
                    'title' => generate_title('is_default', i18n('config_mfa')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $message['error'] . '</ul>',
                    'type' => 'is_profile',
                    'is_admin' => true,
                    'bodyclass' => 'edit-mfa',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; '. i18n('config_mfa'),
                ));    
            }
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }    
});

// Edit the frontpage
get('/edit/frontpage', function () {
    
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {

        config('views.root', 'system/admin/views');
        
        if ($role === 'editor' || $role === 'admin') {
            render('edit-page', array(
                'title' => generate_title('is_default', 'Edit frontpage'),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_frontpage',
                'is_admin' => true,
                'bodyclass' => 'edit-frontpage',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit frontpage',
            )); 
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_frontpage',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted data from edit frontpage
post('/edit/frontpage', function () {
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $title = from($_REQUEST, 'title');
    $content = from($_REQUEST, 'content');
    if ($role === 'editor' || $role === 'admin') {    
        if ($proper && !empty($title) && !empty($content)) {
            edit_frontpage($title, $content);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');

            render('edit-page', array(
                'title' => generate_title('is_default', 'Edit frontpage'),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postContent' => $content,
                'type' => 'is_frontpage',
                'is_admin' => true,
                'bodyclass' => 'edit-frontpage',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit frontpage'
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");        
    }
});

// Edit the frontpage
get('/front/edit', function () {

    if (login()) {
        $edit = site_url() . 'edit/frontpage';
        header("location: $edit");
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show the "Add content" page
get('/add/content', function () {

    if (isset($_GET['type'])) {
        $req = _h($_GET['type']);
    } else {
        $req = 'post';
    }

    $type = 'is_' . $req;

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-content', array(
            'title' => generate_title('is_default', i18n('Add_new_post')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_new_post')
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted add post data
post('/add/content', function () {
    
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $is_image = from($_REQUEST, 'is_image');
    $is_audio = from($_REQUEST, 'is_audio');
    $is_video = from($_REQUEST, 'is_video');
    $is_quote = from($_REQUEST, 'is_quote');
    $is_link = from($_REQUEST, 'is_link');
    $is_post = from($_REQUEST, 'is_post');
    
    if (!empty($is_image)) {
        $type = 'is_image';
    } elseif (!empty($is_video)) {
        $type = 'is_video';
    } elseif (!empty($is_link)) {
        $type = 'is_link';
    } elseif (!empty($is_quote)) {
        $type = 'is_quote';
    } elseif (!empty($is_audio)) {
        $type = 'is_audio';
    } elseif (!empty($is_post)) {
        $type = 'is_post';
    }
    
    $link = from($_REQUEST, 'link');
    $image = from($_REQUEST, 'image');
    $audio = from($_REQUEST, 'audio');
    $video = from($_REQUEST, 'video');
    $quote = from($_REQUEST, 'quote');

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $user = $_SESSION[site_url()]['user'];
    $draft = from($_REQUEST, 'draft');
    $category = from($_REQUEST, 'category');
    $date = from($_REQUEST, 'date');
    $time = from($_REQUEST, 'time');
    $comments = from($_REQUEST, 'comments');
    $dateTime = null;
    if ($date !== null && $time !== null) {
        $dateTime = $date . ' ' . $time;
    }
    if (empty($url)) {
        $url = $title;
    }
    
    if (empty($is_post) && empty($is_image) && empty($is_video) && empty($is_audio) && empty($is_link) && empty($is_quote)) {
        $add = site_url() . 'admin/content';
        header("location: $add");    
    }
    
    if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($is_post)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'post', $description, null, $dateTime, $comments);
    } elseif ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'image', $description, $image, $dateTime, $comments);
    } elseif ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'video', $description, $video, $dateTime, $comments);
    } elseif ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'audio', $description, $audio, $dateTime, $comments);
    } elseif ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'quote', $description, $quote, $dateTime, $comments);
    } elseif ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {
        add_content($title, $tag, $url, $content, $user, $draft, $category, 'link', $description, $link, $dateTime, $comments);
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
        }
        if (empty($tag)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_tag') . '</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
        }
        
        if (!empty($is_image)) {
            if (empty($image)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_image') . '</li>';
            }
        } elseif (!empty($is_video)) {
            if (empty($video)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_video') . '</li>';
            }
        } elseif (!empty($is_link)) {
            if (empty($link)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_link') . '</li>';
            }
        } elseif (!empty($is_quote)) {
            if (empty($quote)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_quote') . '</li>';
            }
        } elseif (!empty($is_audio)) {
            if (empty($audio)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_audio') . '</li>';
            }
        }
        
        config('views.root', 'system/admin/views');
        render('add-content', array(
            'title' => generate_title('is_default', i18n('Add_content')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postImage' => $image,
            'postVideo' => $video,
            'postLink' => $link,
            'postQuote' => $quote,
            'postAudio' => $audio,
            'postTag' => $tag,
            'postUrl' => $url,
            'postComments' => $comments,
            'postContent' => $content,
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_content')
        ));
    }
    
});

// Show the static add page
get('/add/page', function () {
    
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_new_page')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_page',
                'is_admin' => true,
                'bodyclass' => 'add-page',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_new_page')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_page',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted static add page data
post('/add/page', function () {

    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $draft = from($_REQUEST, 'draft');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content) && login()) {
            add_page($title, $url, $content, $draft, $description);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_new_page')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_page',
                'is_admin' => true,
                'bodyclass' => 'add-page',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_new_page')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
});

// Autosave
post('/admin/autosave', function () {

    if (login()) {
        $title = $_REQUEST['title'];
        $url = $_REQUEST['url'];
        $content = $_REQUEST['content'];
        $description = $_REQUEST['description'];
        $draft = 'draft';    
        $posttype = $_REQUEST['posttype'];
        $autoSave = $_REQUEST['autoSave'];
        $addEdit = $_REQUEST['addEdit'];
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        $comments = $_REQUEST['comments'];

        if (empty($url)) {
            $url = $title;
        }

        if ($addEdit == 'edit') {
            $revertPage = '';
            $revertPost = '';
            $publishDraft = '';
            $oldfile = $_REQUEST['oldfile'];
            $destination = null;
        }

        if (!empty($title) && !empty($content)) {
            if ($posttype == 'is_page') {
                if ($role === 'editor' || $role === 'admin') {
                    if ($addEdit == 'add') {
                        $response = add_page($title, $url, $content, $draft, $description, $autoSave);
                    } else {
                        $response = edit_page($title, $url, $content, $oldfile, $revertPage, $publishDraft, $destination, $description, null, $autoSave);
                    }
                }
            } elseif ($posttype == 'is_subpage') {
                if ($role === 'editor' || $role === 'admin') {
                    $static = $_REQUEST['parent_page'];
                    if ($addEdit == 'add') {
                        $response = add_sub_page($title, $url, $content, $static, $draft, $description, $autoSave);
                    } else {
                        $response = edit_page($title, $url, $content, $oldfile, $revertPage, $publishDraft, $destination, $description, $static, $autoSave);
                    }
                }
            } else {
                $tag = $_REQUEST['tag'];
                $category = $_REQUEST['category'];
                $dateTime = $_REQUEST['dateTime'];
                if ($posttype == 'is_image') {
                    $type = 'image';
                    $media = $_REQUEST['pimage'];
                } elseif ($posttype == 'is_video') {
                    $type = 'video';
                    $media = $_REQUEST['pvideo'];
                } elseif ($posttype == 'is_link') {
                    $type = 'link';
                    $media = $_REQUEST['plink'];
                } elseif ($posttype == 'is_quote') {
                    $type = 'quote';
                    $media = $_REQUEST['pquote'];
                } elseif ($posttype == 'is_audio') {
                    $type = 'audio';
                    $media = $_REQUEST['paudio'];
                } elseif ($posttype == 'is_post') {
                    $type = 'post';
                    $media = null;
                }
                
                if (!empty($title) && !empty($tag) && !empty($content)) {
                    if ($addEdit == 'add') {
                        $response = add_content($title, $tag, $url, $content, $user, $draft, $category, $type, $description, $media, $dateTime, $autoSave, $comments);
                    } else {
                        $arr = explode('/', $oldfile);
                        if ($user === $arr[1] || $role === 'editor' || $role === 'admin') {
                            $response = edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, $type, $destination, $description, $dateTime, $media, $autoSave, $comments);
                        }
                    }
                }
            }
        } else {
            $response = "No content to save.";
        }
        echo $response;
    }
});

// Show the add category
get('/add/category', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_category')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'add-category',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_category')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted add category 
post('/add/category', function () {

    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content)) {
            add_category($title, $url, $content, $description);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_category')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'add-category',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_category')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
    
});

// Show admin/posts 
get('/admin/posts', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            config('views.root', 'system/admin/views');
            $page = from($_GET, 'page');
            $page = $page ? (int)$page : 1;
            $perpage = 20;

            $posts = get_posts(null, $page, $perpage);

            $total = '';

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'title' => generate_title('is_default', i18n('All_blog_posts')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'bodyclass' => 'no-posts',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('All_blog_posts')
                ));

                die;
            }

            render('posts-list', array(
                'title' => generate_title('is_default', i18n('All_blog_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'heading' => i18n('All_blog_posts'),
                'page' => $page,
                'posts' => $posts,
                'bodyclass' => 'all-posts',
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('All_blog_posts'),
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('All_blog_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('All_blog_posts')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/popular 
get('/admin/popular', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            config('views.root', 'system/admin/views');
            $page = from($_GET, 'page');
            $page = $page ? (int)$page : 1;
            $perpage = 20;

            $posts = popular_posts(true,$perpage);

            $total = '';

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'title' => generate_title('is_default', i18n('Popular_posts')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'is_admin' => true,
                    'bodyclass' => 'admin-popular',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Popular_posts')
                ));

                die;
            }

            render('popular-posts', array(
                'title' => generate_title('is_default', i18n('Popular_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'heading' => i18n('Popular_posts'),
                'page' => $page,
                'posts' => $posts,
                'is_admin' => true,
                'bodyclass' => 'admin-popular',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Popular_posts'),
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Popular_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Popular_posts')
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

        $name = $_SESSION[site_url()]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_profile_posts($name, $page, $perpage);

        $total = get_count('/'.$name.'/', 'dirname');

        $author = get_author($name);

        if (isset($author[0])) {
            $author = $author[0];
        } else {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('user-posts', array(
                'title' => generate_title('is_default', i18n('My_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'page' => $page,
                'heading' => i18n('My_posts'),
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-mine',
                'is_admin' => true,
                'bodyclass' => 'admin-mine',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('My_posts') . ': '. $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('user-posts', array(
            'title' => generate_title('is_default', i18n('My_posts')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'heading' => i18n('My_posts'),
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-mine',
            'is_admin' => true,
            'bodyclass' => 'admin-mine',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('My_posts') . ': '. $author->name,
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

        $name = $_SESSION[site_url()]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_draft($name, $page, $perpage);
        
        $draftPages = find_draft_page();
        
        $draftSubpages = find_draft_subpage();

        $total = get_draftcount($name);

        $author = get_author($name);

        if (isset($author[0])) {
            $author = $author[0];
        } else {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('user-draft', array(
                'title' => generate_title('is_default', i18n('My_draft')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'page' => $page,
                'heading' => i18n('My_draft'),
                'posts' => null,
                'draftPages' => $draftPages,
                'draftSubpages' => $draftSubpages,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-draft',
                'is_admin' => true,
                'bodyclass' => 'admin-draft',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('My_draft'). ': ' . $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }
        
        render('user-draft', array(
            'title' => generate_title('is_default', i18n('My_draft')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'heading' => i18n('My_draft'),
            'page' => $page,
            'posts' => $posts,
            'draftPages' => $draftPages,
            'draftSubpages' => $draftSubpages,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-draft',
            'is_admin' => true,
            'bodyclass' => 'admin-draft',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('My_draft') . ': ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/scheduled
get('/admin/scheduled', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        $name = $_SESSION[site_url()]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_scheduled($name, $page, $perpage);

        $total = get_scheduledcount($name);

        $author = get_author($name);

        if (isset($author[0])) {
            $author = $author[0];
        } else {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('scheduled', array(
                'title' => generate_title('is_default', i18n('Scheduled_posts')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'page' => $page,
                'heading' => i18n('Scheduled_posts'),
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-scheduled',
                'is_admin' => true,
                'bodyclass' => 'admin-scheduled',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Scheduled_posts') . ': ' . $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('scheduled', array(
            'title' => generate_title('is_default', i18n('Scheduled_posts')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'heading' => i18n('Scheduled_posts'),
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-scheduled',
            'is_admin' => true,
            'bodyclass' => 'admin-scheduled',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Scheduled_posts') . ': ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/content
get('/admin/content', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('content-type', array(
            'title' => generate_title('is_default', i18n('Add_content')),
            'description' => safe_html(strip_tags(blog_description())),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'type' => 'is_admin-content',
            'is_admin' => true,
            'bodyclass' => 'admin-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Add_content')
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/pages
get('/admin/pages', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('static-pages', array(
                'title' => generate_title('is_default', i18n('Static_pages')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-pages',
                'is_admin' => true,
                'bodyclass' => 'admin-pages',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Static_pages')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/admin/pages', function () {

    if (login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $json = $_REQUEST['json'];
            reorder_pages($json);
            echo json_encode(array(
                'message' => 'Page order saved successfully!',
            ));
        }
    }
});

// Show admin/pages
get('/admin/pages/:static', function ($static) 
{
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {

            $post = find_page($static);

            if (!$post) {
                not_found();
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

            $post = $post['current'];
            
            render('static-subpages', array(
                'title' => generate_title('is_default', $post->title),
                'description' => $post->description,
                'canonical' => $post->url,
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'in-page ' . strtolower($static),
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . '<a href="'. site_url() .'admin/pages">' .i18n('pages').'</a> &#187; ' . $post->title,
                'p' => $post,
                'static' => $post,
                'type' => 'is_subpage',
                'prev' => static_prev($prev),
                'next' => static_next($next),
                'is_page' => true
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', 'Pages'),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_subpage',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
    } 
});

post('/admin/pages/:static', function ($static) {

    if (login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $json = $_REQUEST['json'];
            reorder_subpages($json);
            echo json_encode(array(
                'message' => 'Page order saved successfully!',
            ));
        }
    }
});

// Show import page
get('/admin/import', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('import', array(
                'title' => generate_title('is_default', i18n('Import_Feed')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-import',
                'is_admin' => true,
                'bodyclass' => 'admin-import',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Import_Feed')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-import',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted import page data
post('/admin/import', function () {
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $url = from($_REQUEST, 'url');
    $credit = from($_REQUEST, 'credit');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if ($role === 'admin') {
        if (!empty($url) && $proper) {

            get_feed($url, $credit);
            $log = get_feed($url, $credit);

            if (!empty($log)) {

                config('views.root', 'system/admin/views');

                render('import', array(
                    'title' => generate_title('is_default', i18n('Import_Feed')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $log . '</ul>',
                    'type' => 'is_admin-import',
                    'is_admin' => true,
                    'bodyclass' => 'admin-import',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Import_Feed')
                ));
            }
        } else {
            $message['error'] = '';
            if (empty($url)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_feedurl') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }

            config('views.root', 'system/admin/views');

            render('import', array(
                'title' => generate_title('is_default', i18n('Import_Feed')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'url' => $url,
                'type' => 'is_admin-import',
                'is_admin' => true,
                'bodyclass' => 'admin-import',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Import_Feed')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
});

// Show Config page
get('/admin/config', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted Config page data
post('/admin/config', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            $new_config = array();
            $new_Keys = array();
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            foreach (glob('cache/widget/archive*.cache', GLOB_NOSORT) as $file) {
                unlink($file);
            }
            $redir = site_url() . 'admin/config';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/custom', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-custom', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/config/custom', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
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
            $redir = site_url() . 'admin/config/custom';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/reading', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-reading', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/config/reading', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            $redir = site_url() . 'admin/config/reading';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/writing', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-writing', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/config/writing', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            $redir = site_url() . 'admin/config/writing';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/widget', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-widget', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/config/widget', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            foreach (glob('cache/widget/tags*.cache', GLOB_NOSORT) as $file) {
                unlink($file);
            }
            $redir = site_url() . 'admin/config/widget';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/metatags', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-metatags', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted Config page data
post('/admin/config/metatags', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            foreach (glob('cache/widget/*.cache', GLOB_NOSORT) as $file) {
                unlink($file);
            }
            $redir = site_url() . 'admin/config/metatags';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Config page
get('/admin/config/security', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-security', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/config/security', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            $redir = site_url() . 'admin/config/security';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});


// Show Config page
get('/admin/config/performance', function () {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config-performance', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Config')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Config')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted Config page data
post('/admin/config/performance', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $new_config = array();
        $new_Keys = array();
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            foreach ($_POST as $name => $value) {
                if (substr($name, 0, 8) == "-config-") {
                    $name = str_replace("_", ".", substr($name, 8));
                    if(!is_null(config($name))) {
                        $new_config[$name] = $value;
                    } else {
                        $new_Keys[$name] = $value;    
                    }
                }
            }
            save_config($new_config, $new_Keys);
            $redir = site_url() . 'admin/config/performance';
            header("location: $redir");
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Backup page
get('/admin/backup', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('backup', array(
                'title' => generate_title('is_default', i18n('Backup')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-backup',
                'is_admin' => true,
                'bodyclass' => 'admin-backup',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Backup')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Create backup page
get('/admin/backup-start', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            render('backup-start', array(
                'title' => generate_title('is_default', i18n('Create_backup')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-backup-start',
                'is_admin' => true,
                'bodyclass' => 'admin-backup-start',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Create_backup')
            ));
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show clear cache page
get('/admin/clear-cache', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('clear-cache', array(
                'title' => generate_title('is_default', i18n('Clear_cache')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-clear-cache',
                'is_admin' => true,
                'bodyclass' => 'admin-clear-cache',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Clear_cache')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-clear-cache',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Update page
get('/admin/update', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('update', array(
                'title' => generate_title('is_default', i18n('Check_update')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-update',
                'is_admin' => true,
                'bodyclass' => 'admin-update',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' .  i18n('Check_update')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show the update now link
get('/admin/update/now/:csrf', function ($CSRF) {
    $proper = is_csrf_proper($CSRF);
    $updater = new \Kanti\HubUpdater(array(
        'name' => 'danpros/htmly',
        'prerelease' => !!config("prerelease"),
    ));
    if (login() && $proper && $updater->able()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'admin') {
            $updater->update();
            config('views.root', 'system/admin/views');
            render('updated-to', array(
                'title' => generate_title('is_default', i18n('Update')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'info' => $updater->getCurrentInfo(),
                'type' => 'is_admin-update',
                'is_admin' => true,
                'bodyclass' => 'admin-update',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Update')
            ));
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show Menu builder
get('/admin/menu', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('menu', array(
                'title' => generate_title('is_default', i18n('Menus')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'admin-menu',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Menus')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/admin/menu', function () {

    if (login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $json = from($_REQUEST, 'json');
            file_put_contents('content/data/menu.json', json_encode($json, JSON_UNESCAPED_UNICODE));
            echo json_encode(array(
                'message' => 'Menu saved successfully!',
            ));
        } else {
            $redir = site_url();
            header("location: $redir");
        }
    }
});

// Manage users page
get('/admin/users', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('users', array(
                'title' => generate_title('is_default', i18n('User')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-users',
                'is_admin' => true,
                'bodyclass' => 'admin-users',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('User')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

get('/admin/add/user', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('add-user', array(
                'title' => generate_title('is_default', i18n('Add_user')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-users',
                'is_admin' => true,
                'bodyclass' => 'admin-users',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('add_user')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/admin/add/user', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $username = from($_REQUEST, 'username');
    $user_role = from($_REQUEST, 'user-role');
    $password = from($_REQUEST, 'password');
    if (login() && $proper) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            
            if (!empty($username) && !empty($password)) {
                create_user($username, $password, $user_role);
            } else {
            
                $message['error'] = '';
                if (empty($username)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_username') . '</li>';
                }
                if (empty($password)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_password') . '</li>';
                }        
                
                render('add-user', array(
                    'title' => generate_title('is_default', i18n('Add_user')),
                    'description' => safe_html(strip_tags(blog_description())),
                    'canonical' => site_url(),
                    'metatags' => generate_meta(null, null),
                    'error' => '<ul>' . $message['error'] . '</ul>',
                    'type' => 'is_admin-users',
                    'is_admin' => true,
                    'username' => $username,
                    'user_role' => $user_role,
                    'password' => $password,
                    'bodyclass' => 'admin-users',
                    'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('add_user')
                ));
            }
            $redir = site_url() . 'admin/users';
            header("location: $redir");              
        } else {
            $redir = site_url();
            header("location: $redir");              
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

get('/admin/users/:username/edit', function ($username) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('edit-user', array(
                'title' => generate_title('is_default', $username),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-users',
                'username' => $username,
                'is_admin' => true,
                'bodyclass' => 'admin-users',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $username
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted Config page data
post('/admin/users/:username/edit', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (login() && $proper) {
        $username = from($_REQUEST, 'username');
        $user_role = from($_REQUEST, 'role-name');
        $new_password = from($_REQUEST, 'password');
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        $old_password = user('password', $username);
        if ($role === 'admin') {
            $file = 'config/users/' . $username . '.ini';
            if (file_exists($file)) {
                if (empty($new_password)) {
                    file_put_contents($file, "password = " . $old_password . "\n" .
                        "encryption = password_hash\n" .
                        "role = " . $user_role . "\n", LOCK_EX);
                } else {
                    update_user($username, $new_password, $user_role);
                }
            }
            $redir = site_url() . 'admin/users';
            header("location: $redir");  
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

get('/admin/users/:username/delete', function ($username) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('delete-user', array(
                'title' => generate_title('is_default', $username),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-users',
                'username' => $username,
                'is_admin' => true,
                'bodyclass' => 'admin-users',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $username
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-menu',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/admin/users/:username/delete', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $file = from($_REQUEST, 'file');
    $username = from($_REQUEST, 'username');
    $user_role = user('role', $username);
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        if ($role === 'admin') {
            if ($user_role !== 'admin') {
                unlink($file);
            }
        }
        $redir = site_url() . 'admin/users';
        header("location: $redir");         
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

post('/admin/gallery', function () {

    if (login()) {
        $page = from($_REQUEST, 'page');
        $images = image_gallery(null, $page, 40);
        echo json_encode(array('images' => $images));
    }
});

// Show category page
get('/admin/categories', function () {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            render('categories', array(
                'title' => generate_title('is_default', i18n('Categories')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-categories',
                'is_admin' => true,
                'bodyclass' => 'admin-categories',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Categories')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-categories',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show the category page
get('/admin/categories/:category', function ($category) {

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $page = from($_GET, 'page');
            $page = $page ? (int)$page : 1;
            $perpage = config('category.perpage');
            
            if (empty($perpage)) {
                $perpage = 10;    
            }

            $posts = get_category($category, $page, $perpage);
            
            $desc = get_category_info($category);
            
            if(!empty($desc)) {
                $desc = $desc[0];
            }
            
            if (empty($desc)) {
                // a non-existing page
                not_found();
            }
    
            $total = $desc->count;
            
            render('category-list', array(
                'title' => generate_title('is_default', $desc->title),
                'description' => $desc->description,
                'canonical' => $desc->url,
                'metatags' => generate_meta(null, null),
                'page' => $page,
                'posts' => $posts,
                'category' => $desc,
                'bodyclass' => 'in-category category-' . strtolower($category),
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . site_url() . 'admin/categories">' . i18n('Categories') .'</a>  &#187; ' . $desc->title,
                'pagination' => has_pagination($total, $perpage, $page),
                'is_category' => true,
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', 'Categories'),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-categories',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
    }   
});

// Show the category page
get('/category/:category', function ($category) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('category.perpage');
    
    if (empty($perpage)) {
        $perpage = 10;    
    }

    $posts = get_category($category, $page, $perpage);
    
    $desc = get_category_info($category);
    
    
    if(!empty($desc)) {
        $desc = $desc[0];
    }

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }
    
    $total = $desc->count;
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--category--'. strtolower($category) .'.html.php'; 
    $ls = $vroot . '/layout--category.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--category--' . strtolower($category);
    } else if (file_exists($ls)) {
        $layout = 'layout--category';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/main--category--'. strtolower($category) .'.html.php';
    $ps = $vroot . '/main--category.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--category--' . strtolower($category);
    } else if (file_exists($ps)) {
        $pview = 'main--category';
    } else {
        $pview = 'main';
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }
    
    render($pview, array(
        'title' => generate_title('is_category', $desc),
        'description' => $desc->description,
        'canonical' => $desc->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_category', $desc),
        'page' => $page,
        'posts' => $posts,
        'category' => $desc,
        'bodyclass' => 'in-category category-' . strtolower($category),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $desc->title,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_category' => true
    ), $layout);
});

// Show the RSS feed
get('/category/:category/feed', function ($category) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_category($category, 1, config('rss.count'));
    
    $data = get_category_info($category);
    
    if(!empty($data)) {
        $data = $data[0];
    }

    // Show an RSS feed
    echo generate_rss($posts, $data);
});

// Show edit the category page
get('/category/:category/edit', function ($category) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = get_category_info($category);

            if(empty($post)) {
                not_found();
            }

            $post = $post[0];

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit_category')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'edit-category',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Category') . ': ' . $post->title,
                'p' => $post,
                'static' => $post,
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from category page
post('/category/:category/edit', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content)) {
            edit_category($title, $url, $content, $oldfile, $destination, $description);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit_category')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'oldfile' => $oldfile,
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'edit-category',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Edit_category')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
});

// Delete category
get('/category/:category/delete', function ($category) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = get_category_info($category);

            if(empty($post)) {
                not_found();
            }

            $post = $post[0];

            render('delete-category', array(
                'title' => generate_title('is_default', i18n('Delete') . ' ' . i18n('Category')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_category',
                'is_admin' => true,
                'bodyclass' => 'delete-category',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Category') . ': ' . $post->title,
                'p' => $post,
                'static' => $post,
                'type' => 'categoryPage',
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted category data
post('/category/:category/delete', function () {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $file = from($_REQUEST, 'file');
            $destination = from($_GET, 'destination');
            delete_page($file, $destination);
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    }
});

// Show the type page
get('/type/:type', function ($type) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('type.perpage');
    
    if (empty($perpage)) {
        $perpage = 10;    
    }

    $posts = get_type($type, $page, $perpage);

    $total = get_typecount($type);
    
    $ttype = new stdClass;
    $ttype->title = ucfirst($type);
    $ttype->url = site_url() . 'type/' . strtolower($type);
    $ttype->count = $total;
    $ttype->description = i18n('Posts_with_type') . ' ' . ucfirst($type) . ' ' . i18n('by') . ' ' . blog_title();
    $ttype->body = $ttype->description;

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--type--'. strtolower($type) .'.html.php'; 
    $ls = $vroot . '/layout--type.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--type--' . strtolower($type);
    } else if (file_exists($ls)) {
        $layout = 'layout--type';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/main--type--'. strtolower($type) .'.html.php';
    $ps = $vroot . '/main--type.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--type--' . strtolower($type);
    } else if (file_exists($ps)) {
        $pview = 'main--type';
    } else {
        $pview = 'main';
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }
    
    render($pview, array(
        'title' => generate_title('is_type', $ttype),
        'description' => $ttype->description,
        'canonical' => $ttype->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_type', $ttype),
        'page' => $page,
        'posts' => $posts,
        'type' => $ttype,
        'bodyclass' => 'in-type type-' . strtolower($type),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . ucfirst($type),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_type' => true
    ), $layout);
});

// Show the RSS feed
get('/type/:type/feed', function ($type) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_type($type, 1, config('rss.count'));
    $data = new stdClass;
    $data->title = ucfirst($type);
    $data->url = site_url() . 'type/' . strtolower($type);
    $data->body = i18n('Posts_with_type') . ' ' . ucfirst($type) . ' ' . i18n('by') . ' ' . blog_title();

    // Show an RSS feed
    echo generate_rss($posts, $data);
});

// Show the tag page
get('/tag/:tag', function ($tag) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('tag.perpage');

    $posts = get_tag($tag, $page, $perpage);

    $total = get_tagcount($tag);
        
    $ttag = new stdClass;
    $ttag->title = tag_i18n($tag);
    $ttag->url = site_url() . 'tag/' . strtolower($tag);
    $ttag->count = $total;
    $ttag->description = i18n('All_posts_tagged') . ' ' . tag_i18n($tag) . ' ' . i18n('by') . ' ' . blog_title();
    $ttag->body = $ttag->description;

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--tag--' . strtolower($tag) . '.html.php'; 
    $ls = $vroot . '/layout--tag.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--tag--' . strtolower($tag);
    } else if (file_exists($ls)) {
        $layout = 'layout--tag';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/main--tag--' . strtolower($tag) . '.html.php'; 
    $ps = $vroot . '/main--tag.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--tag--' . strtolower($tag);
    } elseif (file_exists($ps)) {
        $pview = 'main--tag';
    } else {
        $pview = 'main';
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }
    
    render($pview, array(
        'title' => generate_title('is_tag', $ttag),
        'description' => $ttag->description,
        'canonical' => $ttag->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_tag', $ttag),
        'page' => $page,
        'posts' => $posts,
        'tag' => $ttag,
        'bodyclass' => 'in-tag tag-' . strtolower($tag),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Posts_tagged') . ' ' . tag_i18n($tag),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_tag' => true
    ), $layout);
});

// Show the RSS feed
get('/tag/:tag/feed', function ($tag) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_tag($tag, 1, config('rss.count'));
    $data = new stdClass;
    $data->title = tag_i18n($tag);
    $data->url = site_url() . 'tag/' . strtolower($tag);
    $data->body = i18n('All_posts_tagged') . ' ' . tag_i18n($tag) . ' ' . i18n('by') . ' ' . blog_title();

    // Show an RSS feed
    echo generate_rss($posts, $data);
});

// Show the archive page
get('/archive/:req', function ($req) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('archive.perpage');

    $posts = get_archive($req, $page, $perpage);

    $total = get_count($req, 'basename');

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }

    $time = explode('-', $req);
    $date = strtotime($req);

    if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
        $timestamp = format_date($date, 'd F Y');
    } elseif (isset($time[0]) && isset($time[1])) {
        $timestamp = format_date($date, 'F Y');
    } else {
        $timestamp = $req;
    }
    
    $tarchive = new stdClass;
    $tarchive->title = $timestamp;
    $tarchive->url = site_url() . 'archive/' . $req;
    $tarchive->count = $total;
    $tarchive->description = i18n('Archive_page_for') . ' ' . $timestamp . ' ' . i18n('by') . ' ' . blog_title();
    $tarchive->body = $tarchive->description;

    if (!$date) {
        // a non-existing page
        not_found();
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--archive.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--archive';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/main--archive.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--archive';
    } else {
        $pview = 'main';
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }

    render($pview, array(
        'title' => generate_title('is_archive', $tarchive),
        'description' => $tarchive->description,
        'canonical' => $tarchive->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_archive', $tarchive),
        'page' => $page,
        'posts' => $posts,
        'archive' => $tarchive,
        'bodyclass' => 'in-archive archive-' . strtolower($req),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Archive_for') . ' ' . $timestamp,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_archive' => true
    ), $layout);
});

// Show the RSS feed
get('/archive/:req/feed', function ($req) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_archive($req, 1, config('rss.count'));
    
    $time = explode('-', $req);
    $date = strtotime($req);

    if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
        $timestamp = format_date($date, 'd F Y');
    } elseif (isset($time[0]) && isset($time[1])) {
        $timestamp = format_date($date, 'F Y');
    } else {
        $timestamp = $req;
    }
    
    $data = new stdClass;
    $data->title = $timestamp;
    $data->url = site_url() . 'archive/' . $req;
    $data->body = i18n('Archive_page_for') . ' ' . $timestamp . ' ' . i18n('by') . ' ' . blog_title();

    // Show an RSS feed
    echo generate_rss($posts, $data);
});

// Show the search page
get('/search/:keyword', function ($keyword) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('search.perpage');

    $posts = get_keyword($keyword, $page, $perpage);
    $total = keyword_count($keyword);

    $tsearch = new stdClass;
    $tsearch->title = $keyword;
    $tsearch->url = site_url() . 'search/' . strtolower($keyword);
    $tsearch->count = $total;
    $tsearch->description = i18n('Search_results_for') . ' ' . $keyword . ' ' . i18n('by') . ' ' . blog_title();
    $tsearch->body = $tsearch->description;

    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--search.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--search';
    } else {
        $layout = '';
    }

    if (!$posts || $page < 1) {
        // a non-existing page or no search result
        render('404-search', array(
            'title' => i18n('Search_results_not_found') . ' - ' . blog_title(),
            'description' => i18n('Search_results_not_found'),
            'canonical' => site_url(),
            'metatags' => generate_meta(null, null),
            'search' => $tsearch,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('No_search_results'),
            'canonical' => site_url(),
            'bodyclass' => 'error-404-search',
            'is_404search' => true,
        ), $layout);
        die;
    }
    
    $pv = $vroot . '/main--search.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--search';
    } else {
        $pview = 'main';
    }
    
    if ($page > 1) {
        $CanonicalPageNum = '?page=' . $page;
    } else {
        $CanonicalPageNum = NULL;
    }

    render($pview, array(
        'title' => generate_title('is_search', $tsearch),
        'description' => $tsearch->description,
        'canonical' => $tsearch->url . $CanonicalPageNum,
        'metatags' => generate_meta('is_search', $tsearch),
        'page' => $page,
        'posts' => $posts,
        'search' => $tsearch,
        'bodyclass' => 'in-search search-' . strtolower($keyword),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Search_results_for') . ' ' . $keyword,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_search' => true
    ), $layout);
});

// Show the RSS feed
get('/search/:keyword/feed', function ($keyword) {

    header('Content-Type: application/rss+xml');
    
    $posts = get_keyword($keyword, 1, config('rss.count'));

    $data = new stdClass;
    $data->title = $keyword;
    $data->url = site_url() . 'search/' . strtolower($keyword);
    $data->body = i18n('Search_results_for') . ' ' . $keyword . ' ' . i18n('by') . ' ' . blog_title();

    // Show an RSS feed
    echo generate_rss($posts, $data);
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

// Show blog post without year-month
get('/'. permalink_type() .'/:name', function ($name) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (permalink_type() == 'default') {
        $post = find_post(null, null, $name);
        if (is_null($post)) {
            not_found();
        } else {
            $current = $post['current'];
        }
        $redir = site_url() . date('Y/m', $current->date) . '/' . $name;
        header("location: $redir", TRUE, 301);
    }

    if (config("views.counter") != "true") {
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
    } else {
        add_view('post_' . $name);
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }        
    }

    $post = find_post(null, null, $name);

    if (is_null($post)) {
        not_found('post_' . $name);
    } else {
        $current = $post['current'];
    }

    $author = new stdClass;
    $author->url = $current->authorUrl;
    $author->name = $current->authorName;
    $author->description = $current->authorDescription;
    $author->about = $current->authorAbout;
    $author->avatar = $current->authorAvatar;

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
    
    if (isset($current->image)) {
        $var = 'imagePost';
    } elseif (isset($current->link)) {
        $var = 'linkPost';
    } elseif (isset($current->quote)) {
        $var = 'quotePost';
    } elseif (isset($current->audio)) {
        $var = 'audioPost';
    } elseif (isset($current->video)) {
        $var = 'videoPost'; }
    else {
        $var = 'blogPost';
    }
    
    if (config('blog.enable') === 'true') {
        $blog = '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . site_url() . blog_path() .'"><span itemprop="name">' . blog_string() . '</span></a><meta itemprop="position" content="2" /></li> &#187; ';
    } else {
        $blog = '';
    }

    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--post--' . $current->ct . '.html.php'; 
    $pt = $vroot . '/layout--post--' . $current->type . '.html.php';
    $ls = $vroot . '/layout--post.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--post--' . $current->ct;
    } else if (file_exists($pt)) {
        $layout = 'layout--post--' . $current->type;
    } else if (file_exists($ls)) {
        $layout = 'layout--post';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/post--' . $current->ct . '.html.php'; 
    $pvt = $vroot . '/post--' . $current->type . '.html.php'; 
    if (file_exists($pv)) {
        $pview = 'post--' . $current->ct;
    } else if(file_exists($pvt)) {
        $pview = 'post--' . $current->type;
    } else {
        $pview = 'post';
    }

    render($pview, array(
        'title' => generate_title('is_post', $current),
        'description' => $current->description,
        'canonical' => $current->url,
        'metatags' => generate_meta('is_post', $current),
        'p' => $current,
        'post' => $current,
        'author' => $author,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type,
        'breadcrumb' => '<style>.breadcrumb-list {margin:0; padding:0;} .breadcrumb-list li {display: inline-block; list-style: none;}</style><ol class="breadcrumb-list" itemscope itemtype="http://schema.org/BreadcrumbList"><li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . site_url() . '"><span itemprop="name">' . config('breadcrumb.home') . '</span></a><meta itemprop="position" content="1" /></li> &#187; '. $blog . '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">' . $current->categoryb . '<meta itemprop="position" content="3" /></li>' . ' &#187; ' . $current->title . '</ol>',
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true
    ), $layout);

});

// Edit blog post
get('/'. permalink_type() .'/:name/edit', function ($name) {

    if (login()) {

        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post(null, null, $name);

        if (!$post) {
            $post = find_draft(null, null, $name);
            if (!$post) { 
                $post = find_scheduled(null, null, $name);
                if (!$post) {
                    not_found();
                }
            }

        }

        $current = $post['current'];
        
        if (isset($current->image)) {
            $type= 'is_image';
        } elseif (isset($current->link)) {
            $type = 'is_link';
        } elseif (isset($current->quote)) {
            $type = 'is_quote';
        } elseif (isset($current->audio)) {
            $type = 'is_audio';
        } elseif (isset($current->video)) {
            $type = 'is_video'; 
        } else {
            $type = 'is_post';
        }
        
        if ($user === $current->author || $role === 'editor' || $role === 'admin') {
            render('edit-content', array(
                'title' => generate_title('is_default', $current->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'type' => $type,
                'is_admin' => true,
                'bodyclass' => 'edit-post',
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', $current->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from blog post
post('/'. permalink_type() .'/:name/edit', function () {
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $title = from($_REQUEST, 'title');
    $is_post = from($_REQUEST, 'is_post');
    $image = from($_REQUEST, 'image');
    $is_image = from($_REQUEST, 'is_image');
    $video = from($_REQUEST, 'video');
    $is_video = from($_REQUEST, 'is_video');
    $link = from($_REQUEST, 'link');
    $is_link = from($_REQUEST, 'is_link');
    $audio = from($_REQUEST, 'audio');
    $is_audio = from($_REQUEST, 'is_audio');
    $quote = from($_REQUEST, 'quote');
    $is_quote = from($_REQUEST, 'is_quote');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $date = from($_REQUEST, 'date');
    $time = from($_REQUEST, 'time');
    $comments = from($_REQUEST, 'comments');
    $dateTime = null;
    $revertPost = from($_REQUEST, 'revertpost');
    $publishDraft = from($_REQUEST, 'publishdraft');
    $category = from($_REQUEST, 'category');
    if ($date !== null && $time !== null) {
        $dateTime = $date . ' ' . $time;
    }
    
    if (!empty($is_image)) {
        $type = 'is_image';
    } elseif (!empty($is_video)) {
        $type = 'is_video';
    } elseif (!empty($is_link)) {
        $type = 'is_link';
    } elseif (!empty($is_quote)) {
        $type = 'is_quote';
    } elseif (!empty($is_audio)) {
        $type = 'is_audio';
    } elseif (!empty($is_post)) {
        $type = 'is_post';
    }
    
    if (empty($url)) {
        $url = $title;
    }
    
    $arr = explode('/', $oldfile); 
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if ($user === $arr[1] || $role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'image', $comments, $destination, $description, $dateTime, $image);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'video', $comments, $destination, $description, $dateTime, $video);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'link', $comments, $destination, $description, $dateTime, $link);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'quote', $comments, $destination, $description, $dateTime, $quote);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'audio', $comments, $destination, $description, $dateTime, $audio);
            
        }  else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($is_post)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'post', $comments, $destination, $description, $dateTime, null);
            
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($tag)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_tag') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }

            if (!empty($is_image)) {
                if (empty($image)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_image') . '</li>';
                }
            } elseif (!empty($is_video)) {
                if (empty($video)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_video') . '</li>';
                }
            } elseif (!empty($is_link)) {
                if (empty($link)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_link') . '</li>';
                }
            } elseif (!empty($is_quote)) {
                if (empty($quote)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_quote') . '</li>';
                }
            } elseif (!empty($is_audio)) {
                if (empty($audio)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_audio') . '</li>';
                }
            }
            
            config('views.root', 'system/admin/views');

            render('edit-content', array(
                'title' => generate_title('is_default', $title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'oldfile' => $oldfile,
                'postTitle' => $title,
                'postImage' => $image,
                'postVideo' => $video,
                'postLink' => $link,
                'postQuote' => $quote,
                'postAudio' => $audio,
                'postTag' => $tag,
                'postUrl' => $url,
                'type' => $type,
                'is_admin' => true,
                'postComments' => $comments,
                'postContent' => $content,
                'bodyclass' => 'edit-post',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Edit_content')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");
    }
});

// Delete blog post
get('/'. permalink_type() .'/:name/delete', function ($name) {

    if (login()) {

        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post(null, null, $name);

        if (!$post) {
            $post = find_draft(null, null, $name);
            if (!$post) { 
                $post = find_scheduled(null, null, $name);
                if (!$post) {
                    not_found();
                }
            }

        }

        $current = $post['current'];
        
        if (config('blog.enable') === 'true') {
            $blog = '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . site_url() . 'blog"><span itemprop="name">Blog</span></a><meta itemprop="position" content="2" /></li> &#187; ';
        } else {
            $blog = '';
        }

        if ($user === $current->author || $role === 'editor' || $role === 'admin') {
            render('delete-post', array(
                'title' => generate_title('is_default', i18n('Delete')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', 'Delete post'),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted data from blog post
post('/'. permalink_type() .'/:name/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        $arr = explode('/', $file); 
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($user === $arr[1] || $role === 'editor' || $role === 'admin') {
            delete_post($file, $destination);
        } else {
            $redir = site_url();
            header("location: $redir");    
        }
    }
});

// Show various page (top-level), admin, login, sitemap, static page.
get('/:static', function ($static) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (strpos($static, ".xml") !== false) {
        if ($static === 'sitemap.xml') {
            $sitemap = 'index.xml';
        } else {
            $sitemap = str_replace('sitemap.', '', $static);
        }
        header('Content-Type: text/xml');
        generate_sitemap($sitemap);
        die;
    } elseif ($static === 'admin') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('main', array(
                'title' => generate_title('is_default', i18n('Admin')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'admin-front',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Admin')
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === 'login') {
        if (session_status() == PHP_SESSION_NONE) session_start();
        config('views.root', 'system/admin/views');
        render('login', array(
            'title' => generate_title('is_default', i18n('Login')),
            'description' => 'Login page from ' . blog_title() . '.',
            'canonical' => site_url() . '/login',
            'metatags' => generate_meta(null, null),
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Login')
        ));
        die;
    } elseif ($static === 'logout') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('logout', array(
                'title' => generate_title('is_default', i18n('Logout')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'in-logout',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Logout')
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === blog_path()) {
    
        if(config('blog.enable') !== 'true') {
            $url = site_url();
            header("Location: $url");
        }
        
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('posts.perpage');

        $posts = get_posts(null, $page, $perpage);

        $total = '';
        
        $vroot = rtrim(config('views.root'), '/');
        
        $lt = $vroot . '/layout--blog.html.php'; 
        if (file_exists($lt)) {
            $layout = 'layout--blog';
        } else {
            $layout = '';
        }
        
        $pv = $vroot . '/main--blog.html.php'; 
        if (file_exists($pv)) {
            $pview = 'main--blog';
        } else {
            $pview = 'main';
        }

        if (empty($posts) || $page < 1) {

            // a non-existing page
            render('no-posts', array(
                'title' => generate_title('is_blog', null),
                'description' => blog_title() . ' ' . blog_string(),
                'canonical' => site_url(),
                'metatags' => generate_meta('is_blog', null),
                'bodyclass' => 'no-posts',
                'is_front' => true,
            ), $layout);

            die;
        }
        
        if ($page > 1) {
            $CanonicalPageNum = '?page=' . $page;
        } else {
            $CanonicalPageNum = NULL;
        }

        render($pview, array(
            'title' => generate_title('is_blog', null),
            'description' => blog_title() . ' ' . blog_string(),
            'canonical' => site_url() . blog_path() . $CanonicalPageNum,
            'metatags' => generate_meta('is_blog', null),
            'page' => $page,
            'posts' => $posts,
            'bodyclass' => 'in-blog',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . blog_string(),
            'pagination' => has_pagination($total, $perpage, $page),
            'is_blog' => true
        ), $layout);
    } elseif ($static === 'front') {

        $redir = site_url();
        header("location: $redir", TRUE, 301);

    } else {

        if (config("views.counter") != "true") {
            if (!login()) {
                file_cache($_SERVER['REQUEST_URI']);
            }
        } else {
            add_view('page_' . $static);
            if (!login()) {
                file_cache($_SERVER['REQUEST_URI']);
            }        
        }

        $post = find_page($static);

        if (!$post) {
            not_found('page_' . $static);
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

        $post = $post['current'];
        
        $vroot = rtrim(config('views.root'), '/');
        
        $lt = $vroot . '/layout--' . strtolower($static) . '.html.php';
        $ls = $vroot . '/layout--static.html.php'; 
        if (file_exists($lt)) {
            $layout = 'layout--' . strtolower($static);
        } else if (file_exists($ls)) {
            $layout = 'layout--static';
        } else {
            $layout = '';
        }
        
        $pv = $vroot . '/static--' . strtolower($static) . '.html.php'; 
        if (file_exists($pv)) {
            $pview = 'static--' . strtolower($static);
        } else {
            $pview = 'static';
        }

        render($pview, array(
            'title' => generate_title('is_page', $post),
            'description' => $post->description,
            'canonical' => $post->url,
            'metatags' => generate_meta('is_page', $post),
            'bodyclass' => 'in-page ' . strtolower($static),
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'static' => $post,
            'type' => 'is_page',
            'prev' => static_prev($prev),
            'next' => static_next($next),
            'is_page' => true
        ), $layout);
    }
});

// Show the add sub static page
get('/:static/add', function ($static) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = find_page($static);

            if (!$post) {
                not_found();
            }

            $post = $post['current'];

            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_new_page')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_subpage',
                'parent' => $static,
                'is_admin' => true,
                'bodyclass' => 'add-page',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . site_url() . 'admin/pages/' . $post->slug . '">' . $post->title . '</a> &#187; ' . i18n('Add_new_page')
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from add sub static page
post('/:static/add', function ($static) {
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $draft = from($_REQUEST, 'draft');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content)) {
            add_sub_page($title, $url, $content, $static, $draft, $description);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-page', array(
                'title' => generate_title('is_default', i18n('Add_new_page')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_subpage',
                'is_admin' => true,
                'bodyclass' => 'add-page',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $title . '">' . $title . '</a> &#187; ' . i18n('Add_new_page')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
});

// Show edit the static page
get('/:static/edit', function ($static) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = find_page($static);

            if (!$post) {
                $post = find_draft_page($static);
                if (!$post) {
                    not_found();
                } else {
                    $post = $post[0];
                }
            } else {
                $post = $post['current'];            
            }

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit') .  ': ' . $post->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'edit-page',
                'is_admin' => true,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="'. site_url() .'admin/pages">' .i18n('pages').'</a> &#187; ' . $post->title,
                'p' => $post,
                'static' => $post,
                'type' => 'is_page',
                'parent' => ''
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_page',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from static page
post('/:static/edit', function () {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $revertPage = from($_REQUEST, 'revertpage');
    $publishDraft = from($_REQUEST, 'publishdraft');
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content)) {
            edit_page($title, $url, $content, $oldfile, $revertPage, $publishDraft, $destination, $description);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit') .  ': ' . $post->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'oldfile' => $oldfile,
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'bodyclass' => 'edit-page',
                'is_admin' => true,
                'type' => 'is_page',
                'parent' => '',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Edit')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");            
    }
});

// Deleted the static page
get('/:static/delete', function ($static) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = find_page($static);

            if (!$post) {
                $post = find_draft_page($static);
                if (!$post) {
                    not_found();
                } else {
                    $post = $post[0];
                }
            } else {
                $post = $post['current'];            
            }

            render('delete-page', array(
                'title' => generate_title('is_default', i18n('Delete') . ': ' . $post->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'delete-page',
                'is_admin' => true,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Delete') . ': ' . $post->title,
                'p' => $post,
                'static' => $post,
                'type' => 'is_page',
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted data for static page
post('/:static/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $file = from($_REQUEST, 'file');
            $destination = from($_GET, 'destination');
            delete_page($file, $destination);
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    }
});

// Show the sb static page
get('/:static/:sub', function ($static, $sub) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
    
    if ($static === 'front') {
        $redir = site_url();
        header("location: $redir", TRUE, 301);
    }
    
    if (config("views.counter") != "true") {
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
    } else {
        add_view('subpage_' . $static.'.'.$sub);
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }        
    }

    $parent_post = find_page($static);
    if (!$parent_post) {
        not_found('subpage_' . $static.'.'.$sub);
    }
    $post = find_subpage($static, $sub);
    
    if (!$post) {
        not_found('subpage_' . $static.'.'.$sub);
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
    
    $post = $post['current'];
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--' . strtolower($static) . '--' . strtolower($sub) . '.html.php';
    $ls = $vroot . '/layout--' . strtolower($static) . '.html.php';
    $lf = $vroot . '/layout--static.html.php';
    if (file_exists($lt)) {
        $layout = 'layout--' . strtolower($static) . '--' . strtolower($sub);
    } else if (file_exists($ls)) {
        $layout = 'layout--' . strtolower($static);
    } else if (file_exists($lf)) {
        $layout = 'layout--static';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/static--' . strtolower($static) . '--' . strtolower($sub) . '.html.php';
    $ps = $vroot . '/static--' . strtolower($static) . '.html.php';
    if (file_exists($pv)) {
        $pview = 'static--' . strtolower($static) . '--' . strtolower($sub);
    } else if (file_exists($ps)) {
        $pview = 'static--' . strtolower($static);
    } else {
        $pview = 'static';
    }

    render($pview, array(
        'title' => generate_title('is_subpage', $post),
        'description' => $post->description,
        'canonical' => $post->url,
        'metatags' => generate_meta('is_subpage', $post),
        'bodyclass' => 'in-page ' . strtolower($static) . ' ' . strtolower($sub) ,
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $parent_post['current']->url . '">' . $parent_post['current']->title . '</a> &#187; ' . $post->title,
        'p' => $post,
        'static' => $post,
        'parent' => $parent_post,
        'prev' => static_prev($prev),
        'next' => static_next($next),
        'type' => 'is_subpage',
        'is_subpage' => true
    ), $layout);
});

// Edit the sub static page
get('/:static/:sub/edit', function ($static, $sub) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = find_page($static);

            if (!$post) {
                not_found();
            }

            $post = $post['current'];

            $page = find_subpage($static, $sub);

            if (!$page) {
                $page = find_draft_subpage($static, $sub);
                if (!$page) {
                    not_found();
                } else {
                    $page = $page[0];
                }
            } else {
                $page = $page['current'];            
            }

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit') . ': ' . $page->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'edit-page',
                'is_admin' => true,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . site_url() . 'admin/pages/' . $post->slug . '">' . $post->title . '</a> &#187; ' . $page->title,
                'p' => $page,
                'static' => $page,
                'type' => 'is_subpage',
                'parent' => $static
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_subpage',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from edit sub static page
post('/:static/:sub/edit', function ($static, $sub) {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $revertPage = from($_REQUEST, 'revertpage');
    $publishDraft = from($_REQUEST, 'publishdraft');
    if ($destination === null) {
        $destination = $static . "/" . $sub;
    }
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (empty($url)) {
        $url = $title;
    }
    if ($role === 'editor' || $role === 'admin') {
        if ($proper && !empty($title) && !empty($content)) {
            edit_page($title, $url, $content, $oldfile, $revertPage, $publishDraft, $destination, $description, $static);
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }
            config('views.root', 'system/admin/views');

            render('edit-page', array(
                'title' => generate_title('is_default', i18n('Edit') . ': ' . $page->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'oldfile' => $oldfile,
                'postTitle' => $title,
                'postUrl' => $url,
                'postContent' => $content,
                'static' => $static,
                'sub' => $sub,
                'type' => 'is_subpage',
                'bodyclass' => 'edit-page',
                'is_admin' => true,
                'parent' => $static,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Edit')
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");        
    }
});

// Delete sub static page
get('/:static/:sub/delete', function ($static, $sub) {
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'editor' || $role === 'admin') {
            $post = find_page($static);

            if (!$post) {
                not_found();
            }

            $post = $post['current'];

            $page = find_subpage($static, $sub);

            if (!$page) {
                $page = find_draft_subpage($static, $sub);
                if (!$page) {
                    not_found();
                } else {
                    $page = $page[0];
                }
            } else {
                $page = $page['current'];            
            }

            render('delete-page', array(
                'title' => generate_title('is_default', i18n('Delete') . ': ' . $page->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'bodyclass' => 'delete-page',
                'is_admin' => true,
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . site_url() . 'admin/pages/' . $post->slug . '">' . $post->title . '</a> &#187; ' . $page->title,
                'p' => $page,
                'static' => $page,
                'type' => 'is_subpage',
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Denied')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'type' => 'is_subpage',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . i18n('Denied')
            ));            
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted data from delete sub static page
post('/:static/:sub/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($role === 'editor' || $role === 'admin') {
            $file = from($_REQUEST, 'file');
            $destination = from($_GET, 'destination');
            delete_page($file, $destination);
        } else {
            $redir = site_url();
            header("location: $redir");                
        }
    }
});

// Show blog post with year-month
get('/:year/:month/:name', function ($year, $month, $name) {

    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
    
    if (permalink_type() !== 'default') {
        $redir = site_url() . permalink_type() . '/' . $name;
        header("location: $redir", TRUE, 301);
    }

    if (config("views.counter") != "true") {
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
    } else {
        add_view('post_' . $name);
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }        
    }

    $post = find_post($year, $month, $name);

    if (is_null($post)) {
        not_found('post_'. $name);
    } else {
        $current = $post['current'];
    }

    $author = new stdClass;
    $author->url = $current->authorUrl;
    $author->name = $current->authorName;
    $author->description = $current->authorDescription;
    $author->about = $current->authorAbout;
    $author->avatar = $current->authorAvatar;

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
    
    if (isset($current->image)) {
        $var = 'imagePost';
    } elseif (isset($current->link)) {
        $var = 'linkPost';
    } elseif (isset($current->quote)) {
        $var = 'quotePost';
    } elseif (isset($current->audio)) {
        $var = 'audioPost';
    } elseif (isset($current->video)) {
        $var = 'videoPost'; }
    else {
        $var = 'blogPost';
    }
    
    if (config('blog.enable') === 'true') {
        $blog = '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . site_url() . blog_path() . '"><span itemprop="name">' . blog_string() . '</span></a><meta itemprop="position" content="2" /></li> &#187; ';
    } else {
        $blog = '';
    }
    
    $vroot = rtrim(config('views.root'), '/');
    
    $lt = $vroot . '/layout--post--' . $current->ct . '.html.php'; 
    $pt = $vroot . '/layout--post--' . $current->type . '.html.php';
    $ls = $vroot . '/layout--post.html.php'; 
    if (file_exists($lt)) {
        $layout = 'layout--post--' . $current->ct;
    } else if (file_exists($pt)) {
        $layout = 'layout--post--' . $current->type;
    } else if (file_exists($ls)) {
        $layout = 'layout--post';
    } else {
        $layout = '';
    }
    
    $pv = $vroot . '/post--' . $current->ct . '.html.php'; 
    $pvt = $vroot . '/post--' . $current->type . '.html.php'; 
    if (file_exists($pv)) {
        $pview = 'post--' . $current->ct;
    } else if(file_exists($pvt)) {
        $pview = 'post--' . $current->type;
    } else {
        $pview = 'post';
    }
    
    render($pview, array(
        'title' => generate_title('is_post', $current),
        'description' => $current->description,
        'canonical' => $current->url,
        'metatags' => generate_meta('is_post', $current),
        'p' => $current,
        'post' => $current,
        'author' => $author,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type,
        'breadcrumb' => '<style>.breadcrumb-list {margin:0; padding:0;} .breadcrumb-list li {display: inline-block; list-style: none;}</style><ol class="breadcrumb-list" itemscope itemtype="http://schema.org/BreadcrumbList"><li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . site_url() . '"><span itemprop="name">' . config('breadcrumb.home') . '</span></a><meta itemprop="position" content="1" /></li> &#187; '. $blog . '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">' . $current->categoryb . '<meta itemprop="position" content="3" /></li>' . ' &#187; ' . $current->title . '</ol>',
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true
    ), $layout);

});

// Edit blog post
get('/:year/:month/:name/edit', function ($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            $post = find_draft($year, $month, $name);
            if (!$post) { 
                $post = find_scheduled($year, $month, $name);
                if (!$post) {
                    not_found();
                }
            }

        }

        $current = $post['current'];
        
        if (isset($current->image)) {
            $type= 'is_image';
        } elseif (isset($current->link)) {
            $type = 'is_link';
        } elseif (isset($current->quote)) {
            $type = 'is_quote';
        } elseif (isset($current->audio)) {
            $type = 'is_audio';
        } elseif (isset($current->video)) {
            $type = 'is_video'; 
        } else {
            $type = 'is_post';
        }
        
        if ($user === $current->author || $role === 'editor' || $role === 'admin') {
            render('edit-content', array(
                'title' => generate_title('is_default', $current->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'type' => $type,
                'bodyclass' => 'edit-post',
                'is_admin' => true,
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', $current->title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from blog post
post('/:year/:month/:name/edit', function () {

    if(!login()) {
        $login = site_url() . 'login';
        header("location: $login");
    }
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $title = from($_REQUEST, 'title');
    $is_post = from($_REQUEST, 'is_post');
    $image = from($_REQUEST, 'image');
    $is_image = from($_REQUEST, 'is_image');
    $video = from($_REQUEST, 'video');
    $is_video = from($_REQUEST, 'is_video');
    $link = from($_REQUEST, 'link');
    $is_link = from($_REQUEST, 'is_link');
    $audio = from($_REQUEST, 'audio');
    $is_audio = from($_REQUEST, 'is_audio');
    $quote = from($_REQUEST, 'quote');
    $is_quote = from($_REQUEST, 'is_quote');
    $tag = from($_REQUEST, 'tag');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    $date = from($_REQUEST, 'date');
    $time = from($_REQUEST, 'time');
    $comments = $_REQUEST['comments'];
    $dateTime = null;
    $revertPost = from($_REQUEST, 'revertpost');
    $publishDraft = from($_REQUEST, 'publishdraft');
    $category = from($_REQUEST, 'category');
    if ($date !== null && $time !== null) {
        $dateTime = $date . ' ' . $time;
    }
    
    if (!empty($is_image)) {
        $type = 'is_image';
    } elseif (!empty($is_video)) {
        $type = 'is_video';
    } elseif (!empty($is_link)) {
        $type = 'is_link';
    } elseif (!empty($is_quote)) {
        $type = 'is_quote';
    } elseif (!empty($is_audio)) {
        $type = 'is_audio';
    } elseif (!empty($is_post)) {
        $type = 'is_post';
    }

    if (empty($url)) {
        $url = $title;
    }

    $arr = explode('/', $oldfile); 
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    if ($user === $arr[1] || $role === 'editor' || $role === 'admin') {

        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'image', $comments, $destination, $description, $dateTime, $image);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'video', $comments, $destination, $description, $dateTime, $video);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'link', $comments, $destination, $description, $dateTime, $link);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'quote', $comments, $destination, $description, $dateTime, $quote);
            
        } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'audio', $comments, $destination, $description, $dateTime, $audio);
            
        }  else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($is_post)) {

            edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, 'post', $comments, $destination, $description, $dateTime, null);
            
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_title') . '</li>';
            }
            if (empty($tag)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_tag') . '</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_content') . '</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li class="alert alert-danger">' . i18n('Token_Error') . '</li>';
            }

            if (!empty($is_image)) {
                if (empty($image)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_image') . '</li>';
                }
            } elseif (!empty($is_video)) {
                if (empty($video)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_video') . '</li>';
                }
            } elseif (!empty($is_link)) {
                if (empty($link)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_link') . '</li>';
                }
            } elseif (!empty($is_quote)) {
                if (empty($quote)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_quote') . '</li>';
                }
            } elseif (!empty($is_audio)) {
                if (empty($audio)) {
                    $message['error'] .= '<li class="alert alert-danger">' . i18n('msg_error_field_req_audio') . '</li>';
                }
            }
            
            config('views.root', 'system/admin/views');

            render('edit-content', array(
                'title' => generate_title('is_default', $title),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'oldfile' => $oldfile,
                'postTitle' => $title,
                'postImage' => $image,
                'postVideo' => $video,
                'postLink' => $link,
                'postQuote' => $quote,
                'postAudio' => $audio,
                'postTag' => $tag,
                'postUrl' => $url,
                'type' => $type,
                'postContent' => $content,
                'postComments' => $comments,
                'is_admin' => true,
                'bodyclass' => 'edit-post',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $title
            ));
        }
    } else {
        $redir = site_url();
        header("location: $redir");
    }
});

// Delete blog post
get('/:year/:month/:name/delete', function ($year, $month, $name) {

    if (login()) {

        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post($year, $month, $name);

        if (!$post) {
            $post = find_draft($year, $month, $name);
            if (!$post) { 
                $post = find_scheduled($year, $month, $name);
                if (!$post) {
                    not_found();
                }
            }

        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'editor' || $role === 'admin') {
            render('delete-post', array(
                'title' => generate_title('is_default', i18n('Delete')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
                'breadcrumb' => '<span><a rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => generate_title('is_default', i18n('Delete')),
                'description' => safe_html(strip_tags(blog_description())),
                'canonical' => site_url(),
                'metatags' => generate_meta(null, null),
                'p' => $current,
                'post' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
                'breadcrumb' => '<span><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->categoryb . ' &#187; ' . $current->title
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
        $arr = explode('/', $file); 
        $user = $_SESSION[site_url()]['user'];
        $role = user('role', $user);
        if ($user === $arr[1] || $role === 'editor' || $role === 'admin') {
            delete_post($file, $destination);
        }
    }
});

// If we get here, it means that
// nothing has been matched above
get('.*', function () {
    not_found();
});

// Serve the blog
dispatch();
