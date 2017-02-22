<?php $post = $p; ?>
<div class="single single-post">
<div class="content section-inner">
	<div class="posts">				
		<?php // set format class and icon class 
			if($post->type == 'post'){ $icon = 'icon-doc-text'; $format = 'standard'; }
			if($post->type == 'image'){ $icon = 'icon-camera'; $format = 'gallery'; }
			if($post->type == 'video'){ $icon = 'icon-videocam'; $format = 'video'; }
			if($post->type == 'audio'){ $icon = 'icon-music'; $format = 'audio';}
			if($post->type == 'quote'){ $icon = 'icon-chat-empty'; $format = 'quote'; }
			if($post->type == 'link'){ $icon = 'icon-link'; $format = 'link';}		
		?>
		<div class="post format-<?php echo $format; ?>">

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
						<p><?php echo $post->body; ?></p>
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
					</div>
					<div class="clear"></div>
				<?php elseif($post->type == "video"): ?>
					<div class="post-header">
						<div class="featured-media">
							<?php if($post->video[1] == 'youtube') : ?>
								<iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $post->video[2]; ?>" frameborder="0" allowfullscreen></iframe>
							<?php elseif($post->video[1] == 'vimeo') : ?>
								<iframe width="100%" height="315px" class="embed-responsive-item" src="https://player.vimeo.com/video/<?php echo $post->video[2]; ?>" frameborder="0" allowfullscreen></iframe>
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
			</div>
		</div>
	</div>
</div>
</div>