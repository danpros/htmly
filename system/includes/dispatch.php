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
    if (config('site.url') == null)
        error(500, '[site.url] is not set');

    // Forcing the forward slash
    return rtrim(config('site.url'), '/') . '/';
}

function site_path()
{
    static $_path;

    if (config('site.url') == null)
        error(500, '[site.url] is not set');

    if (!$_path)
        $_path = rtrim(parse_url(config('site.url'), PHP_URL_PATH), '/');

    return $_path;
}

function error($code, $message)
{
    @header("HTTP/1.0 {$code} {$message}", true, $code);
    die($message);
}

// i18n provides strings in the current language
function i18n($key, $value = null)
{
    static $_i18n = array();

    if ($key === 'source') {
      if (file_exists($value))
        $_i18n = parse_ini_file($value, true);
      else
        $_i18n = parse_ini_file('lang/en_US.ini', true);
    } elseif ($value == null)
        return (isset($_i18n[$key]) ? $_i18n[$key] : '_i18n_' . $key . '_i18n_');
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
        $value = str_replace('"', '\"', $value);
        $string = preg_replace("/^" . $word . " = .+$/m", $word . ' = "' . $value . '"', $string);
    }
    $string = rtrim($string);
    foreach ($new as $word => $value) {
        $value = str_replace('"', '\"', $value);
        $string .= "\n" . $word . ' = "' . $value . '"' . "\n";
    }
    $string = rtrim($string);
    return file_put_contents($config_file, $string);
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

// Move folder and files
function copy_folders($oldfolder, $newfolder)
{
    if (is_dir($oldfolder))
    {
        $dir = opendir($oldfolder);
        if (!is_dir($newfolder))
        {
            mkdir($newfolder, 0775, true);
        }
        while (($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($oldfolder . '/' . $file))
                {
                    copy_folders($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
                else
                {
                    copy($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}

// Delete folder and files
function remove_folders($dir)
{
    if (false === file_exists($dir)) {
        return false;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            if (false === rmdir($fileinfo->getRealPath())) {
                return false;
            }
        } else {
            if (false === unlink($fileinfo->getRealPath())) {
                return false;
            }
        }
    }

    return rmdir($dir);
}

// Based on <https://github.com/mecha-cms/extend.minify>
// HTML Minifier
function minify_html($input) {
    if(trim($input) === "") return $input;
    // Remove extra white-space(s) between HTML attribute(s)
    $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
        return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
    }, str_replace("\r", "", $input));
    // Minify inline CSS declaration(s)
    if(strpos($input, ' style=') !== false) {
        $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
            return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
        }, $input);
    }
    if(strpos($input, '</style>') !== false) {
      $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function($matches) {
        return '<style' . $matches[1] .'>'. minify_css($matches[2]) . '</style>';
      }, $input);
    }
    if(strpos($input, '</script>') !== false) {
      $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function($matches) {
        return '<script' . $matches[1] .'>'. minify_js($matches[2]) . '</script>';
      }, $input);
    }

    return preg_replace(
        array(
            // t = text
            // o = tag open
            // c = tag close
            // Keep important white-space(s) after self-closing HTML tag(s)
            '#<(img|input)(>| .*?>)#s',
            // Remove a line break and two or more white-space(s) between tag(s)
            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
            '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
            '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
            // Remove HTML comment(s) except IE comment(s)
            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
        ),
        array(
            '<$1$2</$1>',
            '$1$2$3',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            '$1',
            ""
        ),
    $input);
}

// CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
function minify_css($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
            // Remove unused white-space(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
            '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
            // Replace `:0 0 0 0` with `:0`
            '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
            // Replace `background-position:0` with `background-position:0 0`
            '#(background-position):0(?=[;\}])#si',
            // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
            '#(?<=[\s:,\-])0+\.(\d+)#s',
            // Minify string value
            '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
            '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
            // Minify HEX color code
            '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
            // Replace `(border|outline):none` with `(border|outline):0`
            '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
            // Remove empty selector(s)
            '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
        ),
        array(
            '$1',
            '$1$2$3$4$5$6$7',
            '$1',
            ':0',
            '$1:0 0',
            '.$1',
            '$1$3',
            '$1$2$4$5',
            '$1$2$3',
            '$1:0',
            '$1$2'
        ),
    $input);
}

// JavaScript Minifier
function minify_js($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
    $input);
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

// if we have APC loaded, enable cache functions
if (extension_loaded('apc')) {

    function cache($key, $func, $ttl = 0)
    {
        if (($data = apc_fetch($key)) === false) {
            $data = call_user_func($func);
            if ($data !== null) {
                apc_store($key, $data, $ttl);
            }
        }
        return $data;
    }

    function cache_invalidate()
    {
        foreach (func_get_args() as $key) {
            apc_delete($key);
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
    if (is_array($name)) {
        $data = array();
        foreach ($name as $k)
            $data[$k] = isset($source[$k]) ? $source[$k] : null;
        return $data;
    }
    return isset($source[$name]) ? $source[$name] : null;
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
    $login = login();
    if (!$login) {
        $c = str_replace('/', '#', str_replace('?', '~', $_SERVER['REQUEST_URI']));
        $dir = 'cache/page';
        $cachefile = $dir . '/' . $c . '.cache';
        if (is_dir($dir) === false) {
            mkdir($dir, 0775, true);
        }
    }

    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
    }

    if (($view_root = config('views.root')) == null)
        error(500, "[views.root] is not set");

    ob_start();
    include "{$view_root}/{$view}.html.php";
    content(trim(ob_get_clean()));

    if ($layout !== false) {
        if ($layout == null) {
            $layout = config('views.layout');
            $layout = ($layout == null) ? 'layout' : $layout;
        }
        $layout = "{$view_root}/{$layout}.html.php";
        header('Content-type: text/html; charset=utf-8');
        if (config('generation.time') == 'true') {
            ob_start();
            require $layout;
            static $total_time; // Fix minify
            $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            $total_time = round($time, 4);
            echo "\n" . '<!-- Dynamic page generated in '.$total_time.' seconds. -->';
        } else {
            ob_start();
            require $layout;
        }
        if (!$login && $view != '404') {
            if (!file_exists($cachefile)) {
            	if (config('cache.timestamp') == 'true') {
                    echo "\n" . '<!-- Cached page generated on '.date('Y-m-d H:i:s').' -->';
                }
                if(config('cache.minify') == 'true') {
                	$content = minify_html(ob_get_contents());
                    if (config('generation.time') == 'true') {
                    	$content .= "\n" . '<!-- Dynamic page generated in '.$total_time.' seconds. -->';
                    }
                    if (config('cache.timestamp') == 'true') {
                    	$content .= "\n" . '<!-- Cached page generated on '.date('Y-m-d H:i:s').' -->';
                    }
                	file_put_contents($cachefile, $content);
                } else {
                	file_put_contents($cachefile, ob_get_contents());
                }
            }
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

    // a callback was passed, so we create a route defiition
    if ($callback !== null) {

        // create a route entry for this pattern
        $route_map[$method][$pattern] = array(
            'xp' => route_to_regex($pattern),
            'cb' => $callback
        );

    } else {


        // callback is null, so this is a route invokation. look up the callback.
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
