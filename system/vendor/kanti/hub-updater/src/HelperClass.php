<?php

namespace Kanti;

class HelperClass
{
    protected static function isAbsolutePath($path)
    {
        return ('/' == $path[0] || '\\' == $path[0] || (strlen($path) > 3 && ctype_alpha($path[0]) && $path[1] == ':' &&
                ('\\' == $path[2] || '/' == $path[2])));
    }

    public static function fileExists($file)
    {
        if (is_bool($file) || is_array($file)) {
            throw new \InvalidArgumentException;
        }
        if (strlen($file) >= 3 && static::isAbsolutePath($file)) {
            return file_exists($file);
        }
        return file_exists(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $file);
    }

    public static function isInPhar()
    {
        return substr(__FILE__, 0, 7) === "phar://";
    }
}
