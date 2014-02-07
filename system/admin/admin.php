<?php

function user($key, $user=null) {
	$value = 'config/users/' . $user . '.ini';
	static $_config = array();
	if (file_exists($value)) {
		$_config = parse_ini_file($value, true);
		return $_config[$key];
	}
}

function session($user, $pass, $str = null) {
		$user_file = 'config/users/' . $user . '.ini';
		$user_pass = user('password', $user);
		
		if(file_exists($user_file)) {
			if($pass === $user_pass) {
				$_SESSION['user'] = $user;
				header('location: admin');
			}
			else {
				return $str = '<li>Your username and password mismatch.</li>';
			}
		}
		else {
			return $str = '<li>Username not found in our record.</li>';
		}
}

function edit_post($title, $tag, $url, $content, $oldfile, $destination = null) {

	$oldurl = explode('_', $oldfile);

	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tag);
	$post_tag = str_replace(' ', '-',$post_tag);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
	$post_url = rtrim(ltrim($post_url, ',\.\-'), ',\.\-');
	$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
		
	if(!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$newfile = $oldurl[0] . '_' . $post_tag . '_' . $post_url . '.md';
		if($oldfile === $newfile) {
			file_put_contents($oldfile, print_r($post_content, true));
		}
		else {
			rename($oldfile, $newfile);
			file_put_contents($newfile, print_r($post_content, true));
		}
		
		$replaced = substr($oldurl[0], 0,strrpos($oldurl[0], '/')) . '/';
		$dt = str_replace($replaced,'',$oldurl[0]);
		$t = str_replace('-','',$dt);
		$time = new DateTime($t);
		$timestamp= $time->format("Y-m-d");
		// The post date
		$postdate = strtotime($timestamp);
		// The post URL
		$posturl = site_url().date('Y/m', $postdate).'/'.$post_url;
		
		if($destination == 'admin/posts') {
			$redirect = site_url() . 'admin/posts';
			header("Location: $redirect");
		}
		elseif($destination == 'admin') {
			$redirect = site_url() . 'admin';
			header("Location: $redirect");
		}
		elseif ($destination == 'post') {
			header("Location: $posturl");
		}
		else {
			$redirect = site_url();
			header("Location: $redirect");
		}
	}
		
}

function edit_page($title, $url, $content, $oldfile, $destination = null) {

	$dir = substr($oldfile, 0, strrpos($oldfile, '/'));

	$post_title = $title;
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
	$post_url = rtrim(ltrim($post_url, ',\.\-'), ',\.\-');
	$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
		
	if(!empty($post_title) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$newfile = $dir . '/' . $post_url . '.md';
		if($oldfile === $newfile) {
			file_put_contents($oldfile, print_r($post_content, true));
		}
		else {
			rename($oldfile, $newfile);
			file_put_contents($newfile, print_r($post_content, true));
		}
		
		$posturl = site_url() . $post_url;
		
		if($destination == 'admin') {
			$redirect = site_url() . 'admin';
			header("Location: $redirect");
		}
		elseif ($destination == 'post') {
			header("Location: $posturl");
		}
		else {
			$redirect = site_url();
			header("Location: $redirect");
		}
		
	}
		
}

function add_post($title, $tag, $url, $content, $user) {

	$post_date = date('Y-m-d-H-i-s');
	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tag);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
	$post_url = rtrim(ltrim($post_url, ' \,\.\-'), ' \,\.\-');
	$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
	
	if(!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
		$dir = 'content/' . $user. '/blog/';
		if(is_dir($dir)) {
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		else {
			mkdir($dir, 0777, true);
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		$redirect = site_url() . 'admin/posts';
		header("Location: $redirect");	
	}
	
}

function add_page($title, $url, $content) {

	$post_title = $title;
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
	$post_url = rtrim(ltrim($post_url, ',\.\-'), ',\.\-');
	$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
	
	if(!empty($post_title) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$filename = $post_url . '.md';
		$dir = 'content/static/';
		if(is_dir($dir)) {
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		else {
			mkdir($dir, 0777, true);
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		$redirect = site_url() . 'admin';
		header("Location: $redirect");
	}
	
}

function delete_post($file, $destination) {
	$deleted_content = $file;
	if(!empty($deleted_content)) {
		unlink($deleted_content);
		if($destination == 'post') {
			$redirect = site_url();
			header("Location: $redirect");
		}
		else {
			$redirect = site_url() . $destination;
			header("Location: $redirect");
		}	
	}
}

function delete_page($file, $destination) {
	$deleted_content = $file;
	if(!empty($deleted_content)) {
		unlink($deleted_content);
		if($destination == 'post') {
			$redirect = site_url();
			header("Location: $redirect");
		}
		else {
			$redirect = site_url() . $destination;
			header("Location: $redirect");
		}			
	}
}

function edit_profile($title, $content, $user) {

	$user_title = $title;
	$user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;
	
	if(!empty($user_title) && !empty($user_content)) {
		if(get_magic_quotes_gpc()) {
			$user_content = stripslashes($user_content);
		}
		$dir = 'content/' . $user. '/';
		$filename = 'content/' . $user . '/author.md';
		if(is_dir($dir)) {
			file_put_contents($filename, print_r($user_content, true));
		}
		else {
			mkdir($dir, 0777, true);
			file_put_contents($filename, print_r($user_content, true));
		}
		$redirect = site_url() . 'admin';
		header("Location: $redirect");			
	}
	
}

function migrate($title, $time, $tags, $content, $url, $user, $source) {

	$post_date = date('Y-m-d-H-i-s', $time);
	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tags);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
	$post_url = rtrim(ltrim($post_url, ',\.\-'), ',\.\-');
	if(!empty($source)) {
		$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content . "\n\n" . 'Source: <a target="_blank" href="' . $source . '">' . $title . '</a>';
	}
	else {
		$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $content;
	}
	if(!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
		$dir = 'content/' . $user. '/blog/';
		if(is_dir($dir)) {
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		else {
			mkdir($dir, 0777, true);
			file_put_contents($dir . $filename, print_r($post_content, true));
		}
		$redirect = site_url() . 'admin/posts';
		header("Location: $redirect");	
	}
	
}

function get_feed($feed_url, $credit, $message=null) {  
    $source = file_get_contents($feed_url);
    $feed = new SimpleXmlElement($source);
	if(!empty($feed->channel->item)) {
		foreach($feed->channel->item as $entry) {
			$descriptionA = $entry->children('content', true);
			$descriptionB = $entry->description;
			if(!empty($descriptionA)) {
				$content = $descriptionA;
			}
			else if (!empty($descriptionB)) {
				$content = preg_replace('#<br\s*/?>#i', "\n", $descriptionB);
			}
			else {
				return $str = '<li>Can not read the feed content.</li>';
			}
			$time = new DateTime($entry->pubDate);
			$timestamp= $time->format("Y-m-d H:i:s");
			$time = strtotime($timestamp);
			$tags = strip_tags(preg_replace('/[^A-Za-z0-9,.-]/u', '', $entry->category));
			$title = rtrim($entry->title, ' \,\.\-');
			$title = ltrim($title, ' \,\.\-');
			$user = $_SESSION['user'];
			$url = preg_replace('/[^A-Za-z0-9 .-]/u', '', strtolower($title));
			$url = str_replace(' ', '-',$url);
			$url = str_replace('--', '-',$url);
			$url = rtrim($url, ',\.\-');
			$url = ltrim($url, ',\.\-');
			if ($credit == 'yes') {
				$source = $entry->link;
			}
			else {
				$source= null;
			}
			migrate($title, $time, $tags, $content, $url, $user, $source);
		}
	}
	else {
		return $str= '<li>Unsupported feed.</li>';
	}
	
}  

function get_recent_posts() {
	if (isset($_SESSION['user'])) {
		$posts = get_profile($_SESSION['user'], 1, 5);
		if(!empty($posts)) {
			echo '<table class="post-list">';
			echo '<tr><th>Title</th><th>Published</th><th>Tag</th><th>Operations</th></tr>';
			foreach($posts as $p) {
				echo '<tr>';
				echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
				echo '<td>' . date('d F Y', $p->date) . '</td>';
				echo '<td>' . $p->tag . '</td>';
				echo '<td><a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
}

// Auto generate menu from static page
function get_recent_pages() {
	if (isset($_SESSION['user'])) {
		$posts = get_static_post(null);
		if(!empty($posts)) {
			krsort($posts);
			echo '<table class="post-list">';
			echo '<tr><th>Title</th><th>Operations</th></tr>';
			foreach($posts as $p) {
				echo '<tr>';
				echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
				echo '<td><a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
}