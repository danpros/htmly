<?php
define('HTMLY', true);
require 'system/vendor/autoload.php';

if (login()) {
		
	// Automatically Save Draft
	function auto_save_page($title, $url, $content, $draft, $description = null) 
	{
		$post_title = safe_html($title);
		if (empty($url)) {
			$url = $title;
		}
		$post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
		$description = safe_html($description);
		if ($description !== null) {
			if (!empty($description)) {        
				$post_description = "\n<!--d " . $description . " d-->";
			} else {
				$post_description = "\n<!--d " . $content . " d-->";
			}            
		} else {
			$post_description = "";
		}
		$posts = get_static_pages();
		$timestamp = date('YmdHis');
		foreach ($posts as $index => $v) {
			if (strtolower($v['basename']) === strtolower($post_url . '.md')) {
				$post_url = $post_url .'-'. $timestamp;
			} else {
				$post_url = $post_url;
			}
		}
		$post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
		if (!empty($post_title) && !empty($post_url) && !empty($post_content)) {

			$filename = $post_url . '.md';
			$dir = 'content/static/';
			$dirDraft = 'content/static/draft/';

			if (!is_dir($dirDraft)) {
				mkdir($dirDraft, 0775, true);
			} 
			file_put_contents($dirDraft . $filename, print_r($post_content, true), LOCK_EX);
			return "Auto Saved";
		}
	}
	
	// Automatically Save Draft
	function auto_save_post($title, $tag, $url, $content, $user, $draft, $category, $type, $description = null, $media = null, $dateTime = null)
	{
		$tag = explode(',', preg_replace("/\s*,\s*/", ",", rtrim($tag, ',')));
		$tag = array_filter(array_unique($tag));
		$tagslang = "content/data/tags.lang";
		if (file_exists($tagslang)) {
			$taglang = array_flip(unserialize(file_get_contents($tagslang)));
			$tflip = array_intersect_key($taglang, array_flip($tag));
			$post_tag = array();
			$post_tagmd = array();
			foreach ($tag as $t) {
				if (array_key_exists($t, $tflip)) {
					foreach ($tflip as $tfp => $tf){
						if($t == $tfp) {
							$post_tag[] = $tf;
							$post_tagmd[] = $tfp;
						}
					}
				} else {
					$post_tag[] = $t;
					$post_tagmd[] = $t;
				}
			}

			$post_tag = safe_tag(implode(',', $post_tag));
			$post_tagmd = safe_html(implode(',', $post_tagmd));        

		} else {
			$post_tag = safe_tag(implode(',', $tag));
			$post_tagmd = safe_html(implode(',', $tag));        
		}
		
		$post_date = date('Y-m-d-H-i-s', strtotime($dateTime));
		$post_title = safe_html($title);
		if (empty($url)) {
			$url = $title;
		}
		$post_tag = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($post_tag)));
		$post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
		$category = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($category)));
		$description = safe_html($description);
		
		$post_t =  explode(',', $post_tag);
		$pret_t = explode(',', $post_tagmd);
		$tags = tag_cloud(true);
		$timestamp = date('YmdHis');

		$combine = array_combine($pret_t, $post_t);
		$inter = array_intersect_key($tags, array_flip($post_t));
		$newtag = array();
		
		foreach ($combine as $tag => $v) {
			if (array_key_exists($v, $tags)) {
				foreach ($inter as $in => $i){
					if($v == $in) {
						if (strtolower($tag) == strtolower(tag_i18n($in))) {
							$newtag[$v]= $tag;
						} else {
							$newtag[$v.'-'. $timestamp]= $tag;
						}
					}
				}
			} else {
				$newtag[$v] = $tag;
			}            
		}

		$post_tag = implode(',', array_keys($newtag));

		$posts = get_blog_posts();
		foreach ($posts as $index => $v) {
			$arr = explode('_', $v['basename']);
			if (strtolower($arr[2]) === strtolower($post_url . '.md')) {
				$post_url = $post_url .'-'. $timestamp;
			} else {
				$post_url = $post_url;
			}
		}

		if ($description !== null) {
			if (!empty($description)) {        
				$post_description = "\n<!--d " . $description . " d-->";
			} else {
				$post_description = "\n<!--d " . get_description($content) . " d-->";
			}            
		} else {
			$post_description = "";
		}
		if ($tag !== null) {
			$tagmd = "\n<!--tag " . $post_tagmd . " tag-->";
		} else {
			$tagmd = "";
		}
		if ($media!== null) {
			$post_media = "\n<!--" .$type. " " . preg_replace('/\s\s+/', ' ', strip_tags($media)) . " " .$type. "-->";
		} else {
			$post_media = "";
		}
		$post_content = "<!--t " . $post_title . " t-->" . $post_description . $tagmd . $post_media . "\n\n" . $content;

		if (!empty($post_title) && !empty($post_tag) && !empty($post_url) && !empty($post_content)) {

			$filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';

			$dir = 'content/' . $user . '/blog/' . $category. '/draft/';

			if (is_dir($dir)) {
				file_put_contents($dir . $filename, print_r($post_content, true), LOCK_EX);
			} else {
				mkdir($dir, 0775, true);
				file_put_contents($dir . $filename, print_r($post_content, true), LOCK_EX);
			}

			save_tag_i18n($post_tag, $post_tagmd);

			rebuilt_cache('all');
			return "Auto Saved";
		}
	}

	$title = $_POST['title'];
	$url = $_POST['url'];
	$content = $_POST['content'];
	$description = $_POST['description'];
	$draft = 'true';	
	$posttype = $_POST['posttype'];
	
	if (!empty($content)) {
		if ($posttype == 'is_page') {
			$response = auto_save_page($title, $url, $content, $draft, $description);
		} else {
			$user = $_SESSION[site_url()]['user'];
			$tag = $_POST['tag'];
			$category = $_POST['category'];
			$dateTime = $_POST['dateTime'];
			if ($posttype == 'is_image') {
				$type = 'image';
				$media = $_POST['pimage'];
			} elseif ($posttype == 'is_video') {
				$type = 'video';
				$media = $_POST['pvideo'];
			} elseif ($posttype == 'is_link') {
				$type = 'link';
				$media = $_POST['plink'];
			} elseif ($posttype == 'is_quote') {
				$type = 'quote';
				$media = $_POST['pquote'];
			} elseif ($posttype == 'is_audio') {
				$type = 'audio';
				$media = $_POST['paudio'];
			} elseif ($posttype == 'is_post') {
				$type = 'post';
				$media = null;
			}
			$response = auto_save_post($title, $tag, $url, $content, $user, $draft, $category, $type, $description, $media, $dateTime);
		}
	} else {
		$response = "No content to save.";
	}
	echo $response;
} else {
    $login = site_url() . 'login';
    header("location: $login");
}
?>