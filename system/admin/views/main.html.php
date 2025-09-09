<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Your_recent_posts') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/content"><?php echo i18n('Add_content');?></a>
<br><br>
<?php 

if (isset($_SESSION[site_url()]['user'])) {
    $posts = get_profile_posts($_SESSION[site_url()]['user'], 1, 10);
    if (!empty($posts)) {
        $posts = $posts[0];
        echo '<table class="table post-list">';
        echo '<tr class="head"><th>' . i18n('Title') . '</th><th>' . i18n('Published') . '</th>';
        if (config("views.counter") == "true")
            echo '<th>'.i18n('Views').'</th>';
        echo '<th>' . i18n('Category') . '</th><th>' . i18n('Tags') . '</th><th>' . i18n('Operations') . '</th></tr>';
        foreach ($posts as $p) {
            echo '<tr>';
            echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
            echo '<td>' . format_date($p->date) . '</td>';
            if (config("views.counter") == "true")
                echo '<td>' . $p->views . '</td>';
            echo '<td><a href="' . site_url() . 'admin/categories/' . $p->categorySlug . '">'. $p->categoryTitle .'</a></td>';
            echo '<td>' . str_replace("rel=\"tag\"", "rel=\"tag\" class=\"badge badge-light text-primary font-weight-normal\"", $p->tag) . '</td>';
            echo '<td><a class="btn btn-primary btn-xs" href="' . $p->url . '/edit?destination=admin">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-xs" href="' . $p->url . '/delete?destination=admin">' . i18n('Delete') . '</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}

?>
