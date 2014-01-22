<?php

// Change this to your timezone
date_default_timezone_set('Asia/Jakarta');

use \Michelf\MarkdownExtra;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

// Get blog post path. Unsorted. Mostly used on widget.
function get_post_unsorted(){

	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the posts

		$_cache = glob('content/*/blog/*.md', GLOB_NOSORT);
	}

	return $_cache;
}

// Get blog post with more info about the path. Sorted by filename.
function get_post_sorted(){

	static $tmp= array();
	
	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the posts

		$tmp = glob('content/*/blog/*.md', GLOB_NOSORT);
		
		foreach($tmp as $file) {
			$_cache[] = pathinfo($file);
		}
		
	}
	
	usort($_cache, "sortfile");
	
	return $_cache;
}

// Get static page path. Unsorted. 
function get_static_pages(){

	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the
		// static page.

		$_cache = glob('content/static/*.md', GLOB_NOSORT);
	}

	return $_cache;
}

// Get author bio path. Unsorted. 
function get_author_names(){

	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the
		// author.

		$_cache = glob('content/*/author.md', GLOB_NOSORT);
	}

	return $_cache;
}

// usort function. Sort by filename.
function sortfile($a, $b) {
	return $a['filename'] == $b['filename'] ? 0 : ( $a['filename'] < $b['filename'] ) ? 1 : -1;
}

// usort function. Sort by date.
function sortdate($a, $b) {
	return $a->date == $b->date ? 0 : ( $a->date < $b->date ) ? 1 : -1;
}

// Return blog posts. 
function get_posts($posts, $page = 1, $perpage = 0){
		
	if(empty($posts)) {
		$posts = get_post_sorted();
	}
	
	$tmp = array();
	
	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);
	
	foreach($posts as $index => $v){

		$post = new stdClass;
		
		$filepath = $v['dirname'] . '/' . $v['basename'];

		// Extract the date
		$arr = explode('_', $filepath);
		
		// Replaced string
		$replaced = substr($arr[0], 0,strrpos($arr[0], '/')) . '/';
		
		// Author string
		$str = explode('/', $replaced);
		$author = $str[count($str)-3];
		
		// The post author + author url
		$post->author = $author;
		$post->authorurl = site_url() . 'author/' .  $author;
		
		// The post date
		$post->date = strtotime(str_replace($replaced,'',$arr[0]));
		
		// The archive per day
		$post->archive = site_url(). 'archive/' . date('Y-m-d', $post->date) ;

		// The post URL
		$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
		
		$tag = array();
		$url = array();
		$bc = array();
		
		$t = explode(',', $arr[1]);
		foreach($t as $tt) {
			$tag[] = array($tt, site_url(). 'tag/' . $tt);
		}
		
		foreach($tag as $a) {
			$url[] = '<span><a href="' .  $a[1] . '">'. $a[0] .'</a></span>';
			$bc[] = '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' .  $a[1] . '">'. $a[0] .'</a></span>';
		}
		
		$post->tag = implode(', ', $url);
		
		$post->tagb = implode(' » ', $bc);

		// Get the contents and convert it to HTML
		$content = MarkdownExtra::defaultTransform(file_get_contents($filepath));

		// Extract the title and body
		$arr = explode('</h1>', $content);
		$post->title = str_replace('<h1>','',$arr[0]);
		$post->body = $arr[1];

		$tmp[] = $post;
	}

	return $tmp;
}

// Find post by year, month and name, previous, and next.
function find_post($year, $month, $name){

	$posts = get_post_sorted();
	
	foreach ($posts as $index => $v) {
		$url = $v['basename'];
		if( strpos($url, "$year-$month") !== false && strpos($url, $name.'.md') !== false){
		
			// Use the get_posts method to return
			// a properly parsed object

			$ar = get_posts($posts, $index+1,1);
			$nx = get_posts($posts, $index,1);
			$pr = get_posts($posts, $index+2,1);
			
			if ($index == 0) {
				if(isset($pr[0])) {
					return array(
						'current'=> $ar[0],
						'prev'=> $pr[0]
					);
				}
				else {
					return array(
						'current'=> $ar[0],
						'prev'=> null
					);
				}
			}
			elseif (count($posts) == $index+1) {
				return array(
					'current'=> $ar[0],
					'next'=> $nx[0]
				);
			}
			else {
				return array(
					'current'=> $ar[0],
					'next'=> $nx[0],
					'prev'=> $pr[0]
				);
			}
		
		}
	}
}

// Return tag page.
function get_tag($tag, $page, $perpage){

	$posts = get_post_sorted();
	
	$tmp = array();
	
	foreach ($posts as $index => $v) {
		$url = $v['filename'];
		$str = explode('_', $url);
		$mtag = explode(',', $str[1]);
		$etag = explode(',', $tag);
		foreach ($mtag as $t) {
			foreach ($etag as $e) {
				if($t === $e){
					$tmp[] = $v;
				}
			}
		}
	}
	
	if(empty($tmp)) {
		not_found();
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
	
}

// Return archive page.
function get_archive($req, $page, $perpage){

	$posts = get_post_sorted();
	
	$tmp = array();
	
	foreach ($posts as $index => $v) {
		$url = $v['filename'];
		$str = explode('_', $url);
		if( strpos($str[0], "$req") !== false ){
			$tmp[] = $v;
		}
	}
	
	if(empty($tmp)) {
		not_found();
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
	
}

// Return posts list on profile.
function get_profile($profile, $page, $perpage){

	$posts = get_post_sorted();
	
	$tmp = array();
	
	foreach ($posts as $index => $v) {
		$url = $v['dirname'];
		$str = explode('/', $url);
		$author = $str[count($str)-2];
		if($profile === $author){
			$tmp[] = $v;
		}
	}
	
	if(empty($tmp)) {
		not_found();
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
	
}

// Return author bio.
function get_bio($author){

	$names = get_author_names();

	$tmp = array();

	foreach($names as $index => $v){
		$post = new stdClass;
		
		// Replaced string
		$replaced = substr($v, 0,strrpos($v, '/')) . '/';
		
		// Author string
		$str = explode('/', $replaced);
		$profile = $str[count($str)-2];
		
		if($author === $profile){
			// Profile URL
			$url = str_replace($replaced,'',$v);
			$post->url = site_url() . 'author/' . $profile;
			
			// Get the contents and convert it to HTML
			$content = MarkdownExtra::defaultTransform(file_get_contents($v));

			// Extract the title and body
			$arr = explode('</h1>', $content);
			$post->title = str_replace('<h1>','',$arr[0]);
			$post->body = $arr[1];

			$tmp[] = $post;
		}
	}
	
	return $tmp;
}

function default_profile($author) {

	$tmp = array();
	$profile = new stdClass;
	
	$profile->title = $author;
	$profile->body = '<p>Just another HTMLy user.</p>';
	
	return $tmp[] = $profile;
	
}

// Return static page.
function get_static_post($static){

	$posts = get_static_pages();

	$tmp = array();

	foreach($posts as $index => $v){
		if(strpos($v, $static.'.md') !== false){
		
			$post = new stdClass;
			
			// Replaced string
			$replaced = substr($v, 0, strrpos($v, '/')) . '/';
			
			// The static page URL
			$url = str_replace($replaced,'',$v);
			$post->url = site_url() . str_replace('.md','',$url);
			
			// Get the contents and convert it to HTML
			$content = MarkdownExtra::defaultTransform(file_get_contents($v));

			// Extract the title and body
			$arr = explode('</h1>', $content);
			$post->title = str_replace('<h1>','',$arr[0]);
			$post->body = $arr[1];

			$tmp[] = $post;
			
		}
	}
	
	return $tmp;
}

// Return search page.
function get_keyword($keyword){

	$posts = get_post_unsorted();
	$tmp = array();
	
	$words = explode(' ', $keyword);
	
	foreach($posts as $index => $v){
	
		$content = MarkdownExtra::defaultTransform(file_get_contents($v));
		
		foreach ($words as $word) {
			if(strpos(strtolower(strip_tags($content)), strtolower($word)) !== false){
			
				$post = new stdClass;

				// Extract the date
				$arr = explode('_', $v);
				
				// Replaced string
				$replaced = substr($arr[0], 0,strrpos($arr[0], '/')) . '/';
				
				// Author string
				$str = explode('/', $replaced);
				$author = $str[count($str)-3];
				
				// The post author + author url
				$post->author = $author;
				$post->authorurl = site_url() . 'author/' .  $author;
				
				// The post date
				$post->date = strtotime(str_replace($replaced,'',$arr[0]));

				// The post URL
				$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);
				
				$tag = array();
				$url = array();
				$bc = array();
				
				$t = explode(',', $arr[1]);
				foreach($t as $tt) {
					$tag[] = array($tt, site_url(). 'tag/' . $tt);
				}
				
				foreach($tag as $a) {
					$url[] = '<span><a href="' .  $a[1] . '">'. $a[0] .'</a></span>';
					$bc[] = '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' .  $a[1] . '">'. $a[0] .'</a></span>';
				}
				
				$post->tag = implode(', ', $url);
				
				$post->tagb = implode(' » ', $bc);

				// Extract the title and body
				$arr = explode('</h1>', $content);
				$post->title = str_replace('<h1>','',$arr[0]);
				$post->body = $arr[1];
				$tmp[] = $post;
			
			}
		}
	}
	
	$tmp = array_unique($tmp, SORT_REGULAR);

	usort($tmp,'sortdate');
	
	return $tmp;
}

// Get related posts base on post tag.
function get_related($tag) {
	$perpage = config('related.count');
	$posts = get_tag(strip_tags($tag), 1, $perpage+1);
	$tmp = array();
	$req = $_SERVER['REQUEST_URI'];
	
	foreach ($posts as $post) {
		$url = $post->url;
		if( strpos($url, $req) === false){
			$tmp[] = $post;
		}
	}
	
	$total = count($tmp);
	
	if($total >= 1) {
	
		shuffle($tmp);
		
		$i = 1;
		echo '<div class="related"><h4>Related posts</h4><ul>';
		foreach ($tmp as $post) {
			echo '<li><a href="' . $post->url . '">' . $post->title . '</a></li>';
			if ($i++ >= $perpage) break;
		}
		echo '</ul></div>';
	}
	
}

// Return post count. Matching $var and $str provided.
function get_count($var, $str) {

	$posts = get_post_sorted();
	
	$tmp = array();
	
	foreach ($posts as $index => $v) {
		$url = $v[$str];
		if( strpos($url, "$var") !== false){
			$tmp[] = $v;
		}
	}
	
	return count($tmp);
	
}

// Return an archive list, categorized by year and month.
function archive_list() {

	$posts = get_post_unsorted();
	$by_year = array();
	$col = array();
	
	foreach($posts as $index => $v){
	
		$arr = explode('_', $v);
		
		// Replaced string
		$str = $arr[0];
		$replaced = substr($str, 0,strrpos($str, '/')) . '/';
		
		$date = str_replace($replaced,'',$arr[0]);
		$data = explode('-', $date);
		$col[] = $data;
		
	}
	
	foreach ($col as $row){
	
		$y = $row['0'];
		$m = $row['1'];
		$by_year[$y][] = $m;

	}
	
	# Most recent year first
	krsort($by_year);
	# Iterate for display
	echo '<h3>Archive</h3>';
	foreach ($by_year as $year => $months){
	
		echo '<span class="year"><a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a></span> ';
		echo '(' . count($months) . ')';
		echo '<ul class="month">';

		# Sort the months
		krsort($months);
		$by_month = array_count_values($months);
		foreach ($by_month as $month => $count){
			$name = date('F', mktime(0,0,0,$month,1,2010));
			echo '<li class="item"><a href="' . site_url() .  'archive/' . $year . '-' . $month . '">' . $name .  '</a>';
			echo ' <span class="count">(' . $count . ')</span></li>';
		}

		echo '</ul>';
		
	}

}

// Return tag cloud.
function tag_cloud() {

	$posts = get_post_unsorted();
	$tags = array();
	
	foreach($posts as $index => $v){
	
		$arr = explode('_', $v);
		
		$data = $arr[1];
		$mtag = explode(',', $data);
		foreach($mtag as $etag) {
			$tags[] = $etag;
		}
		
	}
	
	$tag_collection = array_count_values($tags);
	ksort($tag_collection);
	
	echo '<h3>Tags</h3>';
	echo '<ul class="taglist">';
	foreach ($tag_collection as $tag => $count){
		echo '<li class="item"><a href="' . site_url() . 'tag/' . $tag . '">' . $tag . '</a> <span class="count">(' . $count . ')</span></li>';
	}
	echo '</ul>';
	
}

// Helper function to determine whether
// to show the previous buttons
function has_prev($prev){
	if(!empty($prev)) {
		return array(
			'url'=> $prev->url,
			'title'=> $prev->title
		);
	}
}

// Helper function to determine whether
// to show the next buttons
function has_next($next){
	if(!empty($next)) {
		return array(
			'url'=> $next->url,
			'title'=> $next->title
		);
	}
}

// Helper function to determine whether
// to show the pagination buttons
function has_pagination($total, $perpage, $page = 1){
	if(!$total) {
		$total = count(get_post_unsorted());
	}
	return array(
		'prev'=> $page > 1,
		'next'=> $total > $page*$perpage
	);
}

// Get the meta description
function get_description($text) {
	
	$string = explode('</p>', $text);
	$string = preg_replace('/[^A-Za-z0-9 !@#$%^&*(),.-]/u', ' ', strip_tags($string[0] . '</p>'));
	$string = ltrim($string);
	
	if (strlen($string) > 1) {
		return $string;
	}
	else {
		$string = preg_replace('/[^A-Za-z0-9 !@#$%^&*(),.-]/u', ' ', strip_tags($text));
		$string = ltrim($string);
		if (strlen($string) < config('description.char')) {
			return $string;
		}
		else {
			return $string = substr($string, 0, strpos($string, ' ', config('description.char')));
		}
	}

}

// Get the teaser
function get_teaser($text, $url) {
	
	if (strlen(strip_tags($text)) < config('teaser.char')) {
		$string = preg_replace('/\s\s+/', ' ', strip_tags($text));
		$body = $string . '...' . ' <a class="readmore" href="' . $url . '#more">more</a>' ;
		echo '<p>' . $body . '</p>';
	}
	else {
		$string = preg_replace('/\s\s+/', ' ', strip_tags($text));
		$string = substr($string, 0, strpos($string, ' ', config('teaser.char')));
		$body = $string . '...' . ' <a class="readmore" href="' . $url . '#more">more</a>' ;
		echo '<p>' . $body . '</p>';
	}

}

// Get thumbnail from image and Youtube.
function get_thumbnail($text) {

	$default = config('default.thumbnail');
	$dom = new DOMDocument();
	$dom->loadHtml($text);
	$imgTags = $dom->getElementsByTagName('img');
	$vidTags = $dom->getElementsByTagName('iframe');
	if ($imgTags->length > 0) {
		$imgElement = $imgTags->item(0);
		$imgSource = $imgElement->getAttribute('src');
		return '<div class="thumbnail" style="background-image:url(' . $imgSource . ');"></div>';
	}
	elseif ($vidTags->length > 0) {
		$vidElement = $vidTags->item(0);
		$vidSource = $vidElement->getAttribute('src');
		$fetch = explode("embed/", $vidSource);
		if(isset($fetch[1])) {
			$vidThumb = '//img.youtube.com/vi/' . $fetch[1] . '/default.jpg';
			return '<div class="thumbnail" style="background-image:url(' . $vidThumb . ');"></div>';
		}
	}
	else {
		if (!empty($default)) {
			return '<div class="thumbnail" style="background-image:url(' . $default . ');"></div>';
		}
	}
	
}

// Use base64 encode image to speed up page load time.
function base64_encode_image($filename=string,$filetype=string) {
	if ($filename) {
		$imgbinary = fread(fopen($filename, "r"), filesize($filename));
		return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
	}
}

// Social links
function social(){

	$twitter = config('social.twitter'); 
	$facebook = config('social.facebook'); 
	$google = config('social.google'); 
	$tumblr = config('social.tumblr');
	$rss = site_url() . 'feed/rss';
	
	if (!empty($twitter)) {
		echo '<a href="' . $twitter . '" target="_blank"><img src="' . site_url() . 'themes/default/img/twitter.png" width="32" height="32" alt="Twitter"/></a>';
	}
	
	if (!empty($facebook)) {
		echo '<a href="' . $facebook . '" target="_blank"><img src="' . site_url() . 'themes/default/img/facebook.png" width="32" height="32" alt="Facebook"/></a>';
	}
	
	if (!empty($google)) {
		echo '<a href="' . $google . '" target="_blank"><img src="' . site_url() . 'themes/default/img/googleplus.png" width="32" height="32" alt="Google+"/></a>';
	}
	
	if (!empty($tumblr)) {
		echo '<a href="' . $tumblr . '" target="_blank"><img src="' . site_url() . 'themes/default/img/tumblr.png" width="32" height="32" alt="Tumblr"/></a>';
	}
	
	echo '<a href="' . site_url() . 'feed/rss" target="_blank"><img src="' . site_url() . 'themes/default/img/rss.png" width="32" height="32" alt="RSS Feed"/></a>';
	
}

// Copyright
function copyright(){

	$blogcp = config('blog.copyright');
	$credit = 'Proudly powered by <a href="http://www.htmly.com" target="_blank">HTMLy</a>.';
	
	if (!empty($blogcp)) {
		return $copyright = '<p>' . $blogcp .  '</p><p>' . $credit . '</p>';
	}
	else {
		return $credit = '<p>' . $credit . '</p>';
	}
	
}

// Disqus on post.
function disqus($title, $url){
	$disqus = config('disqus.shortname');
	$script = <<<EOF
	<script type="text/javascript">
		var disqus_shortname = '{$disqus}';
		var disqus_title = '{$title}';
		var disqus_url = '{$url}';
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
EOF;
	if (!empty($disqus)) {
		return $script;
	}
}

// Disqus comment count on teaser
function disqus_count(){
	$disqus = config('disqus.shortname');
	$script = <<<EOF
	<script type="text/javascript">
		var disqus_shortname = '{$disqus}';
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
EOF;
	if (!empty($disqus)) {
		return $script;
	}
}

// Google Publisher (Google+ page).
function publisher(){
	$publisher = config('google.publisher');
	if (!empty($publisher)) {
		return $publisher;
	}
}

// Google Analytics
function analytics(){
	$analytics = config('google.analytics.id');
	$script = <<<EOF
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '{$analytics}']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
EOF;
	if (!empty($analytics)) {
		return $script;
	}
}

// Menu
function menu(){
	$menu = config('blog.menu');
	if (!empty($menu)) {
		return $menu;
	}
	else {
		get_menu();
	}
}

// Auto generate menu from static page
function get_menu() {

	$posts = get_static_pages();
	krsort($posts);
	
	echo '<ul>';
	echo '<li><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
	foreach($posts as $index => $v){
	
		// Replaced string
		$replaced = substr($v, 0, strrpos($v, '/')) . '/';
			
		// The static page URL
		$title = str_replace($replaced,'',$v);
		$url = site_url() . str_replace('.md','',$title);
		echo '<li><a href="' . $url . '">' . ucfirst(str_replace('.md','',$title)) . '</a></li>';
			
	}
	echo '</ul>';
	
}

// Search form
function search() {
	echo <<<EOF
	<form id="search-form" method="get">
		<input type="text" class="search-input" name="search" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}">
		<input type="submit" value="Search" class="search-button">
	</form>
EOF;
	if(isset($_GET['search'])) {
		$url = site_url() . 'search/' . $_GET['search']; 
		header ("Location: $url");
	}
}

// The not found error
function not_found(){
	error(404, render('404', null, false));
}

// Turn an array of posts into an RSS feed
function generate_rss($posts){
	
	$feed = new Feed();
	$channel = new Channel();
	
	$channel
		->title(config('blog.title'))
		->description(config('blog.description'))
		->url(site_url())
		->appendTo($feed);

	foreach($posts as $p){
		$item = new Item();
		$item
			->title($p->title)
			->pubDate($p->date)
			->description($p->body)
			->url($p->url)
			->category($p->tag, $p->tagurl)
			->appendTo($channel);
	}
	
	echo $feed;
}

// Return post, archive url. 
function get_path(){
		
	$posts = get_post_sorted();
	
	$tmp = array();
	
	foreach($posts as $index => $v){

		$post = new stdClass;
		
		$filepath = $v['dirname'] . '/' . $v['basename'];

		// Extract the date
		$arr = explode('_', $filepath);
		
		// Replaced string
		$replaced = substr($arr[0], 0,strrpos($arr[0], '/')) . '/';
		
		// Author string
		$str = explode('/', $replaced);
		$author = $str[count($str)-3];
		
		$post->authorurl = site_url() . 'author/' .  $author;
		
		// The post date
		$post->date = strtotime(str_replace($replaced,'',$arr[0]));
		
		// The archive per day
		$post->archiveday = site_url(). 'archive/' . date('Y-m-d', $post->date) ;
		
		// The archive per day
		$post->archivemonth = site_url(). 'archive/' . date('Y-m', $post->date) ;
		
		// The archive per day
		$post->archiveyear = site_url(). 'archive/' . date('Y', $post->date) ;

		// The post URL
		$post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md','',$arr[2]);

		$tmp[] = $post;
	}

	return $tmp;
}

// Return static page path.
function get_static_path(){

	$posts = get_static_pages();

	$tmp = array();

	foreach($posts as $index => $v){
		
		$post = new stdClass;
		
		// Replaced string
		$replaced = substr($v, 0, strrpos($v, '/')) . '/';
		
		// The static page URL
		$url = str_replace($replaced,'',$v);
		$post->url = site_url() . str_replace('.md','',$url);

		$tmp[] = $post;
			
	}
	
	return $tmp;
}

// Generate sitemap.xml.
function generate_sitemap($str){
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	if ($str == 'index') {
	
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		echo '<sitemap><loc>' . site_url() . 'sitemap.base.xml</loc></sitemap>';
		echo '<sitemap><loc>' . site_url() . 'sitemap.post.xml</loc></sitemap>';
		echo '<sitemap><loc>' . site_url() . 'sitemap.static.xml</loc></sitemap>';
		echo '<sitemap><loc>' . site_url() . 'sitemap.tag.xml</loc></sitemap>';
		echo '<sitemap><loc>' . site_url() . 'sitemap.archive.xml</loc></sitemap>';
		echo '<sitemap><loc>' . site_url() . 'sitemap.author.xml</loc></sitemap>';		
		echo '</sitemapindex>';
		
	}
	elseif ($str == 'base') {
	
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		echo '<url><loc>' . site_url() . '</loc><changefreq>hourly</changefreq><priority>1.0</priority></url>';
		echo '</urlset>';
		
	}
	elseif ($str == 'post') {
	
		$posts = get_path();
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($posts as $p) {
			echo '<url><loc>' . $p->url . '</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'static') {
	
		$posts = get_static_path();
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($posts as $p) {
			echo '<url><loc>' . $p->url . '</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'tag') {
	
		$posts = get_post_unsorted();
		$tags = array();
		
		foreach($posts as $index => $v){
		
			$arr = explode('_', $v);
			
			$data = $arr[1];
			$mtag = explode(',', $data);
			foreach($mtag as $etag) {
				$tags[] = $etag;
			}
			
		}
		
		foreach($tags as $t) {
			$tag[] = site_url() . 'tag/' . $t;
		}
		
		$tag = array_unique($tag, SORT_REGULAR);
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($tag as $t) {
			echo '<url><loc>' . $t . '</loc><changefreq>weekly</changefreq><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'archive') {
	
		$posts = get_path();
		$day = array();
		$month = array();
		$year = array();
	
		foreach($posts as $p) {
			$day[] = $p->archiveday;
			$month[] = $p->archivemonth;
			$year[] = $p->archiveyear;
			
		}
	
		$day = array_unique($day, SORT_REGULAR);
		$month = array_unique($month, SORT_REGULAR);
		$year = array_unique($year, SORT_REGULAR);
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($day as $d) {
			echo '<url><loc>' . $d . '</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';
		}
		
		foreach($month as $m) {
			echo '<url><loc>' . $m . '</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';
		}
		
		foreach($year as $y) {
			echo '<url><loc>' . $y . '</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'author') {
	
		$posts = get_path();
		$author = array();
		
		foreach($posts as $p) {
			$author[] = $p->authorurl;
		}
		
		$author = array_unique($author, SORT_REGULAR);
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($author as $a) {
			echo '<url><loc>' . $a . '</loc><changefreq>daily</changefreq><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	
}

// Function to generate OPML file
function generate_opml(){
	
	$opml_data = array(
		'head' => array(
			'title' => config('blog.title') . ' OPML File',
			'ownerName' => config('blog.title'),
			'ownerId' => config('site.url')
			),
		'body' => array(
			array(
				'text' => config('blog.title'),
				'description' => config('blog.description'),
				'htmlUrl' => config('site.url'),
				'language' => 'unknown',
				'title' => config('blog.title'),
				'type' => 'rss',
				'version' => 'RSS2',
				'xmlUrl' => config('site.url') . '/feed/rss'
				)
			)
		);

	$opml = new OPML($opml_data);
	echo $opml->render();
}

// Turn an array of posts into a JSON
function generate_json($posts){
	return json_encode($posts);
}

function welcome_page() {
	echo <<<EOF
	<div style="font-size:20px;text-align:center;padding:50px 20px;">
		<h1>Welcome to your new HTMLy-powered blog.</h1>
		<p>The next thing you will need to do is creating the first account. Please create <strong><em>YourUsername.ini</em></strong> inside <strong><em>admin/users</em></strong> folder and write down your password there:</p>
		<pre><code>password = YourPassword</code></pre>
		<p>Login to your blog admin panel at <strong><em>www.example.com/admin</em></strong> to creating your first post.</p>
		<p>This welcome message will disappear after your first post published.</p>
	</div>
EOF;
}