<?php

namespace Kanti;
#[\AllowDynamicProperties]
class CacheOneFile
{
    protected $fileName = "";
    protected $holdTime = 43200; //12h

    public function __construct($fileName, $holdTime = 43200)
    {
        $this->fileName = $fileName;
        $this->holdTime = $holdTime;
    }

    public function has()
    {
        if (!file_exists($this->fileName)) {
            return false;
        }
        clearstatcache();

        if (filemtime($this->fileName) < (time() - $this->holdTime)) {
            unlink($this->fileName);

            return false;
        }

        return true;
    }

    protected function fileForceContents()
    {
        $args = func_get_args();
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $args[0]);
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($parts);
        $directory = '';
        foreach ($parts as $part) {
            $checkPath = $directory . $part;
            if (is_dir($checkPath . DIRECTORY_SEPARATOR) === false) {
                mkdir($checkPath, 0755);
            }
            $directory = $checkPath . DIRECTORY_SEPARATOR;
        }
        call_user_func_array('file_put_contents', $args);
    }

    public function get()
    {
        return file_get_contents($this->fileName);
    }

    public function set($content)
    {
        $this->fileForceContents($this->fileName, $content);
    }
}
