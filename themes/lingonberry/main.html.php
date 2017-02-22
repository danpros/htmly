<?php $posts = recent_posts(true);?>
<?php if(count($posts) > 0) : ?>
	<div class="content section-inner">
		<?php if(login()) : ?> 
			<div class="addPost"><a href="http://localhost/agitatorfilm/admin/content"><i class="icon-pencil"></i> add a new post</a></div>
		<?php endif; ?>
		
		<div class="posts">		
			<?php foreach ($posts as $post):?>
			
			<?php // set post icon class 
				if($post->type == 'post'){ $icon = 'icon-doc-text'; $format = 'standard'; }
				if($post->type == 'image'){ $icon = 'icon-camera'; $format = 'gallery'; }
				if($post->type == 'video'){ $icon = 'icon-videocam'; $format = 'video'; }
				if($post->type == 'audio'){ $icon = 'icon-music'; $format = 'audio';} 
				if($post->type == 'quote'){ $icon = 'icon-chat-empty'; $format = 'quote'; }
				if($post->type == 'link'){ $icon = 'icon-link'; $format = 'link';}		
			?>
			<div class="post format-<?php echo $format; ?>">
				<div class="post-bubbles">
					<a href="<?php echo $post->url; ?>" class="format-bubble">
						<i class="<?php echo $icon; ?>"></i>
					</a>
					<?php if (login()): ?><a href="<?php echo $post->url; ?>/edit?destination=post" title="edit post" class="sticky-bubble"><i class="icon-pencil"></i></a><?php endif; ?>
				</div>

				<div class="content-inner">
				
						<?php if($post->type == "post"): ?>
							<div class="post-header">
								<h2 class="post-title"><a href="<?php echo $post->url; ?>" rel="bookmark"><?php echo $post->title; ?></a></h2>
								
								<div class="post-meta">
									<span class="post-date" itemprop="datePublished"><?php echo date('d F Y', $post->date) ?></span>
									<span class="date-sep"> / </span>
									<span class="post-author" itemprop="author"><a href="<?php echo $post->authorUrl;?>"><?php echo $post->author;?></a></span>
									<span class="date-sep"> / </span>
									<span itemprop="articleSection"><?php echo $post->category;?></span>
								</div>
								
							</div>
							<div class="post-content">
								<p><?php echo shorten($post->body, 300); ?></p>
								<p><a class="read-more" href="<?php echo $post->url; ?>">Read more</a></p>
							</div>
							<div class="clear"></div>
						
						<?php elseif($post->type == "image"): ?>
							<div class="post-header">
								<div class="featured-media">
									<?php echo $post->image; ?>	
								</div>
								<h2 class="post-title"><a href="<?php echo $post->url; ?>" rel="bookmark"><?php echo $post->title; ?></a></h2>
								<div class="post-meta">
									<span class="post-date" itemprop="datePublished"><?php echo date('d F Y', $post->date) ?></span>
									<span class="date-sep"> / </span>
									<span class="post-author" itemprop="author"><a href="<?php echo $post->authorUrl;?>"><?php echo $post->author;?></a></span>
									<span class="date-sep"> / </span>
									<span itemprop="articleSection"><?php echo $post->category;?></span>
								</div>
							</div>
							<div class="post-content">
								<?php echo $post->body; ?>
								<p><a class="read-more" href="<?php echo $post->url; ?>">Read more</a></p>
							</div>
							<div class="clear"></div>
						<?php elseif($post->type == "video"): ?>
							<div class="post-header">
								<div class="featured-media">
									<?php if($post->video[1] == 'youtube') : ?>
										<div class="youtube" data-embed="<?php echo $post->video[2]; ?>"> 
											<div class="play-button"></div> 
										 </div>
<!--										<iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $post->video[2]; ?>" frameborder="0" allowfullscreen></iframe> -->
									<?php elseif($post->video[1] == 'vimeo') : ?>
										<div class="vimeo" data-embed="<?php echo $post->video[2]; ?>"> 
											<div class="play-button"></div> 
										 </div>
<!--										<iframe width="100%" height="315px" class="embed-responsive-item" src="https://player.vimeo.com/video/<?php echo $post->video[2]; ?>" frameborder="0" allowfullscreen></iframe> -->
									<?php endif; ?>
								</div>
								<h2 class="post-title"><a href="<?php echo $post->url; ?>" rel="bookmark"><?php echo $post->title; ?></a></h2>
								<div class="post-meta">
									<span class="post-date" itemprop="datePublished"><?php echo date('d F Y', $post->date) ?></span>
									<span class="date-sep"> / </span>
									<span class="post-author" itemprop="author"><a href="<?php echo $post->authorUrl;?>"><?php echo $post->author;?></a></span>
									<span class="date-sep"> / </span>
									<span itemprop="articleSection"><?php echo $post->category;?></span>
								</div>
							</div>
							<div class="post-content">
								<?php echo $post->body; ?>
							</div>
							<div class="clear"></div>							
						<?php elseif($post->type == "audio"): ?>
							<div class="post-header">
								<div class="featured-media">
									<span class="embed-soundcloud"><iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe></span>	
								</div>
								<h2 class="post-title"><a href="<?php echo $post->url; ?>" rel="bookmark"><?php echo $post->title; ?></a></h2>
								<div class="post-meta">
									<span class="post-date" itemprop="datePublished"><?php echo date('d F Y', $post->date) ?></span>
									<span class="date-sep"> / </span>
									<span class="post-author" itemprop="author"><a href="<?php echo $post->authorUrl;?>"><?php echo $post->author;?></a></span>
									<span class="date-sep"> / </span>
									<span itemprop="articleSection"><?php echo $post->category;?></span>
								</div>
							</div> <!-- /post-header -->
							<div class="post-content">
								<?php echo $post->body; ?>
							</div>
							<div class="clear"></div>							
						<?php elseif($post->type == "quote"): ?>
							<div class="post-content">
								<blockquote><p><?php echo $post->quote; ?></p></blockquote>
								<?php echo $post->body; ?>
							</div>
							<div class="clear"></div>							
						<?php elseif($post->type == "link"): ?>
							<div class="post-content">
								<p><?php echo $post->link; ?></p>
								<?php echo $post->body; ?>
							</div>
							<div class="clear"></div>
						<?php endif; ?>
											
				</div> <!-- /content-inner -->				
		
				<div class="clear"></div>
			
			</div> <!-- /post -->
			<?php endforeach; ?>
		</div><!-- /posts -->
	</div>
<?php endif; ?>
		
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<div class="navigation pagination">
    <div class="nav-links">
        <?php if (!empty($pagination['prev'])): ?>
            <a class="prev page-numbers" href="?page=<?php echo $page - 1 ?>">«</a>
        <?php endif; ?>
        <span class="page-numbers"><?php echo $pagination['pagenum'];?></span>
        <?php if (!empty($pagination['next'])): ?>
            <a class="next page-numbers" href="?page=<?php echo $page + 1 ?>">»</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>