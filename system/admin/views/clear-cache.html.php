<?php

rebuilt_cache('all');

foreach(glob('cache/page/*.cache', GLOB_NOSORT) as $file) {
	unlink($file);
}


echo 'All cache has been deleted!';
 
 ?>