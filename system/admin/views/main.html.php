<section class="row">
<h1>Your recent posts</h1>
<header class="row"><p class="col">Title</p><p class="col">Published at</p></header>
	<?php 
	$posts=get_my_posts();
	foreach ($posts as $post){
		echo '<article class="row">'
			 . '<p class="col">' . $post->title 
			    . ' (<a href="' . $post->url . '/edit?destination=admin">Edit</a> , '
				. '<a href="' . $post->url . '/delete?destination=admin">Delete</a> )'
				. '</p>'
			 . '<p class="col">' . date('d F Y', $post->date) . '</p>'             
			 . '</article>';
	}
	?>
</section>

<section class="row">
<h2>Static pages</h2>
<?php get_recent_pages(); ?>
</section>