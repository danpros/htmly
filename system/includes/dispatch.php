<?php
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
    error(500, 'dispatch requires at least PHP 5.3 to run.');
}

function _log($message)
{
    if (config('debug.enable') == true && php_sapi_name() !== 'cli') {
        $file = config('debug.log');
        $type = $file ? 3 : 0;
        error_log("{$message}\n", $type, $file);
    }
}

function site_url()
{
    if (config('multi.site') == "true" || config('site.url') == null){
        return rtrim(generateSiteUrl(), '/') . '/';
    } else {
        // Forcing the forward slash
        return rtrim(config('site.url'), '/') . '/';
    }
}

function generateSiteUrl()
{
    $dir = trim(dirname(substr($_SERVER["SCRIPT_FILENAME"], strlen($_SERVER["DOCUMENT_ROOT"]))), '/');
    if ($dir == '.' || $dir == '..') {
        $dir = '';
    }
    $port = '';
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $port = ':' . $_SERVER["SERVER_PORT"];
    }
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    if ($dir === '') {
        return $siteUrl = $scheme . '://' . trim($_SERVER['SERVER_NAME'], "/") . $port . "/";
    }
    return $siteUrl = $scheme . '://' . trim($_SERVER['SERVER_NAME'], "/") . $port . "/" . $dir . '/';
}

function site_path()
{
    static $_path;

    if (!$_path)
        $_path = rtrim(parse_url(site_url(), PHP_URL_PATH), '/');

    return $_path;
}

function theme_path()
{
    if (config('views.root') == null)
        error(500, '[views.root] is not set');

    return site_url() . rtrim(config('views.root'), '/') . '/';
}

function error($code, $message)
{
    @header("HTTP/1.0 {$code} {$message}", true, $code);
    die($message);
}

// Set the language
function get_language()
{
    $langID = config('language');
    $langFile = 'lang/'. $langID . '.ini';

    // Settings for the language
    if (file_exists($langFile)) {
        i18n('source', $langFile);
        setlocale(LC_ALL, $langID . '.utf8');
    } else {
        i18n('source', 'lang/en_US.ini'); // Load the English language file
        setlocale(LC_ALL, 'en_US.utf8'); // Change locale to English
    }
}

// i18n provides strings in the current language
function i18n($key, $value = null)
{
    static $_i18n = array();
    $key = strtolower($key);

    if ($key === 'source') {
      if (file_exists($value))
        $_i18n = parse_ini_file($value, true);
      else
        $_i18n = parse_ini_file('lang/en_US.ini', true);
      $theme_path = parse_url(theme_path(), PHP_URL_PATH);
      $theme_lang_file = $_SERVER['DOCUMENT_ROOT'] . $theme_path . 'lang/en_US.ini';
      $_i18n_local = array();
      if (file_exists($theme_lang_file)) {
        $_i18n_local = parse_ini_file($theme_lang_file, true);
      }
      $_i18n = array_replace($_i18n, $_i18n_local);
    } elseif ($value == null)
        return (isset($_i18n[$key]) ? $_i18n[$key] : $key);
    else
        $_i18n[$key] = $value;
}

function config($key, $value = null)
{
    static $_config = array();

    if ($key === 'source' && file_exists($value))
        $_config = parse_ini_file($value, true);
    elseif ($value == null)
        return (isset($_config[$key]) ? $_config[$key] : null);
    else
        $_config[$key] = $value;
}

function save_config($data = array(), $new = array())
{
    global $config_file;

    $string = file_get_contents($config_file) . "\n";

    foreach ($data as $word => $value) {
        $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $map = array('\r\n' => ' \n ', '\r' => ' \n ');
        $value = trim(strtr($value, $map));
        $string = preg_replace("/^" . $word . " = .+$/m", $word . ' = ' . $value, $string);
    }
    $string = rtrim($string);
    foreach ($new as $word => $value) {
        $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $map = array('\r\n' => ' \n ', '\r' => ' \n ');
        $value = trim(strtr($value, $map));
        $string .= "\n" . $word . ' = ' . $value . "\n";
    }
    $string = rtrim($string);
    return file_put_contents($config_file, $string, LOCK_EX);
}

// Set the theme settings
function theme_settings()
{
    $views_root = config('views.root');
    $settings = '';
    if (!empty($views_root)) {
        $exp = explode('/', $views_root);
        $settings = 'config/themes/' . $exp[1] . '.ini';
    }

    if (file_exists($settings)) {
        theme_config('source', $settings);
    }
}

function theme_config($key, $value = null)
{
    static $_config = array();

    if ($key === 'source' && file_exists($value))
        $_config = parse_ini_file($value, true);
    elseif ($value == null)
        return (isset($_config[$key]) ? $_config[$key] : null);
    else
        $_config[$key] = $value;
}

function save_theme_config($data = array(), $new = array(), $theme = null)
{
    $dir = 'config/themes/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $string = '';
    $config_file = $dir . $theme . '.ini';
    if (file_exists($config_file)) {
        $string = file_get_contents($config_file) . "\n";
    }

    foreach ($data as $word => $value) {
        $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $map = array('\r\n' => ' \n ', '\r' => ' \n ');
        $value = trim(strtr($value, $map));
        $string = preg_replace("/^" . $word . " = .+$/m", $word . ' = ' . $value, $string);
    }
    $string = rtrim($string);
    foreach ($new as $word => $value) {
        $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $map = array('\r\n' => ' \n ', '\r' => ' \n ');
        $value = trim(strtr($value, $map));
        $string .= "\n" . $word . ' = ' . $value . "\n";
    }
    $string = rtrim($string);
    return file_put_contents($config_file, $string, LOCK_EX);
}

function get_search_query()
{
    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . trim($search);
        header("Location: $url");
        die;
    }
}

function to_b64($str)
{
    $str = base64_encode($str);
    $str = preg_replace('/\//', '_', $str);
    $str = preg_replace('/\+/', '.', $str);
    $str = preg_replace('/\=/', '-', $str);
    return trim($str, '-');
}

function from_b64($str)
{
    $str = preg_replace('/\_/', '/', $str);
    $str = preg_replace('/\./', '+', $str);
    $str = preg_replace('/\-/', '=', $str);
    $str = base64_decode($str);
    return $str;
}

if (extension_loaded('mcrypt')) {

    function encrypt($decoded, $algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        if (($secret = config('cookies.secret')) == null)
            error(500, '[cookies.secret] is not set');

        $secret = mb_substr($secret, 0, mcrypt_get_key_size($algo, $mode));
        $iv_size = mcrypt_get_iv_size($algo, $mode);
        $iv_code = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
        $encrypted = to_b64(mcrypt_encrypt($algo, $secret, $decoded, $mode, $iv_code));

        return sprintf('%s|%s', $encrypted, to_b64($iv_code));
    }

    function decrypt($encoded, $algo = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
    {
        if (($secret = config('cookies.secret')) == null)
            error(500, '[cookies.secret] is not set');

        $secret = mb_substr($secret, 0, mcrypt_get_key_size($algo, $mode));
        list($enc_str, $iv_code) = explode('|', $encoded);
        $enc_str = from_b64($enc_str);
        $iv_code = from_b64($iv_code);
        $enc_str = mcrypt_decrypt($algo, $secret, $enc_str, $mode, $iv_code);

        return rtrim($enc_str, "\0");
    }

}

function set_cookie($name, $value, $expire = 31536000, $path = '/')
{
    $value = (function_exists('encrypt') ? encrypt($value) : $value);
    setcookie($name, $value, time() + $expire, $path);
}

function get_cookie($name)
{
    $value = from($_COOKIE, $name);

    if ($value)
        $value = (function_exists('decrypt') ? decrypt($value) : $value);

    return $value;
}

function delete_cookie()
{
    $cookies = func_get_args();
    foreach ($cookies as $ck)
        setcookie($ck, '', -10, '/');
}

// if we have APCu loaded, enable cache functions
if (extension_loaded('apcu')) {

    function cache($key, $func, $ttl = 0)
    {
        if (($data = apcu_fetch($key)) === false) {
            $data = call_user_func($func);
            if ($data !== null) {
                apcu_store($key, $data, $ttl);
            }
        }
        return $data;
    }

    function cache_invalidate()
    {
        foreach (func_get_args() as $key) {
            apcu_delete($key);
        }
    }

}

function warn($name = null, $message = null)
{
    static $warnings = array();

    if ($name == '*')
        return $warnings;

    if (!$name)
        return count(array_keys($warnings));

    if (!$message)
        return isset($warnings[$name]) ? $warnings[$name] : null;

    $warnings[$name] = $message;
}

function _u($str)
{
    return urlencode($str);
}

function _h($str, $enc = 'UTF-8', $flags = ENT_QUOTES)
{
    return htmlentities($str, $flags, $enc);
}

function from($source, $name)
{
    $map = array("\r\n" => "\n", "\r" => "\n");
    if (is_array($name)) {
        $data = array();
        foreach ($name as $k)
            $data[$k] = isset($source[$k]) ? trim(strtr($source[$k], $map)) : null;
        return $data;
    }
    return isset($source[$name]) ? trim(strtr($source[$name], $map)) : null;
}

function stash($name, $value = null)
{
    static $_stash = array();

    if ($value === null)
        return isset($_stash[$name]) ? $_stash[$name] : null;

    $_stash[$name] = $value;

    return $value;
}

function method($verb = null)
{
    if ($verb == null || (strtoupper($verb) == strtoupper($_SERVER['REQUEST_METHOD'])))
        return strtoupper($_SERVER['REQUEST_METHOD']);

    error(400, 'bad request');
}

function client_ip()
{
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];

    return $_SERVER['REMOTE_ADDR'];
}

function redirect(/* $code_or_path, $path_or_cond, $cond */)
{
    $argv = func_get_args();
    $argc = count($argv);

    $path = null;
    $code = 302;
    $cond = true;

    switch ($argc) {
        case 3:
            list($code, $path, $cond) = $argv;
            break;
        case 2:
            if (is_string($argv[0]) ? $argv[0] : $argv[1]) {
                $code = 302;
                $path = $argv[0];
                $cond = $argv[1];
            } else {
                $code = $argv[0];
                $path = $argv[1];
            }
            break;
        case 1:
            if (!is_string($argv[0]))
                error(500, 'bad call to redirect()');
            $path = $argv[0];
            break;
        default:
            error(500, 'bad call to redirect()');
    }

    $cond = (is_callable($cond) ? !!call_user_func($cond) : !!$cond);

    if (!$cond)
        return;

    header('Location: ' . $path, true, $code);
    exit;
}

function partial($view, $locals = null)
{
    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
    }

    if (($view_root = config('views.root')) == null)
        error(500, "[views.root] is not set");

    $path = basename($view);
    $view = preg_replace('/' . $path . '$/', "_{$path}", $view);
    $view = "{$view_root}/{$view}.html.php";

    if (file_exists($view)) {
        ob_start();
        require $view;
        return ob_get_clean();
    } else {
        error(500, "partial [{$view}] not found");
    }

    return '';
}

function content($value = null)
{
    return stash('$content$', $value);
}

function render($view, $locals = null, $layout = null)
{
    if (!login()) {
        $c = str_replace('/', '#', str_replace('?', '~', rawurldecode($_SERVER['REQUEST_URI'])));
        $dir = 'cache/page';
        $cachefile = $dir . '/' . $c . '.cache';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
    }

    if (($view_root = config('views.root')) == null)
        error(500, "[views.root] is not set");

    $fnc = "{$view_root}/functions.php";

    ob_start();
    if (file_exists($fnc)) {
        include $fnc;
    }
    include "{$view_root}/{$view}.html.php";
    content(trim(ob_get_clean()));

    if ($layout !== false) {
        if ($layout == null) {
            $layout = config('views.layout');
            $layout = ($layout == null) ? 'layout' : $layout;
        }
        $layout = "{$view_root}/{$layout}.html.php";
        if ($view != 'search-reindex') {
            header('Content-type: text/html; charset=utf-8');
        }
        if (config('generation.time') == 'true') {
            ob_start();
            require $layout;
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $total_time = round($time, 4);
            echo "\n" . '<!-- Dynamic page generated in '.$total_time.' seconds. -->';
        } else {
            ob_start();
            require $layout;
        }
        if (!login() && $view != '404' && $view != '404-search' && $view != 'login-mfa' && config('cache.off') == "false") {
            if (config('cache.timestamp') == 'true') {
                echo "\n" . '<!-- Cached page generated on '.date('Y-m-d H:i:s').' -->';
            }
            if (isset($cachefile))
                file_put_contents($cachefile, ob_get_contents(), LOCK_EX);
        }
        echo trim(ob_get_clean());
    } else {
        echo content();
    }
}

function json($obj, $code = 200)
{
    header('Content-type: application/json', true, $code);
    echo json_encode($obj);
    exit;
}

function save_json_pretty($filename, $arr)
{
    if (defined("JSON_PRETTY_PRINT")) {    
        file_put_contents($filename, json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    } else {
        file_put_contents($filename, json_encode($arr, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}

function file_get_data($filename) 
{
    $thisFile = fopen($filename, 'r');
    if (flock($thisFile, LOCK_SH)) {
        $fileData = file_get_contents($filename);
        flock($thisFile, LOCK_UN);
    } else {
        $fileData = json_encode(array('flock_fail' => 'reading'));
    }
    fclose($thisFile);
    return $fileData;
}

function condition()
{
    static $cb_map = array();

    $argv = func_get_args();
    $argc = count($argv);

    if (!$argc)
        error(500, 'bad call to condition()');

    $name = array_shift($argv);
    $argc = $argc - 1;

    if (!$argc && is_callable($cb_map[$name]))
        return call_user_func($cb_map[$name]);

    if (is_callable($argv[0]))
        return ($cb_map[$name] = $argv[0]);

    if (is_callable($cb_map[$name]))
        return call_user_func_array($cb_map[$name], $argv);

    error(500, 'condition [' . $name . '] is undefined');
}

function middleware($cb_or_path = null)
{
    static $cb_map = array();

    if ($cb_or_path == null || is_string($cb_or_path)) {
        foreach ($cb_map as $cb) {
            call_user_func($cb, $cb_or_path);
        }
    } else {
        array_push($cb_map, $cb_or_path);
    }
}

function filter($sym, $cb_or_val = null)
{
    static $cb_map = array();

    if (is_callable($cb_or_val)) {
        $cb_map[$sym] = $cb_or_val;
        return;
    }

    if (is_array($sym) && count($sym) > 0) {
        foreach ($sym as $s) {
            $s = substr($s, 1);
            if (isset($cb_map[$s]) && isset($cb_or_val[$s]))
                call_user_func($cb_map[$s], $cb_or_val[$s]);
        }
        return;
    }

    error(500, 'bad call to filter()');
}

function route_to_regex($route)
{
    $route = preg_replace_callback('@:[\w]+@i', function ($matches) {
        $token = str_replace(':', '', $matches[0]);
        return '(?P<' . $token . '>[a-z0-9_\0-\.]+)';
    }, $route);
    return '@^' . rtrim($route, '/') . '$@i';
}

function route($method, $pattern, $callback = null)
{
    // callback map by request type
    static $route_map = array(
        'GET' => array(),
        'POST' => array()
    );

    $method = strtoupper($method);

    if (!in_array($method, array('GET', 'POST')))
        error(500, 'Only GET and POST are supported');

    // a callback was passed, so we create a route definition
    if ($callback !== null) {

        // create a route entry for this pattern
        $route_map[$method][$pattern] = array(
            'xp' => route_to_regex($pattern),
            'cb' => $callback
        );

    } else {


        // callback is null, so this is a route invocation. look up the callback.
        foreach ($route_map[$method] as $pat => $obj) {

            // if the requested uri ($pat) has a matching route, let's invoke the cb
            if (!preg_match($obj['xp'], $pattern, $vals))
                continue;

            // call middleware
            middleware($pattern);

            // construct the params for the callback
            array_shift($vals);
            preg_match_all('@:([\w]+)@', $pat, $keys, PREG_PATTERN_ORDER);
            $keys = array_shift($keys);
            $argv = array();

            foreach ($keys as $index => $id) {
                $id = substr($id, 1);
                if (isset($vals[$id])) {
                    array_push($argv, trim(urldecode($vals[$id])));
                }
            }

            // call filters if we have symbols
            if (count($keys)) {
                filter(array_values($keys), $vals);
            }

            // if cb found, invoke it
            if (is_callable($obj['cb'])) {
                call_user_func_array($obj['cb'], $argv);
            }

            // leave after first match
            break;

        }
    }

}

function get($path, $cb)
{
    route('GET', $path, $cb);
}

function post($path, $cb)
{
    route('POST', $path, $cb);
}

function flash($key, $msg = null, $now = false)
{
    static $x = array(),
    $f = null;

    $f = (config('cookies.flash') ? config('cookies.flash') : '_F');

    if ($c = get_cookie($f))
        $c = json_decode($c, true);
    else
        $c = array();

    if ($msg == null) {

        if (isset($c[$key])) {
            $x[$key] = $c[$key];
            unset($c[$key]);
            set_cookie($f, json_encode($c));
        }

        return (isset($x[$key]) ? $x[$key] : null);
    }

    if (!$now) {
        $c[$key] = $msg;
        set_cookie($f, json_encode($c));
    }

    $x[$key] = $msg;
}

function create_thumb($src, $desired_width = null, $desired_height = null) {
    
    if (!extension_loaded('gd')) {
        return $src;
    }

    $dir = 'content/images/thumbnails';

    if (!is_dir($dir)) {
        mkdir($dir);
    }
    
    $w = config('thumbnail.width');
    if (empty($w)) {
        $w = 500;
    }
    
    if (is_null($desired_width)) {
        $desired_width = $w;
    }
    
    if (!is_null($desired_height)) {
        $h = 'x' . $desired_height;
    } else {
        $h = null;
    }

    $path = pathinfo($src, PATHINFO_FILENAME);
    $fileName = rawurldecode($path);
    $thumbFile = $dir . '/' . $fileName  . '-' . $desired_width . $h .'.webp';

    if (file_exists($thumbFile)) {
        return site_url() . $thumbFile;
    } else {

        /* read the source image */
        $source_image = imagecreatefromstring(file_get_contents($src));
        if ($source_image === false) {
            return $src;
        }
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        if (is_null($desired_height)) {
            $desired_height = floor($height * ($desired_width / $width));
        }
        
        $ratio = max($desired_width/$width, $desired_height/$height);
        $height = floor($desired_height / $ratio);
        $x = floor(($width - $desired_width / $ratio) / 2);
        $width = floor($desired_width / $ratio);

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
        imageAlphaBlending($virtual_image, false);
        imageSaveAlpha($virtual_image, true);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, $x, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagewebp($virtual_image, $thumbFile, 75);
        imagedestroy($virtual_image);
        
        return site_url() . $thumbFile;

    }
}

function dispatch()
{
    $path = $_SERVER['REQUEST_URI'];

    if (config('site.url') !== null)
        $path = preg_replace('@^' . preg_quote(site_path()) . '@', '', $path);

    $parts = preg_split('/\?/', $path, -1, PREG_SPLIT_NO_EMPTY);

    $uri = trim($parts[0], '/');
    $uri = strlen($uri) ? $uri : 'index';

    route(method(), "/{$uri}");
}
