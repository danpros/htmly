<?php
if (!defined('HTMLY')) die('HTMLy');

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

// Add author 
function add_author($title, $user, $password, $content)
{
    create_user($user, $password);

    $user_title = safe_html($title);
    $user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;

    if (!empty($user_title) && !empty($user_content)) {

        $user_content = stripslashes($user_content);

        $dir = 'content/' . $user . '/';
        $filename = 'content/' . $user . '/author.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($user_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($user_content, true));
        }
        rebuilt_cache('all');
        $redirect = site_url() . 'admin/authors';
        header("Location: $redirect");
    }
}

// Edit author
function edit_author($name, $title, $user, $password, $content)
{
    $name = get_author_info($name);
    $name = $name[0];

    // Jika edit tanpa ganti password
    if(empty($password)) {
        $file = 'config/users/' . $user . '.ini';
        if (!file_exists($file))
        {
            // Hanya akan dieksekusi ketika tidak melakukan penggantian password namun melakukan penggantian username
            file_put_contents($file, "password = " . $name->password . "\n" .
                "encryption = password_hash\n" .
                "role = " . $name->role . "\n");
        }
    } else {
        // jika melakukan pergantian password
        create_user($user, $password, $name->role);
    }

    $user_title = safe_html($title);
    $user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;

    if (!empty($user_title) && !empty($user_content)) {
        
        $user_content = stripslashes($user_content);

        $dir = 'content/' . $user . '/';
        $filename = 'content/' . $user . '/author.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($user_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($user_content, true));
        }

        // Jika username lama tidak sama dengan yang baru maka file username lama akan dihapus
        if($name->username !== $user) {
            // copying all content and file dari username lama ke username baru
            copy_folders('content/' . $name->username, 'content/' . $user);
            remove_folders('content/' . $name->username);
            // Jika username sesi sama dengan username lama
            if($_SESSION[config("site.url")]['user'] === $name->username) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION[config("site.url")]['user'] = $user;
            }
            unlink($name->file);
        }

        rebuilt_cache('all');
        $redirect = site_url() . 'admin/authors';
        header("Location: $redirect");
    }
}

// Check old password
function valid_password($user, $pass)
{
    $user_enc = user('encryption', $user);
    $user_pass = user('password', $user);
    $user_role = user('role', $user);

    if ($user_enc == "password_hash") {
        if (password_verify($pass, $user_pass)) {
            if (password_needs_rehash($user_pass, PASSWORD_DEFAULT)) {
                update_user($user, $pass, $user_role);
            }
            return true;
        } else {
            return false;
        }
    } else if (old_password_verify($pass, $user_enc, $user_pass)) {
        update_user($user, $pass, $user_role);
        return true;
    } else {
        return false;
    }
}

// Check username exists
function username_exists($username, $user = null)
{
    // Jika username baru tidak sama dengan username lama
    if($username !== $user || $user === null) {
        $file = 'config/users/' . $username . '.ini';
        if(file_exists($file))
        {
            return true;
        } else {
            return false;
        }
    } else { // Jika username baru sama dengan username lama
        $file = 'config/users/' . $username . '.ini';
        if(!file_exists($file))
        {
            return true;
        } else {
            return false;
        }
    }
}

// Matching password and password confirm
function password_match($password, $confirm)
{
    if($password === $confirm)
    {
        return true;
    } else {
        return false;
    }
}

// Create a session
function session($user, $pass)
{
    $user_file = 'config/users/' . $user . '.ini';
    if (!file_exists($user_file)) {
        return $str = '<div class="error-message"><ul><li class="alert alert-danger">ERROR: Invalid username or password.</li></li></div>';
    }

    if(valid_password($user, $pass))
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION[config("site.url")]['user'] = $user;
        header('location: admin');
    } else {
        return $str = '<div class="error-message"><ul><li class="alert alert-danger">ERROR: Invalid username or password.</li></li></div>';
    }

}

function old_password_verify($pass, $user_enc, $user_pass)
{
    $password = (strlen($user_enc) > 0 && $user_enc !== 'clear' && $user_enc !== 'none') ? hash($user_enc, $pass) : $pass;
    return ($password === $user_pass);
}

// Clean URLs
function remove_accent($str)
{
    return URLify::downcode($str);
}

// Add content
function add_content($title, $tag, $url, $content, $user, $description = null, $media = null, $draft, $category, $type)
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
                    if($t === $tfp) {
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

    $post_date = date('Y-m-d-H-i-s');
    $post_title = safe_html($title);
    $post_media = preg_replace('/\s\s+/', ' ', strip_tags($media));
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
                if($v === $in) {
                    if (strtolower($tag) === strtolower(tag_i18n($in))) {
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
    
    $posts = get_post_sorted();
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
        $post_media = "\n<!--" .$type. " " . $post_media . " " .$type. "-->";
    } else {
        $post_media = "";
    }
    $post_content = "<!--t " . $post_title . " t-->" . $post_description . $tagmd . $post_media . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        $post_content = stripslashes($post_content);

        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';

        if (empty($draft)) {
            $dir = 'content/' . $user . '/blog/' . $category. '/'.$type. '/';
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
            $redirect = site_url() . 'admin/mine';
        } else {
            $redirect = site_url() . 'admin/draft';
        }

        header("Location: $redirect");
    }
}

// Edit content
function edit_content($title, $tag, $url, $content, $oldfile, $destination = null, $description = null, $date = null, $media = null, $revertPost, $publishDraft, $category, $type)
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
                    if($t === $tfp) {
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
    
    $oldurl = explode('_', $oldfile);
    $dir = explode('/', $oldurl[0]);
    $olddate = date('Y-m-d-H-i-s', strtotime($date));

    if ($date !== null) {
        $oldurl[0] = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/' . $olddate;
    }

    $post_title = safe_html($title);
    $post_media = preg_replace('/\s\s+/', ' ', strip_tags($media));
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
                if($v === $in) {
                    if (strtolower($tag) === strtolower(tag_i18n($in))) {
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
        $post_media = "\n<!--" . $type . " " . $post_media. " " . $type . "-->";
    } else {
        $post_media = "";
    }
    $post_content = "<!--t " . $post_title . " t-->" . $post_description . $tagmd . $post_media . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        $post_content = stripslashes($post_content);

        if(!empty($revertPost) || !empty($publishDraft)) {

            $dirBlog = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/' . $type . '/';
            $dirDraft = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/draft/';

            if($dir[4] == 'draft') {
                $filename = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
            } else {
                $filename = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
            }

            if (is_dir($dirBlog)) {
            } else {
                mkdir($dirBlog, 0775, true);
            }

            if (is_dir($dirDraft)) {
            } else {
                mkdir($dirDraft, 0775, true);
            }

            file_put_contents($filename, print_r($post_content, true));
            unlink($oldfile);
            $newfile = $olddate . '_' . $post_tag . '_' . $post_url . '.md';

        } else {

            if ($dir[3] === $category) {
                $newfile = $oldurl[0] . '_' . $post_tag . '_' . $post_url . '.md';
                if ($oldfile === $newfile) {
                    file_put_contents($oldfile, print_r($post_content, true));
                } else {
                    rename($oldfile, $newfile);
                    file_put_contents($newfile, print_r($post_content, true));
                }
            } else {

                $dirBlog = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/' . $type. '/';
                $dirDraft = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $category . '/draft/';

                if($dir[4] == 'draft') {
                    $filename = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                } else {
                    $filename = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
                }

                if (is_dir($dirBlog)) {
                } else {
                    mkdir($dirBlog, 0775, true);
                }

                if (is_dir($dirDraft)) {
                } else {
                    mkdir($dirDraft, 0775, true);
                }

                file_put_contents($filename, print_r($post_content, true));
                unlink($oldfile);
                $newfile = $olddate . '_' . $post_tag . '_' . $post_url . '.md';

            }

        }

        if(!empty($publishDraft)) {
            $dt = $olddate;
            $t = str_replace('-', '', $dt);
            $time = new DateTime($t);
            $timestamp = $time->format("Y-m-d");
        } else {
            $replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';
            $dt = str_replace($replaced, '', $oldurl[0]);
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
        clear_post_cache($dt, $post_tag, $post_url, $newfile, $category, $type);
        if ($destination == 'post') {
            if(!empty($revertPost)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                header("Location: $posturl");
            }
        } else {
            if(!empty($publishDraft)) {
                header("Location: $posturl");
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
function add_page($title, $url, $content, $description = null)
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
		$arr = explode('/', $v);
        if (strtolower($arr[2]) === strtolower($post_url . '.md')) {
            $post_url = $post_url .'-'. $timestamp;
        } else {
            $post_url = $post_url;
        }
    }
	
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

        $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/static/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin/pages';
        header("Location: $redirect");
    }
}

// Add static sub page
function add_sub_page($title, $url, $content, $static, $description = null)
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

        $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/static/' . $static . '/';
        if (is_dir($dir)) {
            file_put_contents($dir . $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . $filename, print_r($post_content, true));
        }

        rebuilt_cache('all');
        clear_page_cache($post_url);
        $redirect = site_url() . 'admin/pages';
        header("Location: $redirect");
    }
}

// Edit static page and sub page
function edit_page($title, $url, $content, $oldfile, $destination = null, $description = null, $static = null)
{
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));

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

        $post_content = stripslashes($post_content);

        $newfile = $dir . '/' . $post_url . '.md';
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
            if (empty($static)) {
                $path = pathinfo($oldfile);
                $old = substr($path['filename'], strrpos($path['filename'], '/'));
                if(is_dir($dir . '/' . $old)) {
                    rename($dir . '/' . $old, $dir . '/' . $post_url);
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
        if ($destination == 'post') {
            header("Location: $posturl");
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

        $post_content = stripslashes($post_content);

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
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));

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

        $post_content = stripslashes($post_content);

        $newfile = $dir . '/' . $post_url . '.md';
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
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

        $user_content = stripslashes($user_content);

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

        $front_content = stripslashes($front_content);

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

// Delete author
function delete_author($file, $destination)
{
    if (!login())
        return null;
    $deleted_content = $file;

    if (!empty($deleted_content)) {

        $str = explode('/', $file);
        $str = str_replace('.ini', '', $str);
        $username = $str[2];

        $dir = 'content/' . $username . '/';

        $user = $_SESSION[config("site.url")]['user'];
        // Melarang untuk menghapus diri sendiri, karena bunuh diri itu dosa :D
        if($user !== $username) {
            remove_folders($dir);
            unlink($deleted_content);
            rebuilt_cache('all');
        }
        if ($destination == 'author') {
            $redirect = site_url();
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
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
    $str = explode('/', $replaced);
    $dt = str_replace($replaced, '', $arr[0]);
    clear_post_cache($dt, $arr[1], str_replace('.md', '', $arr[2]), $file, $str[count($str) - 3], $str[count($str) - 2]);

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
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag' . $post_tagmd . 'tag-->' . "\n\n" . $content . "\n\n" . 'Source: <a target="_blank" href="' . $source . '">' . $title . '</a>';
    } else {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag' . $post_tagmd . 'tag-->' .  "\n\n" . $content;
    }
    if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

        $post_content = stripslashes($post_content);

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
                return $str = '<li>Can not read the feed content.</li>';
            }
            $time = new DateTime($entry->pubDate);
            $timestamp = $time->format("Y-m-d H:i:s");
            $time = strtotime($timestamp);
            $tags = $entry->category;
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
function get_user_posts()
{

    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = get_profile_posts($_SESSION[config("site.url")]['user'], 1, 5);
        if (!empty($posts)) {
            echo '<table id="htmly-table" class="table post-list" style="width:100%">';
            echo '<thead><tr class="head"><th>' . i18n('Title') . '</th><th>' . i18n('Published') . '</th>';
            if (config("views.counter") == "true")
                echo '<th>'.i18n('Views').'</th>';
            echo '<th>' . i18n('Category') . '</th><th>' . i18n('Tags') . '</th><th>' . i18n('Operations') . '</th></tr></thead>';
            echo '<tbody>';
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
                echo '<td>' . format_date($p->date) . '</td>';
                if (config("views.counter") == "true")
                    echo '<td>' . $p->views . '</td>';
                echo '<td><a href="' . str_replace('category', 'admin/categories', $p->categoryUrl) . '">'. strip_tags($p->category) .'</a></td>';
                echo '<td>' . $p->tag . '</td>';
                echo '<td><a class="btn btn-primary btn-sm" href="' . $p->url . '/edit?destination=admin">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-sm" href="' . $p->url . '/delete?destination=admin">' . i18n('Delete') . '</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
    }
}

// Get all static pages
function get_user_pages()
{
    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = get_static_post(null);
        if (!empty($posts)) {
            krsort($posts);
            echo '<table id="htmly-table" class="table post-list" style="width:100%">';
            echo '<thead><tr class="head"><th>' . i18n('Title') . '</th>';
            if (config("views.counter") == "true")
                echo '<th>'.i18n('Views').'</th>';
            echo '<th>' . i18n('Operations') . '</th></tr></thead>';
            echo '<tbody>';
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
                echo '<td><a class="btn btn-primary btn-sm" href="' . $p->url . '/add?destination=admin/pages">' . i18n('Add_sub') . '</a> <a class="btn btn-primary btn-sm" href="' . $p->url . '/edit?destination=admin/pages">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-sm" href="' . $p->url . '/delete?destination=admin/pages">' . i18n('Delete') . '</a></td>';
                echo '</tr>';

                $shortUrl = substr($p->url, strrpos($p->url, "/") + 1);
                $subPages = get_static_sub_post($shortUrl, null);

                foreach ($subPages as $sp) {
                    echo '<tr class="' . $class . '">';
                    echo '<td> <span style="margin-left:30px;">&raquo; <a target="_blank" href="' . $sp->url . '">' . $sp->title . '</a></span></td>';
                    if (config("views.counter") == "true")
                        echo '<td>' . $sp->views . '</td>';
                    echo '<td><a class="btn btn-primary btn-sm" href="' . $sp->url . '/edit?destination=admin/pages">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-sm" href="' . $sp->url . '/delete?destination=admin/pages">' . i18n('Delete') . '</a></td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
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
            echo '<table id="htmly-table" class="table backup-list" style="width:100%">';
            echo '<tr class="head"><th>' . i18n('Filename') . '</th><th>'.i18n('Date').'</th><th>' . i18n('Operations') . '</th></tr>';
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
                echo '<td><a class="btn btn-primary btn-sm" target="_blank" href="' . $url . '">Download</a> <form method="GET"><input type="hidden" name="file" value="' . $name . '"/><input type="submit" class="btn btn-danger btn-sm" name="submit" value="Delete"/></form></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo i18n('No_available_backup');
        }
    }
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
    $arr = explode('_', $filename);
    $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';
    $str = explode('/', $replaced);
    $author = $str[count($str) - 5];
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
