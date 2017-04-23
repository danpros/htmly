<?php
echo '<h2>' . i18n('Your_recent_posts') . '</h2>';
get_user_posts();
echo '<h2>' . i18n('Static_pages') . '</h2>';
get_user_pages(); ?>