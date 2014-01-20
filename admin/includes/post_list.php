<?php
ob_start();
// Get blog post with more info about the path. Sorted by filename.
function admin_get_post(){

	static $tmp= array();
	
	static $_cache = array();

	if(empty($_cache)){

		// Get the names of all the posts

		$tmp = glob('../content/*/blog/*.md', GLOB_NOSORT);
		
		foreach($tmp as $file) {
			$_cache[] = pathinfo($file);
		}
		
	}
	
	usort($_cache, "sortfile");
	
	return $_cache;
}

// usort function. Sort by filename.
function sortfile($a, $b) {
	return $a['filename'] == $b['filename'] ? 0 : ( $a['filename'] < $b['filename'] ) ? 1 : -1;
}
// Return blog posts. 
function get_posts($posts, $page = 1, $perpage = 0){
		
	if(empty($posts)) {
		$posts = admin_get_post();
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
		
		// The post tag
		$post->tag = str_replace($replaced,'',$arr[1]);
		
		// The post tag url
		$post->tagurl = site_url(). 'tag/' . $arr[1];
		
		$post->file = $filepath;

		// Get the contents and convert it to HTML
		// $content = file_get_contents($filepath);
		// $post->content = $content;

		$tmp[] = $post;
	}

	return $tmp;
}

// Return posts list on profile.
function get_profile($profile, $page, $perpage){

	$posts = admin_get_post();
	
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
		echo '<tr><td>No posts found!</td></tr>';
		return;
	}
	
	return $tmp = get_posts($tmp, $page, $perpage);
	
}

?>

<table>
<?php 

$posts = get_profile($_SESSION['user'], null, null);

if(!empty($posts)) {

	foreach($posts as $p) {
		echo '<tr>';
		echo '<td>' . $p->file . '</td>';
		echo '<td><form method="GET" action="includes/edit_post.php"><input type="submit" name="submit" value="Edit"/><input type="hidden" name="url" value="' . $p->file . '"/></form></td>';
		echo '</tr>';
	}

}

?>
</table>
