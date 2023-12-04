<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Static_pages') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>add/page"><?php echo i18n('Add_new_page');?></a>
<br><br>
<?php 

if (isset($_SESSION[config("site.url")]['user'])) {
	$posts = get_static_post(null);
	if (!empty($posts)) {
		krsort($posts);
		echo '<table class="table post-list">';
		echo '<tr class="head"><th>' . i18n('Title') . '</th>';
		if (config("views.counter") == "true")
			echo '<th>'.i18n('Views').'</th>';
		echo '<th>' . i18n('Operations') . '</th></tr>';
		$i = 0;
		$len = count($posts);
		foreach ($posts as $p) {
			if ($i == 0) {
				$class = 'item first';
			} elseif ($i == $len - 1) {
				$class = 'item last';
			} else {
				$class = 'item';
			}
			$i++;

			echo '<tr class="' . $class . '">';
			echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
			if (config("views.counter") == "true")
				echo '<td>' . $p->views . '</td>';
			echo '<td><a class="btn btn-primary btn-xs" href="' . $p->url . '/add?destination=admin/pages">' . i18n('Add_sub') . '</a> <a class="btn btn-primary btn-xs" href="' . $p->url . '/edit?destination=admin/pages">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-xs" href="' . $p->url . '/delete?destination=admin/pages">' . i18n('Delete') . '</a></td>';
			echo '</tr>';

			$shortUrl = substr($p->url, strrpos($p->url, "/") + 1);
			$subPages = get_static_sub_post($shortUrl, null);

			foreach ($subPages as $sp) {
				echo '<tr class="' . $class . '">';
				echo '<td> <span style="margin-left:30px;">&raquo; <a target="_blank" href="' . $sp->url . '">' . $sp->title . '</a></span></td>';
				if (config("views.counter") == "true")
					echo '<td>' . $sp->views . '</td>';
				echo '<td><a class="btn btn-primary btn-xs" href="' . $sp->url . '/edit?destination=admin/pages">' . i18n('Edit') . '</a> <a class="btn btn-danger btn-xs" href="' . $sp->url . '/delete?destination=admin/pages">' . i18n('Delete') . '</a></td>';
				echo '</tr>';
			}
		}
		echo '</table>';
	}
}

 ?>