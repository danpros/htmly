<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

foreach (glob('cache/index/*.txt', GLOB_NOSORT) as $file) {
    unlink($file);
}

rebuilt_cache('all');

foreach (glob('cache/page/*.cache', GLOB_NOSORT) as $file) {
    unlink($file);
}

echo i18n('All_cache_has_been_deleted');

?>