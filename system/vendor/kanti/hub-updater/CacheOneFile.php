<?php

namespace Kanti;

class CacheOneFile
{
    protected $fileName = "";
    protected $holdTime = 43200; //12h

    public function __construct($fileName, $holdTime = 43200)
    {
        $this->fileName = $fileName;
        $this->holdTime = $holdTime;
    }

    public function is()
    {
        if (! HelperClass::fileExists($this->fileName)) {
            return false;
        }
        if (filemtime($this->fileName) < ( time() - $this->holdTime )) {
            unlink($this->fileName);

            return false;
        }

        return true;
    }

    public function get()
    {
        return file_get_contents($this->fileName);
    }

    public function set($content)
    {
        file_put_contents($this->fileName, $content);
    }
}
