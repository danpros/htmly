<?php
if (!defined('HTMLY')) die('HTMLy');

use \Michelf\MarkdownExtra;

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

// Update the user
function update_user($userName, $password, $role)
{
    $file = 'config/users/' . $userName . '.ini';
    if (file_exists($file)) {
        file_put_contents($file, "password = " . password_hash($password, PASSWORD_DEFAULT) . "\n" .
            "encryption = password_hash\n" .
            "role = " . $role . "\n");
        return true;
    }
    return false;
}

// Create user
function create_user($userName, $password, $role = "user")
{
    $file = 'config/users/' . $userName . '.ini';
    if (file_exists($file)) {
        return false;
    } else {
        file_put_contents($file, "password = " . password_hash($password, PASSWORD_DEFAULT) . "\n" .
            "encryption = password_hash\n" .
            "role = " . $role . "\n");
        return true;
    }
}

// Create a session
function session($user, $pass)
{
    $user_file = 'config/users/' . $user . '.ini';
    if (!file_exists($user_file)) {
        return $str = '<div class="error-message"><ul><li class="alert alert-danger">' . i18n('Invalid_Error') . '</li></ul></div>';
    }

    $user_enc = user('encryption', $user);
    $user_pass = user('password', $user);
    $user_role = user('role', $user);
    
    if(is_null($user_enc) || is_null($user_pass) || is_null($user_role)) {
        return $str = '<div class="error-message"><ul><li class="alert alert-danger">' . i18n('Invalid_Error') . '</li></ul></div>';
    }

    if ($user_enc == "password_hash") {
        if (password_verify($pass, $user_pass)) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            if (password_needs_rehash($user_pass, PASSWORD_DEFAULT)) {
                update_user($user, $pass, $user_role);
            }
            $_SESSION[site_url()]['user'] = $user;
            header('location: admin');
        } else {
            return $str = '<div class="error-message"><ul><li class="alert alert-danger">' . i18n('Invalid_Error') . '</li></ul></div>';
        }
    } else if (old_password_verify($pass, $user_enc, $user_pass)) {
        if (session_status() == PHP_SESSION_NONE) session_start();
        update_user($user, $pass, $user_role);
        $_SESSION[site_url()]['user'] = $user;
        header('location: admin');
    } else {
        return $str = '<div class="error-message"><ul><li class="alert alert-danger">' . i18n('Invalid_Error') . '</li></ul></div>';
    }
}

function old_password_verify($pass, $user_enc, $user_pass)
{
    $password = (strlen($user_enc) > 0 && $user_enc !== 'clear' && $user_enc !== 'none') ? hash($user_enc, $pass) : $pass;
    return ($password === $user_pass);
}

// Generate csrf token
function generate_csrf_token()
{
    $_SESSION[site_url()]['csrf_token'] = sha1(microtime(true) . mt_rand(10000, 90000));
}

// Get csrf token
function get_csrf()
{
    if (!isset($_SESSION[site_url()]['csrf_token']) || empty($_SESSION[site_url()]['csrf_token'])) {
        generate_csrf_token();
    }
    return $_SESSION[site_url()]['csrf_token'];
}

// Check the csrf token
function is_csrf_proper($csrf_token)
{
    if ($csrf_token == get_csrf()) {
        return true;
    }
    return false;
}

// Clean URLs
function remove_accent($str)
{
    return URLify::downcode($str);
}

// Add content
function add_content($title, $tag, $url, $content, $user, $draft, $category, $type, $description = null, $media = null, $dateTime = null)
{

    $tag = explode(',', preg_replace("/\s*,\s*/", ",", rtrim($tag, ',')));
    $tag = array_filter(array_unique($tag));
    $tagslang = "content/data/tags.lang";
    if (file_exists($tagslang)) {
        $taglang = array_flip(unserialize(file_get_contents($tagslang)));
        $tflip = array_intersect_key($taglang, array_flip($tag));
        $post_tag = array();
        $post_tagmd = array();
        foreach ($tag as $t) {
            if (array_key_exists($t, $tflip)) {
                foreach ($tflip as $tfp => $tf){
                    if($t == $tfp) {
                        $post_tag[] = $tf;
                        $post_tagmd[] = $tfp;
                    }
                }
            } else {
                $post_tag[] = $t;
                $post_tagmd[] = $t;
            }
        }

        $post_tag = safe_tag(implode(',', $post_tag));
        $post_tagmd = safe_html(implode(',', $post_tagmd));        

    } else {
        $post_tag = safe_tag(implode(',', $tag));
        $post_tagmd = safe_html(implode(',', $tag));        
    }

    $post_date = date('Y-m-d-H-i-s', strtotime($dateTime));
    $post_title = safe_html($title); 
    $post_tag = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($post_tag)));
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);

    $post_t =  explode(',', $post_tag);
    $pret_t = explode(',', $post_tagmd);
    $tags = tag_cloud(true);
    $timestamp = date('YmdHis');

    $combine = array_combine($pret_t, $post_t);
    $inter = array_intersect_key($tags, array_flip($post_t));
    $newtag = array();

    foreach ($combine as $tag => $v) {
        if (array_key_exists($v, $tags)) {
            foreach ($inter as $in => $i){
                if($v == $in) {
                    if (strtolower($tag) == strtolower(tag_i18n($in))) {
                        $newtag[$v]= $tag;
                    } else {
                        $newtag[$v.'-'. $timestamp]= $tag;
                    }
                }
            }
        } else {
            $newtag[$v] = $tag;
        }            
    }

    $post_tag = implode(',', array_keys($newtag));

    $posts = get_blog_posts();
    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if (strtolower($arr[2]) === strtolower($post_url . '.md')) {
            $post_url = $post_url .'-'. $timestamp;
        } else {
            $post_url = $post_url;
        }
    }

    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    if ($tag !== null) {
        $tagmd = "\n<!--tag " . $post_tagmd . " tag-->";
    } else {
        $tagmd = "";
    }
    if ($media!== null) {
        $post_media = "\n<!--" .$type. " " . preg_replace('/\s\s+/', ' ', strip_tags($media)) . " " .$type. "-->";
    } else {
        $post_media = "";
    }
    $post_content = "<!--t " . $post_title . " t-->" . $post_description . $tagmd . $post_media . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';

        if (empty($draft)) {
            if (date('Y-m-d-H-i-s') >= $post_date) { 
                $dir = 'content/' . $user . '/blog/' . $category. '/'.$type. '/';
            } else {
                $dir = 'content/' . $user . '/blog/' . $category. '/'.$type. '/scheduled/';
            }
        } else {
            $dir = 'content/' . $user . '/blog/' . $category. '/draft/';
        }

        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        save_tag_i18n($post_tag, $post_tagmd);

        rebuilt_cache('all');
        clear_post_cache($post_date, $post_tag, $post_url, $dir . $filename, $category, $type);

        if (empty($draft)) {
            if (date('Y-m-d-H-i-s') >= $post_date) {
                $redirect = site_url() . 'admin/mine';
            } else {
                $redirect = site_url() . 'admin/scheduled';
            }
        } else {
            $redirect = site_url() . 'admin/draft';
        }

        header("Location: $redirect");
    }
}

// Edit content
function edit_content($title, $tag, $url, $content, $oldfile, $revertPost, $publishDraft, $category, $type, $destination = null, $description = null, $date = null, $media = null)
{

    $tag = explode(',', preg_replace("/\s*,\s*/", ",", rtrim($tag, ',')));
    $tag = array_filter(array_unique($tag));
    $tagslang = "content/data/tags.lang";
    $newfile = '';
    $views = array();
    $viewsFile = "content/data/views.json";
    if (file_exists($tagslang)) {
        $taglang = array_flip(unserialize(file_get_contents($tagslang)));
        $tflip = array_intersect_key($taglang, array_flip($tag));
        $post_tag = array();
        $post_tagmd = array();
        foreach ($tag as $t) {
            if (array_key_exists($t, $tflip)) {
                foreach ($tflip as $tfp => $tf){
                    if($t == $tfp) {
                        $post_tag[] = $tf;
                        $post_tagmd[] = $tfp;
                    }
                }
            } else {
                $post_tag[] = $t;
                $post_tagmd[] = $t;
            }
        }

        $post_tag = safe_tag(implode(',', $post_tag));
        $post_tagmd = safe_html(implode(',', $post_tagmd));
    } else {
        $post_tag = safe_tag(implode(',', $tag));
        $post_tagmd = safe_html(implode(',', $tag));        
    }

    $dir = explode('/', pathinfo($oldfile, PATHINFO_DIRNAME));
    $olddate = date('Y-m-d-H-i-s', strtotime($date));

    $post_title = safe_html($title);
    $post_tag = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($post_tag)));
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    

    $post_t =  explode(',', $post_tag);
    $pret_t = explode(',', $post_tagmd);
    $tags = tag_cloud(true);
    $timestamp = date('YmdHis');

    $combine = array_combine($pret_t, $post_t);
    $inter = array_intersect_key($tags, array_flip($post_t));
    $newtag = array();

    foreach ($combine as $tag => $v) {
        if (array_key_exists($v, $tags)) {
            foreach ($inter as $in => $i){
                if($v == $in) {
                    if (strtolower($tag) == strtolower(tag_i18n($in))) {
                        $newtag[$v]= $tag;
                    } else {
                        $newtag[$v.'-'. $timestamp]= $tag;
                    }
                }
            }
        } else {
            $newtag[$v] = $tag;
        }            
    }

    $post_tag = implode(',', array_keys($newtag));
    
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    if ($tag !== null) {
        $tagmd = "\n<!--tag " . $post_tagmd . " tag-->";
    } else {
        $tagmd = "";
    }
    if ($media !== null) {
        $post_media = "\n<!--" . $type . " " . preg_replace('/\s\s+/', ' ', strip_tags($media)) . " " . $type . "-->";
    } else {
        $post_media = "";
    }
    $post_content = "<!--t " . $post_title . " t-->" . $post_description . $tagmd . $post_media . "\n\n" . $content;
    
    $dirBlog = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/' . $type . '/';
    $dirDraft = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/draft/';
    $dirScheduled = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/' . $type . '/scheduled/';

    if (!is_dir($dirBlog)) {
        mkdir($dirBlog, 0775, true);
    }

    if (!is_dir($dirDraft)) {
        mkdir($dirDraft, 0775, true);
    }
    
    if (!is_dir($dirScheduled)) {
        mkdir($dirScheduled, 0775, true);
    }    

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        if(!empty($revertPost) || !empty($publishDraft)) {

            if($dir[4] == 'draft') {
                if (date('Y-m-d-H-i-s') >= $olddate) { 
                    $newfile = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                } else {
                    $newfile = $dirScheduled . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                }
            } else {
                $newfile = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
            }

            file_put_contents($newfile, print_r($post_content, true));
            unlink($oldfile);

        } else {

            if ($dir[3] === $category) {            
                
                if($dir[4] == 'draft') {
                    $newfile = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                } else {
                    if (date('Y-m-d-H-i-s') >= $olddate) { 
                        $newfile = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                    } else {
                        $newfile = $dirScheduled . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                    }
                }
                                    
                if ($oldfile === $newfile) {
                    file_put_contents($oldfile, print_r($post_content, true));
                } else {
                    rename($oldfile, $newfile);
                    file_put_contents($newfile, print_r($post_content, true));
                }
            } else {

                if($dir[4] == 'draft') {
                    $newfile = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                } else {
                    if (date('Y-m-d-H-i-s') >= $olddate) { 
                        $newfile = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                    } else {
                        $newfile = $dirScheduled . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                    }
                }

                file_put_contents($newfile, print_r($post_content, true));
                unlink($oldfile);

            }

        }

        if(!empty($publishDraft)) {
            $dt = $olddate;
            $t = str_replace('-', '', $dt);
            $time = new DateTime($t);
            $timestamp = $time->format("Y-m-d");
        } else {
            $fn = explode('_', pathinfo($oldfile, PATHINFO_FILENAME));
            $dt = $fn[0];
            $t = str_replace('-', '', $dt);
            $time = new DateTime($t);
            $timestamp = $time->format("Y-m-d");
        }

        // The post date
        $postdate = strtotime($timestamp);

        // The post URL
        if (config('permalink.type') == 'post') {
            $posturl = site_url() . 'post/' . $post_url;
        } else {
            $posturl = site_url() . date('Y/m', $postdate) . '/' . $post_url;
        }

        save_tag_i18n($post_tag, $post_tagmd);

        rebuilt_cache('all');
        clear_post_cache($dt, $post_tag, $post_url, $oldfile, $category, $type);
        
        if ($oldfile != $newfile) {
            if (file_exists($viewsFile)) {
                $views = json_decode(file_get_contents($viewsFile), true);
                $arr = replace_key($views, $oldfile, $newfile);
                file_put_contents($viewsFile, json_encode($arr, JSON_UNESCAPED_UNICODE));                
            }
        } 

        if ($destination == 'post') {
            if(!empty($revertPost)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                if (date('Y-m-d-H-i-s') >= $olddate) { 
                    header("Location: $posturl");
                } else {
                    $schurl = site_url() . 'admin/scheduled';
                    header("Location: $schurl");
                }
            }
        } else {
            if(!empty($publishDraft)) {
                if (date('Y-m-d-H-i-s') >= $olddate) { 
                    header("Location: $posturl");
                } else {
                    $schurl = site_url() . 'admin/scheduled';
                    header("Location: $schurl");
                }
            } elseif (!empty($revertPost)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                $redirect = site_url() . $destination;
                header("Location: $redirect");
            }
        }
    }
}

// Add static page
function add_page($title, $url, $content, $draft, $description = null)
{

    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    
    $posts = get_static_pages();
    $timestamp = date('YmdHis');
    foreach ($posts as $index => $v) {
        if (strtolower($v['basename']) === strtolower($post_url . '.md')) {
            $post_url = $post_url .'-'. $timestamp;
        } else {
            $post_url = $post_url;
        }
    }
    
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

        $filename = $post_url . '.md';
        $dir = 'content/static/';
        $dirDraft = 'content/static/draft/';

        if (empty($draft)) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            if (!is_dir($dirDraft)) {
                mkdir($dirDraft, 0775, true);
            } 
            file_put_contents($dirDraft . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        
        if (empty($draft)) {
            $redirect = site_url() . 'admin/pages';
            header("Location: $redirect");
        } else {
            $redirect = site_url() . 'admin/draft';
            header("Location: $redirect");            
        }
    }
}

// Add static sub page
function add_sub_page($title, $url, $content, $static, $draft, $description = null)
{

    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }

    $posts = get_static_subpages();
    $timestamp = date('YmdHis');
    foreach ($posts as $index => $v) {
        if (strtolower($v['basename']) === strtolower($post_url . '.md')) {
            $post_url = $post_url .'-'. $timestamp;
        } else {
            $post_url = $post_url;
        }
    }    

    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

        $filename = $post_url . '.md';
        $dir = 'content/static/' . $static . '/';
        $dirDraft = 'content/static/' . $static . '/draft/';

        if (empty($draft)) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            if (!is_dir($dirDraft)) {
                mkdir($dirDraft, 0775, true);
            }
            file_put_contents($dirDraft . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin/pages';
        header("Location: $redirect");
    }
}

// Edit static page and sub page
function edit_page($title, $url, $content, $oldfile, $revertPage, $publishDraft, $destination = null, $description = null, $static = null)
{
    $dir = pathinfo($oldfile, PATHINFO_DIRNAME);
    $views = array();
    $viewsFile = "content/data/views.json";
    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {  
        
        if(!empty($revertPage)) {
            $dirDraft = $dir . '/draft';
            if (!is_dir($dirDraft)) {
                mkdir($dirDraft, 0775, true);
            }
            $newfile = $dirDraft . '/' . $post_url . '.md';
            file_put_contents($newfile, print_r($post_content, true));
            if (empty($static)) {
                $old = pathinfo($oldfile, PATHINFO_FILENAME);
                if(is_dir($dir . '/' . $old)) {
                    rename($dir . '/' . $old, $dir . '/' . $post_url);
                }
            }
            unlink($oldfile);
        } elseif(!empty($publishDraft)) {
            $newfile = dirname($dir) . '/' . $post_url . '.md';
            file_put_contents($newfile, print_r($post_content, true));
            if (empty($static)) {
                $old = pathinfo($oldfile, PATHINFO_FILENAME);
                if(is_dir(dirname($dir) . '/' . $old)) {
                    rename(dirname($dir) . '/' . $old, dirname($dir) . '/' . $post_url);
                }
            }
            unlink($oldfile);
        }else { 
            $newfile = $dir . '/' . $post_url . '.md';
            if ($oldfile === $newfile) {
                file_put_contents($oldfile, print_r($post_content, true));
            } else {
                rename($oldfile, $newfile);
                file_put_contents($newfile, print_r($post_content, true));
                if (empty($static)) {
                    $old = pathinfo($oldfile, PATHINFO_FILENAME);
                    if(is_dir($dir . '/' . $old)) {
                        rename($dir . '/' . $old, $dir . '/' . $post_url);
                    } 
                }
            }
        }

        if (!empty($static)) {
            $posturl = site_url() . $static .'/'. $post_url;
        } else {
            $posturl = site_url() . $post_url;
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
    
        if ($oldfile != $newfile) {
            if (file_exists($viewsFile)) {
                $views = json_decode(file_get_contents($viewsFile), true);
                $arr = replace_key($views, $oldfile, $newfile);
                file_put_contents($viewsFile, json_encode($arr, JSON_UNESCAPED_UNICODE));                
            }
        }         

        if ($destination == 'post') {
            if(!empty($revertPage)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                header("Location: $posturl");
            }
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Add category
function add_category($title, $url, $content, $description = null)
{
    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

        $filename = $post_url . '.md';
        $dir = 'content/data/category/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin/categories';
        header("Location: $redirect");
    }
}

// Edit category
function edit_category($title, $url, $content, $oldfile, $destination = null, $description = null)
{
    $dir = pathinfo($oldfile, PATHINFO_DIRNAME);

    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $description = safe_html($description);
    if ($description !== null) {
        if (!empty($description)) {        
            $post_description = "\n<!--d " . $description . " d-->";
        } else {
            $post_description = "\n<!--d " . get_description($content) . " d-->";
        }            
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

        $newfile = $dir . '/' . $post_url . '.md';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            if (file_exists($oldfile)) {
                rename($oldfile, $newfile);
                file_put_contents($newfile, print_r($post_content, true));
            } else {
                file_put_contents($newfile, print_r($post_content, true));
            }
        }

        rename_category_folder($post_url, $oldfile);

        rebuilt_cache('all');
        if ($destination == 'post') {
            header("Location: $posturl");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Edit user profile
function edit_profile($title, $content, $user)
{
    $user_title = safe_html($title);
    $user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;

    if (!empty($user_title) && !empty($user_content)) {

        $dir = 'content/' . $user . '/';
        $filename = 'content/' . $user . '/author.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($user_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($user_content, true));
        }
        rebuilt_cache('all');
        $redirect = site_url() . 'author/' . $user;
        header("Location: $redirect");
    }
}

// Edit homepage
function edit_frontpage($title, $content)
{
    $front_title = safe_html($title);
    $front_content = '<!--t ' . $front_title . ' t-->' . "\n\n" . $content;

    if (!empty($front_title) && !empty($front_content)) {

        $dir = 'content/data/frontpage';
        $filename = 'content/data/frontpage/frontpage.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($front_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($front_content, true));
        }
        rebuilt_cache('all');
        $redirect = site_url();
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
    $info = pathinfo($file);
    $fn = explode('_', $info['basename']);
    $dr = explode('/', $info['dirname']);
    clear_post_cache($fn[0], $fn[1], str_replace('.md', '', $fn[2]), $file, $dr[3], $dr[4]);

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
        clear_page_cache(pathinfo($file, PATHINFO_BASENAME));
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

// Return draft list
function get_draft($profile, $page, $perpage)
{

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        if (strtolower($profile) === strtolower($str[1]) || $role === 'admin') {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Return draft static page.
function find_draft_page($static = null)
{
    $posts = get_draft_pages();

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v['basename'], $static . '.md') !== false) {

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
        }
    }
    
    return $tmp;
}

// Return draft static subpage.
function find_draft_subpage($static = null, $sub_static = null)
{
    $posts = get_draft_subpages($static);

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v['basename'], $sub_static . '.md') !== false) {
                
                $post = new stdClass;
                
                // The static file
                $url= $v['filename'];
                
                if (is_null($static)) {
                    $parent = str_replace('content/static/', '', dirname($v['dirname'])); 
                    $post->parent = $parent;
                    $post->url = site_url() . $parent . "/" . $url;
                } else {
                    $post->parent = $static;
                    $post->url = site_url() . $static . "/" . $url;
                }
                
                $post->file = $v['dirname'] . '/' . $v['basename'];
                $post->lastMod = strtotime(date('Y-m-d H:i:s', filemtime($post->file)));
                
                $post->md = $url;
                $post->slug = $url;

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
        }
    }

    return $tmp;
}

// Find scheduled post.
function find_scheduled($year, $month, $name)
{
    $posts = get_scheduled_posts();

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

// Return scheduled list
function get_scheduled($profile, $page, $perpage)
{

    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $posts = get_scheduled_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        if (strtolower($profile) === strtolower($str[1]) || $role === 'admin') {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return false;
    }

    return $tmp = get_posts($tmp, $page, $perpage);
}

// Import RSS feed
function migrate($title, $time, $tags, $content, $url, $user, $source)
{
    $post_date = date('Y-m-d-H-i-s', $time);
    $post_title = safe_html($title);
    $pt = safe_tag($tags);
    $post_tag = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($pt)));
    $post_tagmd = preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', ' ', ''), $pt);
    $post_tag = rtrim($post_tag, ',');
    $post_tagmd = rtrim($post_tagmd, ',');
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    if (!empty($source)) {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag ' . $post_tagmd . ' tag-->' . "\n\n" . $content . "\n\n" . 'Source: <a target="_blank" href="' . $source . '">' . $title . '</a>';
    } else {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag ' . $post_tagmd . ' tag-->' .  "\n\n" . $content;
    }
    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
        $dir = 'content/' . $user . '/blog/uncategorized/post/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }
        save_tag_i18n($post_tag, $post_tagmd);
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
                return $str = '<li>' . i18n('Cannot_read_feed_content') . '</li>';
            }
            $time = new DateTime($entry->pubDate);
            $timestamp = $time->format("Y-m-d H:i:s");
            $time = strtotime($timestamp);
            $tags = $entry->category;
            $title = rtrim($entry->title, ' \,\.\-');
            $title = ltrim($title, ' \,\.\-');
            $user = $_SESSION[site_url()]['user'];
            $url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($title)));
            if ($credit == 'yes') {
                $source = $entry->link;
            } else {
                $source = null;
            }
            migrate($title, $time, $tags, $content, $url, $user, $source);
        }
    } else {
        return $str = '<li>' . i18n('Unknown_feed_format') . '</li>';
    }
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

// Return toolbar
function toolbar()
{
    $user = $_SESSION[site_url()]['user'];
    $role = user('role', $user);
    $base = site_url();

    echo <<<EOF
    <link href="{$base}system/resources/css/toolbar.css" rel="stylesheet" />
EOF;
    echo '<div id="toolbar"><ul>';
    echo '<li class="tb-admin"><a href="' . $base . 'admin">' . i18n('Admin') . '</a></li>';
    echo '<li class="tb-addcontent"><a href="' . $base . 'admin/content">' . i18n('Add_content') . '</a></li>';
    if ($role === 'admin') {
        echo '<li class="tb-posts"><a href="' . $base . 'admin/posts">' . i18n('Posts') . '</a></li>';
        if (config('views.counter') == 'true') {
            echo '<li class="tb-popular"><a href="' . $base . 'admin/popular">' . i18n('Popular') . '</a></li>';
        }
    }
    echo '<li class="tb-mine"><a href="' . $base . 'admin/pages">' . i18n('Pages') . '</a></li>';
    echo '<li class="tb-draft"><a href="' . $base . 'admin/scheduled">' . i18n('Scheduled') . '</a></li>';
    echo '<li class="tb-draft"><a href="' . $base . 'admin/draft">' . i18n('Draft') . '</a></li>';
    if ($role === 'admin') {
        echo '<li class="tb-categories"><a href="' . $base . 'admin/categories">' . i18n('Categories') . '</a></li>';
    }
    echo '<li class="tb-import"><a href="' . $base . 'admin/menu">' . i18n('Menu') . '</a></li>';
    if ($role === 'admin') {
      echo '<li class="tb-config"><a href="' . $base . 'admin/config">' . i18n('Config') . '</a></li>';
    }
    echo '<li class="tb-backup"><a href="' . $base . 'admin/backup">' . i18n('Backup') . '</a></li>';
    echo '<li class="tb-update"><a href="' . $base . 'admin/update">' . i18n('Update') . '</a></li>';
    echo '<li class="tb-clearcache"><a href="' . $base . 'admin/clear-cache">' . i18n('Clear_cache') . '</a></li>';
    echo '<li class="tb-editprofile"><a href="' . $base . 'edit/profile">' . i18n('Edit_profile') . '</a></li>';
    echo '<li class="tb-logout"><a href="' . $base . 'logout">' . i18n('Logout') . '</a></li>';

    echo '</ul></div>';
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

function clear_post_cache($post_date, $post_tag, $post_url, $filename, $category, $type)
{
    $b = str_replace('/', '#', site_path() . '/');
    $c = explode(',', $post_tag);
    $t = explode('-', $post_date);

    // Delete post default permalink
    $p = 'cache/page/' . $b . $t[0] . '#' . $t[1] . '#' . $post_url . '.cache';
    if (file_exists($p)) {
        unlink($p);
    }

    // Delete post permalink
    $pp = 'cache/page/' . $b . 'post#' . $post_url . '.cache';
    if (file_exists($pp)) {
        unlink($pp);
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

    // Delete category
    $cc = 'cache/page/' . $b . 'category#' . $category . '.cache';
    if (file_exists($cc)) {
        unlink($cc);
    }
    foreach (glob('cache/page/' . $b . 'category#' . $category . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Delete type
    $tp = 'cache/page/' . $b . 'type#' . $type . '.cache';
    if (file_exists($tp)) {
        unlink($tp);
    }
    foreach (glob('cache/page/' . $b . 'type#' . $type . '~*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
    }

    // Get cache post author
    $arr = pathinfo($filename, PATHINFO_DIRNAME);
    $x = explode('/', $arr);
    // Delete author post list cache
    $a = 'cache/page/' . $b . 'author#' . $x[1] . '.cache';
    if (file_exists($a)) {
        unlink($a);
    }
    foreach (glob('cache/page/' . $b . 'author#' . $x[1] . '~*.cache', GLOB_NOSORT) as $file) {
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

function clear_cache()
{
    foreach (glob('cache/page/*.cache', GLOB_NOSORT) as $file) {
        unlink($file);
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

function replace_key($arr, $oldkey, $newkey) {
    if(array_key_exists($oldkey, $arr)) {
        $keys = array_keys($arr);
        $keys[array_search($oldkey, $keys)] = $newkey;
        return array_combine($keys, $arr);    
    }
    return $arr;    
}

// rename category folder
function rename_category_folder($new_name, $old_file)
{

    $old_name = str_replace('.md', '', basename($old_file));
    $dir = get_category_folder();
    foreach ($dir as $index => $v) {
        if (stripos($v, '/' . $old_name . '/') !== false) {
            $str = explode('/', $v);
            $old_folder = $str[0] . '/' . $str[1] . '/' . $str[2] . '/' . $old_name . '/';
            $new_folder = $str[0] . '/' . $str[1] . '/' . $str[2] . '/' . $new_name . '/';
            rename($old_folder, $new_folder);
        }
    }

}
