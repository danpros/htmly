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

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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
        
        $tl = strip_tags(blog_tagline());

        if ($tl) {
            $tagline = ' - ' . $tl;
        } else {
            $tagline = '';
        }
        
        $pv = $vroot . '/static--front.html.php'; 
        if (file_exists($pv)) {
            $pview = 'static--front';
        } else {
            $pview = 'static';
        }
            
        render($pview, array(
            'title' => blog_title() . $tagline,
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'p' => $front,
            'type' => 'is_frontpage',
            'is_front' => true,
        ), $layout);
        
        
    } else {
    
        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('posts.perpage');

        $posts = get_posts(null, $page, $perpage);

        $total = '';

        $tl = strip_tags(blog_tagline());

        if ($tl) {
            $tagline = ' - ' . $tl;
        } else {
            $tagline = '';
        }
        
        $pv = $vroot . '/main--front.html.php'; 
        if (file_exists($pv)) {
            $pview = 'main--front';
        } else {
            $pview = 'main';
        }

        if (empty($posts) || $page < 1) {

            // a non-existing page
            render('no-posts', array(
                'title' => blog_title() . $tagline,
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'no-posts',
                'type' => 'is_frontpage',
                'is_front' => true,
            ), $layout);

            die;
        }

        render($pview, array(
            'title' => blog_title() . $tagline,
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'page' => $page,
            'posts' => $posts,
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'pagination' => has_pagination($total, $perpage, $page),
            'type' => 'is_frontpage',
            'is_front' => true,
        ), $layout);
    
    }
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
                'type' => 'is_login',
                'is_login' => true,
                'bodyclass' => 'in-login',
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
            'type' => 'is_login',
            'is_login' => true,
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Show the author page
get('/author/:name', function ($name) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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

    $total = get_count($name, 'dirname');

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
            'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
            'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
            'canonical' => site_url() . 'author/' . $name,
            'page' => $page,
            'posts' => null,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_profile',
            'bodyclass' => 'in-profile author-' . $name,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page),
            'is_profile' => true,
        ), $layout);
        die;
    }

    render($pview, array(
        'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
        'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'author/' . $name,
        'page' => $page,
        'posts' => $posts,
        'about' => $author->about,
        'name' => $author->name,
        'type' => 'is_profile',
        'bodyclass' => 'in-profile author-' . $name,
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_profile' => true,
    ), $layout);
});

// Edit the profile
get('/edit/profile', function () {

    if (login()) {

        config('views.root', 'system/admin/views');
        render('edit-page', array(
            'title' => 'Edit profile - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
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

        render('edit-page', array(
            'title' => 'Edit profile - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile'
        ));
    }
});

// Edit the frontpage
get('/edit/frontpage', function () {

    if (login()) {

        config('views.root', 'system/admin/views');
        render('edit-page', array(
            'title' => 'Edit frontpage - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_frontpage',
            'is_admin' => true,
            'bodyclass' => 'edit-frontpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit frontpage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get submitted data from edit frontpage
post('/edit/frontpage', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $user = $_SESSION[config("site.url")]['user'];
    $title = from($_REQUEST, 'title');
    $content = from($_REQUEST, 'content');
    if ($proper && !empty($title) && !empty($content)) {
        edit_frontpage($title, $content);
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
            'title' => 'Edit frontpage - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'type' => 'is_frontpage',
            'is_admin' => true,
            'bodyclass' => 'edit-frontpage',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit frontpage'
        ));
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

// Show the "Add post" page
get('/add/content', function () {

    $req = $_GET['type'];
    
    $type = 'is_' . $req;

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-content', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted add post data
post('/add/content', function () {

    $is_image = from($_REQUEST, 'is_image');
    $is_audio = from($_REQUEST, 'is_audio');
    $is_video = from($_REQUEST, 'is_video');
    $is_quote = from($_REQUEST, 'is_quote');
    $is_link = from($_REQUEST, 'is_link');
    $is_post = from($_REQUEST, 'is_post');
    
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
    $user = $_SESSION[config("site.url")]['user'];
    $draft = from($_REQUEST, 'draft');
    $category = from($_REQUEST, 'category');
    
    if (empty($is_post) && empty($is_image) && empty($is_video) && empty($is_audio) && empty($is_link) && empty($is_quote)) {
        $add = site_url() . 'admin/content';
        header("location: $add");    
    }
    
    if (!empty($is_post)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, null, $draft, $category, 'post');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, null, $draft, $category, 'post');
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
            render('add-content', array(
                'title' => 'Add post- ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_post',
                'is_admin' => true,
                'bodyclass' => 'add-post',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
            ));
        }
    }
    
    if (!empty($is_image)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, $image, $draft, $category, 'image');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, $image, $draft, $category, 'image');
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
            if (empty($image)) {
                $message['error'] .= '<li>Image field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-content', array(
                'title' => 'Add image - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postImage' => $image,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_image',
                'is_admin' => true,
                'bodyclass' => 'add-image',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add image'
            ));
        }
    }
    
    if (!empty($is_video)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, $video, $draft, $category, 'video');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, $video, $draft, $category, 'video');
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
            if (empty($video)) {
                $message['error'] .= '<li>Video field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-content', array(
                'title' => 'Add video - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postVideo' => $video,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_video',
                'is_admin' => true,
                'bodyclass' => 'add-video',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add video'
            ));
        }
    }
    
    if (!empty($is_audio)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, $audio, $draft, $category, 'audio');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, $audio, $draft, $category, 'audio');
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
            if (empty($audio)) {
                $message['error'] .= '<li>Audio field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-content', array(
                'title' => 'Add audio - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postAudio' => $audio,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_audio',
                'is_admin' => true,
                'bodyclass' => 'add-audio',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add audio'
            ));
        }
    }
    
    if (!empty($is_quote)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, $quote, $draft, $category, 'quote');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, $quote, $draft, $category, 'quote');
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
            if (empty($quote)) {
                $message['error'] .= '<li>Quote field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-content', array(
                'title' => 'Add quote - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postQuote' => $quote,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_quote',
                'is_admin' => true,
                'bodyclass' => 'add-quote',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add Quote'
            ));
        }
    }
    
    if (!empty($is_link)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {
            if (!empty($url)) {
                add_content($title, $tag, $url, $content, $user, $description, $link, $draft, $category, 'link');
            } else {
                $url = $title;
                add_content($title, $tag, $url, $content, $user, $description, $link, $draft, $category, 'link');
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
            if (empty($link)) {
                $message['error'] .= '<li>Link field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'system/admin/views');
            render('add-content', array(
                'title' => 'Add link - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postLink' => $link,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_link',
                'is_admin' => true,
                'bodyclass' => 'add-link',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add link'
            ));
        }
    }
    
});

// Show the static add page
get('/add/page', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    }
});

// Show the add category
get('/add/category', function () {

    if (login()) {

        config('views.root', 'system/admin/views');

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'add-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Submitted add category 
post('/add/category', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && login()) {
        if (!empty($url)) {
            add_category($title, $url, $content, $description);
        } else {
            $url = $title;
            add_category($title, $url, $content, $description);
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'add-category',
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
                    'description' => strip_tags(blog_description()),
                    'canonical' => site_url(),
                    'bodyclass' => 'no-posts',
                ));

                die;
            }

            $tl = strip_tags(blog_tagline());

            if ($tl) {
                $tagline = ' - ' . $tl;
            } else {
                $tagline = '';
            }

            render('posts-list', array(
                'title' => 'All blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'heading' => 'All blog posts',
                'page' => $page,
                'posts' => $posts,
                'bodyclass' => 'all-posts',
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => 'All blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show admin/popular 
get('/admin/popular', function () {

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (login()) {

        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            config('views.root', 'system/admin/views');
            $page = from($_GET, 'page');
            $page = $page ? (int)$page : 1;
            $perpage = 20;

            $posts = popular_posts(true,$perpage);

            $total = '';

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'title' => 'Popular posts - ' . blog_title(),
                    'description' => strip_tags(blog_description()),
                    'canonical' => site_url(),
                    'is_admin' => true,
                    'bodyclass' => 'admin-popular',
                ));

                die;
            }

            $tl = strip_tags(blog_tagline());

            if ($tl) {
                $tagline = ' - ' . $tl;
            } else {
                $tagline = '';
            }

            render('popular-posts', array(
                'title' => 'Popular posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'heading' => 'Popular posts',
                'page' => $page,
                'posts' => $posts,
                'is_admin' => true,
                'bodyclass' => 'admin-popular',
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => 'Popular posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'is_admin' => true,
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

        $name = $_SESSION[config("site.url")]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_profile_posts($name, $page, $perpage);

        $total = get_count($name, 'dirname');

        $author = get_author($name);

        if (isset($author[0])) {
            $author = $author[0];
        } else {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('user-posts', array(
                'title' => 'My blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'page' => $page,
                'heading' => 'My posts',
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-mine',
                'is_admin' => true,
                'bodyclass' => 'admin-mine',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('user-posts', array(
            'title' => 'My blog posts - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'heading' => 'My posts',
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-mine',
            'is_admin' => true,
            'bodyclass' => 'admin-mine',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
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

        $name = $_SESSION[config("site.url")]['user'];

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('profile.perpage');

        $posts = get_draft($name, $page, $perpage);

        $total = get_count($name, 'dirname');

        $author = get_author($name);

        if (isset($author[0])) {
            $author = $author[0];
        } else {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('user-draft', array(
                'title' => 'My draft - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'page' => $page,
                'heading' => 'My draft',
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-draft',
                'is_admin' => true,
                'bodyclass' => 'admin-draft',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $author->name,
            ));
            die;
        }

        render('user-draft', array(
            'title' => 'My draft - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'heading' => 'My draft',
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-draft',
            'is_admin' => true,
            'bodyclass' => 'admin-draft',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $author->name,
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
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-content',
            'is_admin' => true,
            'bodyclass' => 'admin-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add content'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show import page
get('/admin/import', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('import', array(
            'title' => 'Import feed - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-import',
            'is_admin' => true,
            'bodyclass' => 'admin-import',
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
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $log . '</ul>',
                'type' => 'is_admin-import',
                'is_admin' => true,
                'bodyclass' => 'admin-import',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'url' => $url,
            'type' => 'is_admin-import',
            'is_admin' => true,
            'bodyclass' => 'admin-import',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Show Config page
get('/admin/config', function () {

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);

    if (login()) {
        config('views.root', 'system/admin/views');
        if ($role === 'admin') {
            render('config', array(
                'title' => 'Config - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Config'
            ));
        } else {
            render('denied', array(
                'title' => 'Config page - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-backup',
            'is_admin' => true,
            'bodyclass' => 'admin-backup',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-backup-start',
            'is_admin' => true,
            'bodyclass' => 'admin-backup-start',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-clear-cache',
            'is_admin' => true,
            'bodyclass' => 'admin-clear-cache',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-update',
            'is_admin' => true,
            'bodyclass' => 'admin-update',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'info' => $updater->getCurrentInfo(),
            'type' => 'is_admin-update',
            'is_admin' => true,
            'bodyclass' => 'admin-update',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Update HTMLy'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Show category page
get('/admin/categories', function () {
    if (login()) {
        config('views.root', 'system/admin/views');
        render('categories', array(
            'title' => 'Categories - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-categories',
            'is_admin' => true,
            'bodyclass' => 'admin-categories',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Categories'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Show the category page
get('/category/:category', function ($category) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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
    
    if(strtolower($category) !== 'uncategorized') {
       $desc = $desc[0];
    }

    $total = get_categorycount($category);

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found();
    }
    
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
    
    render($pview, array(
        'title' => $desc->title . ' - ' . blog_title(),
        'description' => $desc->description,
        'canonical' => $desc->url,
        'page' => $page,
        'posts' => $posts,
        'category' => $desc,
        'bodyclass' => 'in-category category-' . strtolower($category),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $desc->title,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_category' => true,
    ), $layout);
});

// Show edit the category page
get('/category/:category/edit', function ($category) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_category_info($category);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('edit-page', array(
            'title' => 'Edit category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'edit-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from category page
post('/category/:category/edit', function () {

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
            edit_category($title, $url, $content, $oldfile, $destination, $description);
        } else {
            $url = $title;
            edit_category($title, $url, $content, $oldfile, $destination, $description);
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
            'title' => 'Edit category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'edit-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit category'
        ));
    }
});

// Delete category
get('/category/:category/delete', function ($category) {

    if (login()) {

        config('views.root', 'system/admin/views');
        $post = get_category_info($category);

        if (!$post) {
            not_found();
        }

        $post = $post[0];

        render('delete-category', array(
            'title' => 'Delete category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'delete-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'categoryPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted category data
post('/category/:category/delete', function () {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
});

// Show the type page
get('/type/:type', function ($type) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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
    $ttype->title = $type;

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
    
    render($pview, array(
        'title' => 'Posts with type: ' . ucfirst($type) . ' - ' . blog_title(),
        'description' => 'All posts with type: ' . ucfirst($type) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'type/' . strtolower($type),
        'page' => $page,
        'posts' => $posts,
        'type' => $ttype,
        'bodyclass' => 'in-type type-' . strtolower($type),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . ucfirst($type),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_type' => true,
    ), $layout);
});

// Show the tag page
get('/tag/:tag', function ($tag) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int)$page : 1;
    $perpage = config('tag.perpage');

    $posts = get_tag($tag, $page, $perpage, false);

    $total = get_tagcount($tag, 'basename');
        
    $ttag = new stdClass;
    $ttag->title = tag_i18n($tag);

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
    
    render($pview, array(
        'title' => 'Posts tagged: ' . tag_i18n($tag) . ' - ' . blog_title(),
        'description' => 'All posts tagged: ' . tag_i18n($tag) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'tag/' . strtolower($tag),
        'page' => $page,
        'posts' => $posts,
        'tag' => $ttag,
        'bodyclass' => 'in-tag tag-' . strtolower($tag),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Posts tagged: ' . tag_i18n($tag),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_tag' => true,
    ), $layout);
});

// Show the archive page
get('/archive/:req', function ($req) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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
        $timestamp = date('d F Y', $date);
    } elseif (isset($time[0]) && isset($time[1])) {
        $timestamp = date('F Y', $date);
    } else {
        $timestamp = $req;
    }
    
    $tarchive = new stdClass;
    $tarchive->title = $timestamp;

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

    render($pview, array(
        'title' => 'Archive for: ' . $timestamp . ' - ' . blog_title(),
        'description' => 'Archive page for: ' . $timestamp . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'archive/' . $req,
        'page' => $page,
        'posts' => $posts,
        'archive' => $tarchive,
        'bodyclass' => 'in-archive archive-' . strtolower($req),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Archive for: ' . $timestamp,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_archive' => true,
    ), $layout);
});

// Show the search page
get('/search/:keyword', function ($keyword) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
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
    
    $tsearch = new stdClass;
    $tsearch->title = $keyword;
    
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
            'title' => 'Search results not found! - ' . blog_title(),
            'description' => 'Search results not found!',
            'search' => $tsearch,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; No search results',
            'canonical' => site_url(),
            'bodyclass' => 'error-404-search',
            'is_404search' => true,
        ), $layout);
        die;
    }

    $total = keyword_count($keyword);
    
    $pv = $vroot . '/main--search.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--search';
    } else {
        $pview = 'main';
    }

    render($pview, array(
        'title' => 'Search results for: ' . tag_i18n($keyword) . ' - ' . blog_title(),
        'description' => 'Search results for: ' . tag_i18n($keyword) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'search/' . strtolower($keyword),
        'page' => $page,
        'posts' => $posts,
        'search' => $tsearch,
        'bodyclass' => 'in-search search-' . strtolower($keyword),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Search results for: ' . tag_i18n($keyword),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_search' => true,
    ), $layout);
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
get('/post/:name', function ($name) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (config('permalink.type') != 'post') {
        $post = find_post(null, null, $name);
        $current = $post['current'];
        $redir = site_url() . date('Y/m', $current->date) . '/' . $name;
        header("location: $redir", TRUE, 301);
    }

    if (config("views.counter") != "true") {
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }
    }

    $post = find_post(null, null, $name);

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

    $author = get_author($current->author);

    if (isset($author[0])) {
        $author = $author[0];
    } else {
        $author = default_profile($current->author);
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
        $blog = ' <span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . 'blog">Blog</a></span> &#187; ';
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
        'title' => $current->title . ' - ' . blog_title(),
        'description' => $current->description,
        'canonical' => $current->url,
        'p' => $current,
        'author' => $author,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type,
        'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; '. $blog . '<span typeof="v:Breadcrumb">' . $current->categoryb . '</span>' . ' &#187; ' . $current->title,
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true,
    ), $layout);

});

// Edit blog post
get('/post/:name/edit', function ($name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post(null, null, $name);

        if (!$post) {
            $post = find_draft(null, null, $name);
            if (!$post) {
                not_found();
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
        
        if ($user === $current->author || $role === 'admin') {
            render('edit-content', array(
                'title' => $type .' - '. blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'type' => $type,
                'is_admin' => true,
                'bodyclass' => 'edit-post',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => $type .' - '. blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get edited data from blog post
post('/post/:name/edit', function () {

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

    if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $image, $revertPost, $publishDraft, $category, 'image');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $video, $revertPost, $publishDraft, $category, 'video');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $link, $revertPost, $publishDraft, $category, 'link');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_contente($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $quote, $revertPost, $publishDraft, $category, 'quote');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $audio, $revertPost, $publishDraft, $category, 'audio');
        
    }  else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($is_post)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, null, $revertPost, $publishDraft, $category, 'post');
        
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

        if (!empty($is_image)) {
            if (empty($image)) {
                $message['error'] .= '<li>Image field is required.</li>';
            }
        } elseif (!empty($is_video)) {
            if (empty($video)) {
                $message['error'] .= '<li>Video field is required.</li>';
            }
        } elseif (!empty($is_link)) {
            if (empty($link)) {
                $message['error'] .= '<li>Link field is required.</li>';
            }
        } elseif (!empty($is_quote)) {
            if (empty($quote)) {
                $message['error'] .= '<li>Quote field is required.</li>';
            }
        } elseif (!empty($is_audio)) {
            if (empty($audio)) {
                $message['error'] .= '<li>Audio field is required.</li>';
            }
        }
        
        config('views.root', 'system/admin/views');

        render('edit-content', array(
            'title' => $type . ' - ' .  blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
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
            'postContent' => $content,
            'bodyclass' => 'edit-post',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit content'
        ));
    }
});

// Delete blog post
get('/post/:name/delete', function ($name) {

    if (login()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'system/admin/views');
        $post = find_post(null, null, $name);

        if (!$post) {
            $post = find_draft(null, null, $name);
            if (!$post) {
                not_found();
            }
        }

        $current = $post['current'];

        if ($user === $current->author || $role === 'admin') {
            render('delete-post', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
});

// Get deleted data from blog post
post('/post/:name/delete', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && login()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_post($file, $destination);
    }
});

// Show various page (top-level), admin, login, sitemap, static page.
get('/:static', function ($static) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if ($static === 'sitemap.xml' || $static === 'sitemap.base.xml' || $static === 'sitemap.post.xml' || $static === 'sitemap.static.xml' || $static === 'sitemap.tag.xml' || $static === 'sitemap.archive.xml' || $static === 'sitemap.author.xml' || $static === 'sitemap.category.xml' || $static === 'sitemap.type.xml') {

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
        } elseif ($static === 'sitemap.category.xml') {
            generate_sitemap('category');
        } elseif ($static === 'sitemap.type.xml') {
            generate_sitemap('type');
        }

        die;
    } elseif ($static === 'admin') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('main', array(
                'title' => 'Admin - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'admin-front',
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
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
        die;
    } elseif ($static === 'logout') {
        if (login()) {
            config('views.root', 'system/admin/views');
            render('logout', array(
                'title' => 'Logout - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'in-logout',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Logout'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === 'blog') {
    
        if(config('blog.enable') !== 'true') return not_found();
        
        if (!login()) {
            file_cache($_SERVER['REQUEST_URI']);
        }

        $page = from($_GET, 'page');
        $page = $page ? (int)$page : 1;
        $perpage = config('posts.perpage');

        $posts = get_posts(null, $page, $perpage);

        $total = '';

        $tl = strip_tags(blog_tagline());

        if ($tl) {
            $tagline = ' - ' . $tl;
        } else {
            $tagline = '';
        }
        
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
                'title' => 'Blog - ' . blog_title(),
                'description' => blog_title() . ' Blog Homepage',
                'canonical' => site_url(),
                'bodyclass' => 'no-posts',
                'is_front' => true,
            ), $layout);

            die;
        }

        render($pview, array(
            'title' => 'Blog - ' . blog_title(),
            'description' => blog_title() . ' Blog Homepage',
            'canonical' => site_url() . 'blog',
            'page' => $page,
            'posts' => $posts,
            'bodyclass' => 'in-blog',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Blog',
            'pagination' => has_pagination($total, $perpage, $page),
            'is_blog' => true,
        ), $layout);
    } elseif ($static === 'front') {

        $redir = site_url();
        header("location: $redir", TRUE, 301);

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
            'title' => $post->title . ' - ' . blog_title(),
            'description' => $post->description,
            'canonical' => $post->url,
            'bodyclass' => 'in-page ' . strtolower($static),
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticPage',
            'is_page' => true,
        ), $layout);
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $title . '">' . $title . '</a> Add page'
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticPage',
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'edit-page',
            'is_admin' => true,
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'delete-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title,
            'p' => $post,
            'type' => 'staticPage',
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

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

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
        'title' => $post->title . ' - ' . blog_title(),
        'description' => $post->description,
        'canonical' => $post->url,
        'bodyclass' => 'in-page ' . strtolower($static) . ' ' . strtolower($sub) ,
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $father_post[0]->url . '">' . $father_post[0]->title . '</a> &#187; ' . $post->title,
        'p' => $post,
        'type' => 'subPage',
        'is_subpage' => true,
    ), $layout);
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a> &#187; ',
            'p' => $page,
            'type' => 'subPage',
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
            edit_page($title, $url, $content, $oldfile, $destination, $description, $static);
        } else {
            $url = $title;
            edit_page($title, $url, $content, $oldfile, $destination, $description, $static);
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'static' => $static,
            'sub' => $sub,
            'bodyclass' => 'edit-page',
            'is_admin' => true,
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
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'delete-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->title . '</a>' . $page->title,
            'p' => $page,
            'type' => 'subPage',
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

// Show blog post with year-month
get('/:year/:month/:name', function ($year, $month, $name) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
    
    if (config('permalink.type') == 'post') {
        $redir = site_url() . 'post/' . $name;
        header("location: $redir", TRUE, 301);
    }

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

    $author = get_author($current->author);

    if (isset($author[0])) {
        $author = $author[0];
    } else {
        $author = default_profile($current->author);
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
        $blog = ' <span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . 'blog">Blog</a></span> &#187; ';
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
        'title' => $current->title . ' - ' . blog_title(),
        'description' => $current->description,
        'canonical' => $current->url,
        'p' => $current,
        'author' => $author,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type,
        'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; '. $blog . '<span typeof="v:Breadcrumb">' . $current->categoryb . '</span>' . ' &#187; ' . $current->title,
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true,
    ), $layout);

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
        
        if ($user === $current->author || $role === 'admin') {
            render('edit-content', array(
                'title' => $type .' - '. blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'type' => $type,
                'bodyclass' => 'edit-post',
                'is_admin' => true,
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => $type .' - '. blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
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

    if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $image, $revertPost, $publishDraft, $category, 'image');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($video)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $video, $revertPost, $publishDraft, $category, 'video');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($link)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $link, $revertPost, $publishDraft, $category, 'link');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($quote)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $quote, $revertPost, $publishDraft, $category, 'quote');
        
    } else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($audio)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $audio, $revertPost, $publishDraft, $category, 'audio');
        
    }  else if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($is_post)) {
        if (empty($url)) {
            $url = $title;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, null, $revertPost, $publishDraft, $category, 'post');
        
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

        if (!empty($is_image)) {
            if (empty($image)) {
                $message['error'] .= '<li>Image field is required.</li>';
            }
        } elseif (!empty($is_video)) {
            if (empty($video)) {
                $message['error'] .= '<li>Video field is required.</li>';
            }
        } elseif (!empty($is_link)) {
            if (empty($link)) {
                $message['error'] .= '<li>Link field is required.</li>';
            }
        } elseif (!empty($is_quote)) {
            if (empty($quote)) {
                $message['error'] .= '<li>Quote field is required.</li>';
            }
        } elseif (!empty($is_audio)) {
            if (empty($audio)) {
                $message['error'] .= '<li>Audio field is required.</li>';
            }
        }
        
        config('views.root', 'system/admin/views');

        render('edit-content', array(
            'title' => $type . ' - ' .  blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
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
            'is_admin' => true,
            'bodyclass' => 'edit-post',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit content'
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
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
                'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . site_url() . '">' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tagb . ' &#187; ' . $current->title
            ));
        } else {
            render('denied', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
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
