<?php

// Change this to your timezone
date_default_timezone_set('Asia/Jakarta');

use \Michelf\MarkdownExtra;
use \Suin\RSSWriter\Feed;
use \Suin\RSSWriter\Channel;
use \Suin\RSSWriter\Item;

// Get blog post path. Unsorted. Mostly used on widget.
function get_post_unsorted(){

	static $_unsorted = array();
	
	if(empty($_unsorted)){
	
		$url = 'cache/index/index-unsorted.txt';
		if (file_exists($url)) {
			$_unsorted = unserialize(file_get_contents($url));
		}
		else {
			rebuilt_cache('all');
			$_unsorted = unserialize(file_get_contents($url));
		}
		
		if(empty($_unsorted)){
			$_unsorted = glob('content/*/blog/*.md', GLOB_NOSORT);
		}
		
	}

	return $_unsorted;
}

// Get blog post with more info about the path. Sorted by filename.
function get_post_sorted(){

	static $_sorted = array();
	
	$url = 'cache/index/index-sorted.txt';
	if (file_exists($url)) {
		$_sorted = unserialize(file_get_contents($url));
	}
	else {
		rebuilt_cache('all');
		$_sorted = unserialize(file_get_contents($url));
	}

	if(empty($_sorted)){
	
		$url = 'cache/index/index-sorted.txt';
		if (file_exists($url)) {
			$_sorted = unserialize(file_get_contents($url));
		}
		else {
			rebuilt_cache('all');
			$_sorted = unserialize(file_get_contents($url));
		}
		
		if(empty($_sorted)){
			$tmp = array();
			$tmp = glob('content/*/blog/*.md', GLOB_NOSORT);
			if (is_array($tmp)) {
				foreach($tmp as $file) {
					$_sorted[] = pathinfo($file);
				}
			}
			usort($_sorted, "sortfile");
		}
	}

	return $_sorted;
}

// Get static page path. Unsorted. 
function get_static_pages(){

	static $_page = array();
	
	if(empty($_page)){
		$url = 'cache/index/index-page.txt';
		if (file_exists($url)) {
			$_page = unserialize(file_get_contents($url));
		}
		else {
			rebuilt_cache('all');
			$_page = unserialize(file_get_contents($url));
		}
		
		if(empty($_page)){
			$_page = glob('content/static/*.md', GLOB_NOSORT);
		}
	}

	return $_page;
}

// Get author bio path. Unsorted. 
function get_author_names(){

	static $_author = array();

	if(empty($_author)){
		$url = 'cache/index/index-author.txt';
		if (file_exists($url)) {
			$_author = unserialize(file_get_contents($url));
		}
		else {
			rebuilt_cache('all');
			$_author = unserialize(file_get_contents($url));
		}
		if(empty($_author)){
			$_author = glob('content/*/author.md', GLOB_NOSORT);
		}
	}

	return $_author;
}

// Get backup file. 
function get_zip_files(){

	static $_zip = array();

	if(empty($_zip)){

		// Get the names of all the
		// zip files.

		$_zip = glob('backup/*.zip');
	}

	return $_zip;
}

// usort function. Sort by filename.
function sortfile($a, $b) {
	return $a['filename'] == $b['filename'] ? 0 : ( $a['filename'] < $b['filename'] ) ? 1 : -1;
}

// usort function. Sort by date.
function sortdate($a, $b) {
	return $a->date == $b->date ? 0 : ( $a->date < $b->date ) ? 1 : -1;
}

// Rebuilt cache index
function rebuilt_cache($type) {

	$dir = 'cache/index';
	$posts_cache_sorted = array();
	$posts_cache_unsorted = array();
	$page_cache = array();
	$author_cache = array();
	
	if(is_dir($dir) === false) {
		mkdir($dir, 0777, true);
	}
	
	if($type === 'posts') {
		$posts_cache_unsorted = glob('content/*/blog/*.md', GLOB_NOSORT);
		$string = serialize($posts_cache_unsorted);
		file_put_contents('cache/index/index-unsorted.txt', print_r($string, true));

		$tmp= array();
		$tmp = glob('content/*/blog/*.md', GLOB_NOSORT);
		
		if (is_array($tmp)) {
			foreach($tmp as $file) {
				$posts_cache_sorted[] = pathinfo($file);
			}
		}
		usort($posts_cache_sorted, "sortfile");
		$string = serialize($posts_cache_sorted);
		file_put_contents('cache/index/index-sorted.txt', print_r($string, true));
		
	}
	
	elseif ($type === 'page') {
	
		$page_cache = glob('content/static/*.md', GLOB_NOSORT);
		$string = serialize($page_cache);
		file_put_contents('cache/index/index-page.txt', print_r($string, true));
		
	}
	
	elseif ($type === 'author') {
	
		$author_cache = glob('content/*/author.md', GLOB_NOSORT);
		$string = serialize($author_cache);
		file_put_contents('cache/index/index-author.txt', print_r($string, true));
	
	}
	
	elseif ($type === 'all') {
	
		$posts_cache_unsorted = glob('content/*/blog/*.md', GLOB_NOSORT);
		$string = serialize($posts_cache_unsorted);
		file_put_contents('cache/index/index-unsorted.txt', print_r($string, true));
		
		$tmp= array();
		$tmp = glob('content/*/blog/*.md', GLOB_NOSORT);
		if (is_array($tmp)) {
			foreach($tmp as $file) {
				$posts_cache_sorted[] = pathinfo($file);
			}
		}
		usort($posts_cache_sorted, "sortfile");
		$string = serialize($posts_cache_sorted);
		file_put_contents('cache/index/index-sorted.txt', print_r($string, true));
		
		$page_cache = glob('content/static/*.md', GLOB_NOSORT);
		$string = serialize($page_cache);
		file_put_contents('cache/index/index-page.txt', print_r($string, true));
		
		$author_cache = glob('content/*/author.md', GLOB_NOSORT);
		$string = serialize($author_cache);
		file_put_contents('cache/index/index-author.txt', print_r($string, true));
		
	}
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
		
		$dt = str_replace($replaced,'',$arr[0]);
		$t = str_replace('-', '', $dt);
		$time = new DateTime($t);
		$timestamp= $time->format("Y-m-d H:i:s");
		
		// The post date
		$post->date = strtotime($timestamp);
		
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
		
		$post->file = $filepath;

		// Get the contents and convert it to HTML
		$content = MarkdownExtra::defaultTransform(file_get_contents($filepath));

		// Extract the title and body
		$arr = explode('t-->', $content);
		if(isset($arr[1])) {
			$title = str_replace('<!--t','',$arr[0]);
			$title = rtrim(ltrim($title, ' '), ' ');	
			$post->title = $title;
			$post->body = $arr[1];
		}
		else {
			$post->title = 'Untitled: ' . date('l jS \of F Y', $post->date);
			$post->body = $arr[0];
		}

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
function get_tag($tag, $page, $perpage, $random){

	$posts = get_post_sorted();
	
	if($random === true) {
		shuffle($posts);
	}
	
	$tmp = array();
	
	foreach ($posts as $index => $v) {
		$url = $v['filename'];
		$str = explode('_', $url);
		$mtag = explode(',', $str[1]);
		$etag = explode(',', $tag);
		foreach ($mtag as $t) {
			foreach ($etag as $e) {
				$e = trim($e);
				if($t === $e){
					$tmp[] = $v;
				}
			}
		}
	}
	
	if(empty($tmp)) {
		not_found();
	}
	
	$tmp = array_unique($tmp, SORT_REGULAR);
	
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
		return;	
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
	
}

// Return author bio.
function get_bio($author){

	$names = get_author_names();
	
	$username = 'config/users/' . $author . '.ini';
	
	$tmp = array();
	
	if(!empty($names)) {
	
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
				$arr = explode('t-->', $content);
				if(isset($arr[1])) {		
					$title = str_replace('<!--t','',$arr[0]);
					$title = rtrim(ltrim($title, ' '), ' ');		
					$post->title = $title;
					$post->body = $arr[1];
				}
				else {
					$post->title = $author;
					$post->body = $arr[0];
				}
	
				$tmp[] = $post;
			}
		}
	}
	
	if(!empty($tmp) || file_exists($username)) {
		return $tmp;
	}
	else {
		not_found();
	}
	
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

	if(!empty($posts)) {

		foreach($posts as $index => $v){
			if(strpos($v, $static.'.md') !== false){
			
				$post = new stdClass;
				
				// Replaced string
				$replaced = substr($v, 0, strrpos($v, '/')) . '/';
				
				// The static page URL
				$url = str_replace($replaced,'',$v);
				$post->url = site_url() . str_replace('.md','',$url);
				
				$post->file = $v;
				
				// Get the contents and convert it to HTML
				$content = MarkdownExtra::defaultTransform(file_get_contents($v));

				// Extract the title and body
				$arr = explode('t-->', $content);
				if(isset($arr[1])) {
					$title = str_replace('<!--t','',$arr[0]);
					$title = rtrim(ltrim($title, ' '), ' ');		
					$post->title = $title;
					$post->body = $arr[1];
				}
				else {
					$post->title = $static;
					$post->body = $arr[0];
				}

				$tmp[] = $post;
				
			}
		}
	
	}
	
	return $tmp;
	
}

// Return search page.
function get_keyword($keyword, $page, $perpage){

	$posts = get_post_sorted();
	
	$tmp = array();
	
	$words = explode(' ', $keyword);
	
	foreach ($posts as $index => $v) {
		$arr = explode('_', $v['filename']);
		$filter = $arr[1] .' '. $arr[2];
		foreach($words as $word) {
			if(stripos($filter, $word) !== false) {
				$tmp[] = $v;
			}
		}
	}
	
	if(empty($tmp)) {
		// a non-existing page
		render('404-search', null, false);
		die;
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
		
}

// Get related posts base on post tag.
function get_related($tag) {
	$perpage = config('related.count');
	$posts = get_tag(strip_tags($tag), 1, $perpage+1, true);
	$tmp = array();
	$req = urldecode($_SERVER['REQUEST_URI']);

	foreach ($posts as $post) {
		$url = $post->url;
		if( strpos($url, $req) === false){
			$tmp[] = $post;
		}
	}
	
	$total = count($tmp);
	
	if($total >= 1) {
		
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

// Return seaarch result count
function keyword_count($keyword) {

	$posts = get_post_sorted();
	
	$tmp = array();
	
	$words = explode(' ', $keyword);
	
	foreach ($posts as $index => $v) {
		$arr = explode('_', $v['filename']);
		$filter = $arr[1] .' '. $arr[2];
		foreach($words as $word) {
			if(strpos($filter, strtolower($word)) !== false) {
				$tmp[] = $v;
			}
		}
	}
	
	$tmp = array_unique($tmp, SORT_REGULAR);
	
	return count($tmp);
	
}

// Return an archive list, categorized by year and month.
function archive_list() {

	$posts = get_post_unsorted();
	$by_year = array();
	$col = array();
	
	if(!empty($posts)) {
	
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
		$script = <<<EOF
	if (this.parentNode.className.indexOf('expanded') > -1){this.parentNode.className = 'collapsed';this.innerHTML = '&#9658;';} else {this.parentNode.className = 'expanded';this.innerHTML = '&#9660;';}
EOF;
		echo <<<EOF
		<style>ul.archivegroup{padding:0;margin:0;}.archivegroup .expanded ul{display:block;}.archivegroup .collapsed ul{display:none;}.archivegroup li.expanded,.archivegroup li.collapsed{list-style:none;}
		</style>
EOF;
		echo '<h3>Archive</h3>';
		$i = 0; 
		$len = count($by_year);
		foreach ($by_year as $year => $months){
			if ($i == 0) {
				$class = 'expanded';
				$arrow = '&#9660;';
			} 
			else {
				$class = 'collapsed';
				$arrow = '&#9658;';
			}
			$i++;
			
			echo '<ul class="archivegroup">';
			echo '<li class="' . $class . '">';
			echo '<a href="javascript:void(0)" class="toggle" onclick="' . $script . '">' . $arrow . '</a> ';
			echo '<a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a> ';
			echo '<span class="count">(' . count($months) . ')</span>';
			echo '<ul class="month">';

			$by_month = array_count_values($months);
			# Sort the months
			krsort($by_month);
			foreach ($by_month as $month => $count){
				$name = date('F', mktime(0,0,0,$month,1,2010));
				echo '<li class="item"><a href="' . site_url() .  'archive/' . $year . '-' . $month . '">' . $name .  '</a>';
				echo ' <span class="count">(' . $count . ')</span></li>';
			}

			echo '</ul>';
			echo '</li>';
			echo '</ul>';
			
		}
	
	}
	
}

// Return tag cloud.
function tag_cloud() {

	$posts = get_post_unsorted();
	$tags = array();
	
	if(!empty($posts)) {
	
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
		$string = rtrim(ltrim($string), $string);
		if (strlen($string) < config('description.char')) {
			return $string;
		}
		else {
			$string = substr($string, 0, config('description.char'));
			return $string = substr($string, 0, strrpos($string, ' '));
		}
	}

}

// Get the teaser
function get_teaser($text, $url) {

	$teaserType = config('teaser.type');
	
	if (strlen(strip_tags($text)) < config('teaser.char') || $teaserType === 'full') {
		echo $text;
	}
	else {
		$string = preg_replace('/\s\s+/', ' ', strip_tags($text));
		$string = substr($string, 0, config('teaser.char'));
		$string = substr($string, 0, strrpos($string, ' '));
		$body = $string . '...' . ' <a class="readmore" href="' . $url . '#more">more</a>' ;
		echo '<p>' . $body . '</p>';
	}

}

// Get thumbnail from image and Youtube.
function get_thumbnail($text) {

	if (config('img.thumbnail') == 'true') {

		$teaserType = config('teaser.type');

		if (strlen(strip_tags($text)) > config('teaser.char') && $teaserType === 'trimmed') {

			libxml_use_internal_errors(true);
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
		else {
		
		}
	}
	
}

// Return edit tab on post
function tab($p) {
	$user = $_SESSION[config("site.url")]['user'];
	$role = user('role', $user);
	if(isset($p->author)) {
		if ($user === $p->author || $role === 'admin') {
			echo '<div class="tab"><a href="' . $p->url . '">View</a><a href="' . $p->url .'/edit?destination=post">Edit</a></div>';
		}
	}
	else {
		echo '<div class="tab"><a href="' . $p->url . '">View</a><a href="' . $p->url .'/edit?destination=post">Edit</a></div>';
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

	$blogcp = blog_copyright();
	$credit = 'Proudly powered by <a href="http://www.htmly.com" target="_blank">HTMLy</a>.';
	
	if (!empty($blogcp)) {
		return $copyright = '<p>' . $blogcp .  '</p><p>' . $credit . '</p>';
	}
	else {
		return $credit = '<p>' . $credit . '</p>';
	}
	
}

// Disqus on post.
function disqus($title=null, $url=null){
	$comment = config('comment.system');
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
	if (!empty($disqus) && $comment == 'disqus') {
		return $script;
	}
}

// Disqus comment count on teaser
function disqus_count(){
	$comment = config('comment.system');
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
	if (!empty($disqus) && $comment == 'disqus') {
		return $script;
	}
}

// Disqus recent comments
function recent_comments(){
	$comment = config('comment.system');
	$disqus = config('disqus.shortname');
	$script = <<<EOF
		<script type="text/javascript">
			var heading ='<h3>Comments</h3>';
			document.write(heading);
		</script>
		<script type="text/javascript" src="//{$disqus}.disqus.com/recent_comments_widget.js?num_items=5&hide_avatars=0&avatar_size=48&excerpt_length=200&hide_mods=0"></script>
		<style>li.dsq-widget-item {border-bottom: 1px solid #ebebeb;margin:0;margin-bottom:10px;padding:0;padding-bottom:10px;}a.dsq-widget-user {font-weight:normal;}img.dsq-widget-avatar {margin-right:10px; }.dsq-widget-comment {display:block;padding-top:5px;}.dsq-widget-comment p {display:block;margin:0;}p.dsq-widget-meta {padding-top:5px;margin:0;}#dsq-combo-widget.grey #dsq-combo-content .dsq-combo-box {background: transparent;}#dsq-combo-widget.grey #dsq-combo-tabs li {background: none repeat scroll 0 0 #DDDDDD;}</style>
EOF;
	if (!empty($disqus) && $comment == 'disqus') {
		return $script;
	}
}

function facebook() {
	$comment = config('comment.system');
	$appid = config('fb.appid');
	$script = <<<EOF
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId={$appid}";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<style>.fb-comments, .fb_iframe_widget span, .fb-comments iframe {width: 100%!important;}</style>
EOF;

	if(!empty($appid) && $comment == 'facebook') {
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

// Google Web Master Tool
function wmt(){
	$wmt_id = config('google.wmt.id');
	$meta_wmt = '<meta name="google-site-verification" content="' . $wmt_id . '" />';
	if (!empty($wmt_id)) {
		return $meta_wmt;
	}
}

// Menu
function menu(){
	$menu = config('blog.menu');
	$req = $_SERVER['REQUEST_URI'];
	
	if (!empty($menu)) {
	
		$links = explode('|', $menu);
		
		echo '<ul class="nav">';
		
		$i = 0; 
		$len = count($links);
		
		foreach($links as $link) {
		
			if ($i == 0) {
				$class = 'item first';
			} 
			elseif ($i == $len - 1) {
				$class = 'item last';
			}
			else {
				$class = 'item';
			}
			
			$i++;	
			
			$anc = explode('->', $link);
			
			if(isset($anc[0]) && isset($anc[1])) {
			
				if(strpos(rtrim($anc[1],'/').'/', site_url()) !== false) {
					$id = substr($link, strrpos($link, '/')+1 );
					$file = 'content/static/' . $id . '.md';
					if(file_exists($file)) {
						if(strpos($req, $id) !== false){
							echo '<li class="' . $class . ' active"><a href="' . $anc[1] . '">' . $anc[0] . '</a></li>';
						}
						else {
							echo '<li class="' . $class . '"><a href="' . $anc[1] . '">' . $anc[0] . '</a></li>';
						}
					}
					else {
						if (rtrim($anc[1],'/').'/' == site_url()) {
							if($req == site_path() . '/') {
								echo '<li class="' . $class . ' active"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
							}
							else {
								echo '<li class="' . $class . '"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
							}
						}
					}
				}
				else {
					echo '<li class="' . $class . '"><a target="_blank" href="' . $anc[1] . '">' . $anc[0] . '</a></li>';
				}
				
			}
		}
		
		echo '</ul>';
	}
	else {
		get_menu();
	}
}

// Auto generate menu from static page
function get_menu() {

	$posts = get_static_pages();
	$req = $_SERVER['REQUEST_URI'];

	if(!empty($posts)) {

		krsort($posts);
		
		echo '<ul class="nav">';
		if($req == site_path() . '/') {
			echo '<li class="item first active"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
		}
		else {
			echo '<li class="item first"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
		}
		
		$i = 0; 
		$len = count($posts);
		
		foreach($posts as $index => $v){
		
				if ($i == $len - 1) {
					$class = 'item last';
				}
				else {
					$class = 'item';
				}
				$i++;
		
			// Replaced string
			$replaced = substr($v, 0, strrpos($v, '/')) . '/';
			$base = str_replace($replaced,'',$v);
			$url = site_url() . str_replace('.md','',$base);
			
			// Get the contents and convert it to HTML
			$content = MarkdownExtra::defaultTransform(file_get_contents($v));

			// Extract the title and body
			$arr = explode('t-->', $content);
			if(isset($arr[1])) {
				$title = str_replace('<!--t','',$arr[0]);
				$title = rtrim(ltrim($title, ' '), ' ');		
			}
			else {
				$title = str_replace('-',' ', str_replace('.md','',$base));
			}
			
			if(strpos($req, str_replace('.md','',$base)) !== false){
				echo '<li class="' . $class . ' active"><a href="' . $url . '">' . ucwords($title) . '</a></li>';
			}
			else {
				echo '<li class="' . $class . '"><a href="' . $url . '">' . ucwords($title) . '</a></li>';
			}
				
		}
		echo '</ul>';
	
	}
	else {
	
		echo '<ul class="nav">';
		if($req == site_path() . '/') {
			echo '<li class="item first active"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
		}
		else {
			echo '<li class="item first"><a href="' . site_url() . '">' .config('breadcrumb.home'). '</a></li>';
		}
		echo '</ul>';
	
	}
	
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
	$rssLength = config('rss.char');
	
	$channel
		->title(blog_title())
		->description(blog_description())
		->url(site_url())
		->appendTo($feed);

	foreach($posts as $p){
	
		if(!empty($rssLength)) {
			if (strlen(strip_tags($p->body)) < config('rss.char')) {
				$string = preg_replace('/\s\s+/', ' ', strip_tags($p->body));
				$body = $string . '...' . ' <a class="readmore" href="' . $p->url . '#more">more</a>' ;
			}
			else {
				$string = preg_replace('/\s\s+/', ' ', strip_tags($p->body));
				$string = substr($string, 0, config('rss.char'));
				$string = substr($string, 0, strrpos($string, ' '));
				$body = $string . '...' . ' <a class="readmore" href="' . $p->url . '#more">more</a>' ;
			}
		}
		else {
			$body = $p->body;
		}
	
		$item = new Item();
		$tags = explode(',', str_replace(' ', '', strip_tags($p->tag)));
		foreach($tags as $tag) {
			$item
				->category($tag, site_url() . 'tag/' . $tag );
		}
		$item
			->title($p->title)
			->pubDate($p->date)
			->description($body)
			->url($p->url)
			->appendTo($channel);
	}
	
	echo $feed;
}

// Return post, archive url for sitemap
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
		
		$dt = str_replace($replaced,'',$arr[0]);
		$t = str_replace('-', '', $dt);
		$time = new DateTime($t);
		$timestamp= $time->format("Y-m-d H:i:s");
		
		// The post date
		$post->date = strtotime($timestamp);
		
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

// Return static page path for sitemap
function get_static_path(){

	$posts = get_static_pages();

	$tmp = array();
	
	if(!empty($posts)) {

		foreach($posts as $index => $v){
			
			$post = new stdClass;
			
			// Replaced string
			$replaced = substr($v, 0, strrpos($v, '/')) . '/';
			
			// The static page URL
			$url = str_replace($replaced,'',$v);
			$post->url = site_url() . str_replace('.md','',$url);

			$tmp[] = $post;
				
		}
	
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
		echo '<url><loc>' . site_url() . '</loc><priority>1.0</priority></url>';
		echo '</urlset>';
		
	}
	elseif ($str == 'post') {
	
		$posts = get_path();
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		foreach($posts as $p) {
			echo '<url><loc>' . $p->url . '</loc><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'static') {
	
		$posts = get_static_path();
		
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		if(!empty($posts)) {
		
			foreach($posts as $p) {
				echo '<url><loc>' . $p->url . '</loc><priority>0.5</priority></url>';
			}
		
		}
		
		echo '</urlset>';
		
	}
	elseif ($str == 'tag') {
	
		$posts = get_post_unsorted();
		$tags = array();
		
		if(!empty($posts)) {
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
			
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			if(isset($tag)) {
			
				$tag = array_unique($tag, SORT_REGULAR);
				
				foreach($tag as $t) {
					echo '<url><loc>' . $t . '</loc><priority>0.5</priority></url>';
				}
			
			}
			
			echo '</urlset>';
		
		}
		
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
			echo '<url><loc>' . $d . '</loc><priority>0.5</priority></url>';
		}
		
		foreach($month as $m) {
			echo '<url><loc>' . $m . '</loc><priority>0.5</priority></url>';
		}
		
		foreach($year as $y) {
			echo '<url><loc>' . $y . '</loc><priority>0.5</priority></url>';
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
			echo '<url><loc>' . $a . '</loc><priority>0.5</priority></url>';
		}
		
		echo '</urlset>';
		
	}
	
}

// Function to generate OPML file
function generate_opml(){
	
	$opml_data = array(
		'head' => array(
			'title' => blog_title() . ' OPML File',
			'ownerName' => blog_title(),
			'ownerId' => site_url() 
			),
		'body' => array(
			array(
				'text' => blog_title(),
				'description' => blog_description(),
				'htmlUrl' => site_url(),
				'language' => 'unknown',
				'title' => blog_title(),
				'type' => 'rss',
				'version' => 'RSS2',
				'xmlUrl' => site_url() . 'feed/rss'
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

// Create Zip files
function Zip($source, $destination, $include_dir = false) {

	if (!extension_loaded('zip') || !file_exists($source)) {
		return false;
	}

	if (file_exists($destination)) {
		unlink ($destination);
	}

	$zip = new ZipArchive();
	
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		return false;
	}

	if (is_dir($source) === true) {

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file) {
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
				continue;

			if (is_dir($file) === true) {
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true) {
				$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
			}
		}
		
	}
	else if (is_file($source) === true) {
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	return $zip->close();
}

// TRUE if the current page is the front page.
function is_front() {
	$req = $_SERVER['REQUEST_URI'];
	if($req == site_path() . '/' || strpos($req, site_path() . '/?page') !== false) {
		return true;
	}
	else {
		return false;
	}
}

// TRUE if the current page is an index page like frontpage, tag index, archive index and search index.
function is_index() {
	$req = $_SERVER['REQUEST_URI'];
	if(strpos($req, '/archive/') !== false || strpos($req, '/tag/') !== false || strpos($req, '/search/') !== false || $req == site_path() . '/' || strpos($req, site_path() . '/?page') !== false){
		return true;
	}
	else {
		return false;
	}
}

// Return blog title
function blog_title() {
	return config('blog.title');
}

// Return blog tagline
function blog_tagline() {
	return config('blog.tagline');
}

// Return blog description
function blog_description() {
	return config('blog.description');
}

// Return blog copyright
function blog_copyright() {
	return config('blog.copyright');
}

// Return author info
function authorinfo($title=null, $body=null) {
	if (config('author.info') == 'true') {
		return '<div class="author-info"><h4>by <strong>' . $title . '</strong></h4>' . $body . '</div>';
	}
}

function head_contents($title, $description, $canonical) {
	$styleImage = config('lightbox');
	$jq = config('jquery');
	$output = '';

	$title = '<title>' . $title . '</title>';
	$favicon = '<link href="' . site_url() . 'favicon.ico" rel="icon" type="image/x-icon"/>';
	$charset = '<meta charset="utf-8" />';
	$generator = '<meta content="htmly" name="generator"/>';
	$xua = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
	$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />';
	$description = '<meta name="description" content="'. $description .'"/>';
	$sitemap = '<link rel="sitemap" href="' . site_url() . 'sitemap.xml" />';
	$canonical = '<link rel="canonical" href="' . $canonical . '" />';
	$feed = '<link rel="alternate" type="application/rss+xml" title="'. blog_title() .' Feed" href="' . site_url() . 'feed/rss" />';
	$lightboxcss = '<link href="' . site_url() . 'system/plugins/lightbox/css/lightbox.css" rel="stylesheet" />';
	$jquery = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>';
	$lightbox = '<script src="' . site_url() . 'system/plugins/lightbox/js/lightbox-2.6.min.js"></script>';
	$corejs = '<script src="' . site_url() . 'system/resources/htmly.js"></script>';
	
	if($styleImage == 'on') {
		$output .= $title ."\n". $favicon ."\n". $charset ."\n". $generator ."\n". $xua ."\n". $viewport ."\n". $description ."\n". $sitemap ."\n". $canonical ."\n". $feed ."\n". $lightboxcss ."\n". $jquery ."\n". $lightbox ."\n" .$corejs ."\n";
	}
	else {
		if($jq == 'enable') {
			$output .= $title ."\n". $favicon ."\n". $charset ."\n". $generator ."\n". $xua ."\n". $viewport ."\n". $description ."\n". $sitemap ."\n". $canonical ."\n". $feed ."\n". $jquery ."\n";
		}
		else {
			$output .= $title ."\n". $favicon ."\n". $charset ."\n". $generator ."\n". $xua ."\n". $viewport ."\n". $description ."\n". $sitemap ."\n". $canonical ."\n". $feed ."\n";
		}
	}
	
	return $output;
	
}

// Return toolbar
function toolbar() {
	$user = $_SESSION[config("site.url")]['user'];
	$role = user('role', $user);
	$base = site_url();
	
	$CSRF = get_csrf();
	
	$updater = new Updater;
	
	echo <<<EOF
	<link href="{$base}themes/default/css/toolbar.css" rel="stylesheet" />
EOF;
	echo '<div id="toolbar"><ul>';
	echo '<li><a href="'.$base.'admin">Admin</a></li>';
	if ($role === 'admin') {echo '<li><a href="'.$base.'admin/posts">Posts</a></li>';}
	echo '<li><a href="'.$base.'admin/mine">Mine</a></li>';
	echo '<li><a href="'.$base.'add/post">Add post</a></li>';
	echo '<li><a href="'.$base.'add/page">Add page</a></li>';
	echo '<li><a href="'.$base.'edit/profile">Edit profile</a></li>';
	echo '<li><a href="'.$base.'admin/import">Import</a></li>';
	echo '<li><a href="'.$base.'admin/backup">Backup</a></li>';
	echo '<li><a href="'.$base.'admin/clear-cache">Clear cache</a></li>';
	if( $updater->updateAble())
	{
		echo '<li><a href="'.$base.'admin/update/now/' . $CSRF . '">Update to ' . $updater->getName() . '</a></li>';
	}
	echo '<li><a href="'.$base.'logout">Logout</a></li>';
		
	echo '</ul></div>';
}

// File cache 
function file_cache($request) {

	$c = str_replace('/', '#', str_replace('?', '~', $request));
	$cachefile = 'cache/page/' . $c . '.cache';
	
	if (file_exists($cachefile)) {
	    header('Content-type: text/html; charset=utf-8');
		readfile($cachefile);
		die;
	}
}

function generate_csrf_token()
{
	$_SESSION[config("site.url")]['csrf_token'] = sha1(microtime(true).mt_rand(10000,90000));
}

function get_csrf()
{
	if(! isset($_SESSION[config("site.url")]['csrf_token']) || empty($_SESSION[config("site.url")]['csrf_token']))
	{
		generate_csrf_token();
	}
	return $_SESSION[config("site.url")]['csrf_token'];
}

function is_csrf_proper($csrf_token)
{
	if($csrf_token == get_csrf())
	{
		return true;
	}
	return false;
}
