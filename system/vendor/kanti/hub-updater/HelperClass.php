<?php

namespace Kanti;

class HelperClass{
	static public function fileExists($file){
		return file_exists(dirname($_SERVER["SCRIPT_FILENAME"]) . "/" . $file);
	}
	static public function isInPhar() {
		return substr(__FILE__,0,7) === "phar://";
	}
}