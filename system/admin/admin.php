<?php

// Return username.ini value
function user($key, $user=null) {
	$value = 'config/users/' . $user . '.ini';
	static $_config = array();
	if (file_exists($value)) {
		$_config = parse_ini_file($value, true);
		if(!empty($_config[$key])) {
			return $_config[$key];
		}
	}
}

// Create a session
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

// Edit blog posts
function edit_post($title, $tag, $url, $content, $oldfile, $destination = null) {

	$oldurl = explode('_', $oldfile);

	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tag);
	$post_tag = str_replace(' ', '-',$post_tag);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
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
		
		if ($destination == 'post') {
			header("Location: $posturl");
		}
		else {
			$redirect = site_url() . $destination;
			header("Location: $redirect");
		}
		
	}
		
}

// Edit static page
function edit_page($title, $url, $content, $oldfile, $destination = null) {

	$dir = substr($oldfile, 0, strrpos($oldfile, '/'));

	$post_title = $title;
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
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
		
		if ($destination == 'post') {
			header("Location: $posturl");
		}
		else {
			$redirect = site_url() . $destination;
			header("Location: $redirect");
		}
		
	}
		
}

// Add blog post
function add_post($title, $tag, $url, $content, $user) {

	$post_date = date('Y-m-d-H-i-s');
	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tag);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
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
		$redirect = site_url() . 'admin/mine';
		header("Location: $redirect");	
	}
	
}

// Add static page
function add_page($title, $url, $content) {

	$post_title = $title;
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
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

// Delete blog post
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

// Delete static page
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

// Edit user profile
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
		$redirect = site_url() . 'author/' . $user;
		header("Location: $redirect");			
	}
	
}

// Import RSS feed
function migrate($title, $time, $tags, $content, $url, $user, $source) {

	$post_date = date('Y-m-d-H-i-s', $time);
	$post_title = $title;
	$post_tag = preg_replace('/[^A-Za-z0-9,.-]/u', '', $tags);
	$post_tag = rtrim(ltrim($post_tag, ',\.\-'), ',\.\-');
	$post_url = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($url));
	$post_url = str_replace(' ', '-',$post_url);
	$post_url = str_replace('--', '-',$post_url);
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
		
		$redirect = site_url() . 'admin/mine';
		header("Location: $redirect");	
	}
	
}

// Fetch RSS feed
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

// Get recent posts by user
function get_recent_posts() {
	if (isset($_SESSION['user'])) {
		$posts = get_profile($_SESSION['user'], 1, 5);
		if(!empty($posts)) {
			echo '<table class="post-list">';
			echo '<tr class="head"><th>Title</th><th>Published</th><th>Tag</th><th>Operations</th></tr>';
			$i = 0; $len = count($posts);
			foreach($posts as $p) {
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
				echo '<tr class="' . $class . '">';
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

// Get all static pages
function get_recent_pages() {
	if (isset($_SESSION['user'])) {
		$posts = get_static_post(null);
		if(!empty($posts)) {
			krsort($posts);
			echo '<table class="post-list">';
			echo '<tr class="head"><th>Title</th><th>Operations</th></tr>';
			$i = 0; $len = count($posts);
			foreach($posts as $p) {
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
				echo '<tr class="' . $class . '">';
				echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title . '</a></td>';
				echo '<td><a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
}

// Get all available zip files
function get_backup_files () {
	if (isset($_SESSION['user'])) {
		$files = get_zip_files();
		if(!empty($files)) {
			krsort($files);
			echo '<table class="backup-list">';
			echo '<tr class="head"><th>Filename</th><th>Date</th><th>Operations</th></tr>';
			$i = 0; $len = count($files);
			foreach($files as $file) {
			
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
				
				// Extract the date
				$arr = explode('_', $file);
				
				// Replaced string
				$replaced = substr($arr[0], 0,strrpos($arr[0], '/')) . '/';
				
				$name = str_replace($replaced,'',$file);
				
				$date = str_replace('.zip','',$arr[1]);
				$t = str_replace('-', '', $date);
				$time = new DateTime($t);
				$timestamp= $time->format("D, d F Y, H:i:s");
				
				$url = site_url() . $file;
				echo '<tr class="' . $class . '">';
				echo '<td>' . $name . '</td>';
				echo '<td>' . $timestamp . '</td>';
				echo '<td><a target="_blank" href="' . $url . '">Download</a> <form method="GET"><input type="hidden" name="file" value="' . $file . '"/><input type="submit" name="submit" value="Delete"/></form></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
		else {
			echo 'No available backup!';
		}
	}
}