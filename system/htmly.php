<?php

// Change this to your timezone
date_default_timezone_set('Asia/Jakarta');

// Explicitly including the dispatch framework,
// and our functions.php file
require 'system/includes/dispatch.php';
require 'system/includes/functions.php';

// Load the configuration file
config('source', 'system/config.ini');

// The front page of the blog.
// This will match the root url
get('/index', function () {

	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1;
	$perpage = config('posts.perpage');
	
	$posts = get_posts(null, $page, $perpage);
	
	$total = '';
	
	if(empty($posts) || $page < 1){
		// a non-existing page
		not_found();
	}
	
	$tl = config('blog.tagline');
	
	if($tl){ $tagline = ' - ' . $tl;} else {$tagline = '';}
	
    render('main',array(
		'title' => config('blog.title') . $tagline,
    	'page' => $page,
		'posts' => $posts,
		'canonical' => config('site.url'),
		'description' => config('blog.description'),
		'bodyclass' => 'infront',
		'breadcrumb' => '',
		'pagination' => has_pagination($total, $perpage, $page)
	));
});

// The tag page
get('/tag/:tag',function($tag){

	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1;
	$perpage = config('tag.perpage');

	$posts = get_tag($tag);
	
	$total = count($posts);
	
	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);

	if(empty($posts) || $page < 1){
		// a non-existing page
		not_found();
	}
	
    render('main',array(
		'title' => 'Posts tagged: ' . $tag .' - ' . config('blog.title'),
    	'page' => $page,
		'posts' => $posts,
		'canonical' => config('site.url') . '/tag/' . $tag,
		'description' => 'All posts tagged ' . $tag . ' on '. config('blog.title') . '.',
		'bodyclass' => 'intag',
		'breadcrumb' => '<a href="' . config('site.url') .  '">Home</a> &#187; Posts tagged: ' . $tag,
		'pagination' => has_pagination($total, $perpage, $page)
	));
});

// The archive page
get('/archive/:req',function($req){

	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1;
	$perpage = config('archive.perpage');

	$posts = get_archive($req);
	
	$total = count($posts);
	
	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);

	if(empty($posts) || $page < 1){
		// a non-existing page
		not_found();
	}
	
	$time = explode('-', $req);
	$date = strtotime($req);
	
	if (isset($time[0]) && isset($time[1]) && isset($time[2])) {
		$timestamp = date('d F Y', $date);
	}
	else if (isset($time[0]) && isset($time[1])) {
		$timestamp = date('F Y', $date);
	}		
	else {
		$timestamp = $req;
	}	
	
	if(!$date){
		// a non-existing page
		not_found();
	}
	
    render('main',array(
		'title' => 'Archive for: ' . $timestamp .' - ' . config('blog.title'),
    	'page' => $page,
		'posts' => $posts,
		'canonical' => config('site.url') . '/archive/' . $req,
		'description' => 'Archive page for: ' . $timestamp . ' on ' . config('blog.title') . '.',
		'bodyclass' => 'inarchive',
		'breadcrumb' => '<a href="' . config('site.url') .  '">Home</a> &#187; Archive for: ' . $timestamp,
		'pagination' => has_pagination($total, $perpage, $page)
	));
});

// The blog post page
get('/:year/:month/:name', function($year, $month, $name){

	$post = find_post($year, $month, $name);
	
	$current = $post['current'];
	
	if(!$current){
		not_found();
	}
	
	$bio = find_bio($current->author);
	
	if (array_key_exists('prev', $post)) {
		$prev = $post['prev'];
	}
	else {
		$prev = array();
	}
	
	if (array_key_exists('next', $post)) {
		$next= $post['next'];
	}
	else {
		$next = array();
	}
	
	render('post',array(
		'title' => $current->title .' - ' . config('blog.title'),
		'p' => $current,
		'authorinfo' => '<div class="author-info"><h4>by <strong>' . $bio->title . '</strong></h4>' . $bio->body . '</div>',
		'canonical' => $current->url,
		'description' => $description = get_description($current->body),
		'bodyclass' => 'inpost',
		'breadcrumb' => '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . config('site.url') .  '">Home</a></span> &#187; <span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="' . $current->tagurl .'">' . $current->tag . '</a></span> &#187; ' . $current->title,
		'prev' => has_prev($prev),
		'next' => has_next($next),
		'type' => 'blogpost',
	));
});

// The search page
get('/search/:keyword', function($keyword){

	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1;
	$perpage = config('search.perpage');

	$posts = get_keyword($keyword);
	
	$total = count($posts);
	
	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);

	if(empty($posts) || $page < 1){
		// a non-existing page
		render('404-search', null, false);
		die;
	}
	
    render('main',array(
		'title' => 'Search results for: ' . $keyword . ' - ' . config('blog.title'),
    	'page' => $page,
		'posts' => $posts,
		'canonical' => config('site.url') . '/search/' . $keyword,
		'description' => 'Search results for: ' . $keyword . ' on '. config('blog.title') . '.',
		'bodyclass' => 'insearch',
		'breadcrumb' => '<a href="' . config('site.url') .  '">Home</a> &#187; Search results for: ' . $keyword,
		'pagination' => has_pagination($total, $perpage, $page)
	));

});

// The static page
get('/:spage', function($spage){

	$post = find_spage($spage);
	
	if(!$post){
		not_found();
	}

	render('post',array(
		'title' => $post->title .' - ' . config('blog.title'),
		'canonical' => $post->url,
		'description' => $description = get_description($post->body),
		'bodyclass' => 'inpage',
		'breadcrumb' => '<a href="' . config('site.url') . '">Home</a> &#187; ' . $post->title,
		'p' => $post,
		'type' => 'staticpage',
	));
});

// The author page
get('/author/:profile',function($profile){

	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1;
	$perpage = config('profile.perpage');

	$posts = get_profile($profile);
	$bio = find_bio($profile);
	
	$total = count($posts);
	
	// Extract a specific page with results
	$posts = array_slice($posts, ($page-1) * $perpage, $perpage);

	if(empty($posts) || $page < 1){
		// a non-existing page
		not_found();
	}
	
    render('profile',array(
		'title' => 'Profile for:  '. $bio->title .' - ' . config('blog.title'),
    	'page' => $page,
		'posts' => $posts,
		'bio' => $bio->body,
		'name' => $bio->title,
		'canonical' => config('site.url') . '/author/' . $profile,
		'description' => 'Profile page and all posts by ' . $bio->title . ' on ' . config('blog.title') . '.',
		'bodyclass' => 'inprofile',
		'breadcrumb' => '<a href="' . config('site.url') .  '">Home</a> &#187; Profile for: ' . $bio->title,
		'pagination' => has_pagination($total, $perpage, $page)
	));
});

// The JSON API
get('/api/json',function(){

	header('Content-type: application/json');

	// Print the 10 latest posts as JSON
	echo generate_json(get_posts(null, 1, config('json.count')));
});

// Show the RSS feed
get('/feed/rss',function(){

	header('Content-Type: application/rss+xml');

	// Show an RSS feed with the 30 latest posts
	echo generate_rss(get_posts(null, 1, config('rss.count')));
});


// If we get here, it means that
// nothing has been matched above

get('.*',function(){
	not_found();
});

// Serve the blog
dispatch();