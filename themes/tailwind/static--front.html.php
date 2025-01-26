<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="space-y-2 pb-6 pt-6 md:space-y-5">
	<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-6xl md:leading-14"><?php echo $static->title;?></h1>
	<?php if (authorized($p)):?><span><a href="<?php echo $p->url;?>/edit?destination=post"><?php echo i18n('edit');?></a></span><?php endif;?>
	<div class="prose max-w-none text-lg leading-7 pb-6 text-gray-500 dark:text-gray-400 dark:prose-invert">
		<?php echo $static->body;?>
	</div>
	<div class="space-y-2 md:space-y-5">
		<h2 class="text-2xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-4xl md:leading-14"><?php echo i18n('recent_posts');?></h2>
	</div>
</div>
<?php $posts = recent_posts(true);?>
<?php $teaserType = config('teaser.type'); $readMore = config('read.more');?>
<ul class="divide-y divide-gray-200 dark:divide-gray-700">
	<?php foreach ($posts as $p):?>
	<?php $img = get_image($p->body);?>
	<li class="py-12">
		<article>
		<div class="space-y-2 xl:grid xl:grid-cols-4 xl:items-baseline xl:space-y-0">
			<dl>
				<dt class="sr-only"><?php echo i18n('posted_on');?></dt>
				<dd class="text-base font-medium leading-6 text-gray-500 dark:text-gray-400"><time><?php echo format_date($p->date);?></time>
				<?php if (authorized($p)):?> • <span><a href="<?php echo $p->url;?>/edit?destination=post"><?php echo i18n('edit');?></a></span><?php endif;?>
				</dd>
				
				<?php if ($teaserType === 'trimmed') :?>
				<dt class="sr-only"><?php echo i18n('featured_image');?></dt>
				<dd class="pt-4 pr-6">
						<?php if (!empty($p->image)) {?>
						<a class="thumbnail" href="<?php echo $p->url;?>"><img src="<?php echo $p->image;?>" width="100%" alt="<?php echo $p->title;?>"></a>
						<?php } elseif (!empty($p->video)) {?>
						<a class="thumbnail" href="<?php echo $p->url;?>"><img src="//img.youtube.com/vi/<?php echo get_video_id($p->video);?>/sddefault.jpg" width="100%" alt="<?php echo $p->title;?>">
							<span class="thumb-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-play" viewBox="0 0 16 16"><path d="M10.804 8 5 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C4.713 12.69 4 12.345 4 11.692V4.308c0-.653.713-.998 1.233-.696z"/></svg>
							</span>
						</a>
						<?php } elseif (!empty($p->audio)) {?>
						<a class="thumbnail" href="<?php echo $p->url;?>">
							<img src="<?php echo theme_path();?>img/soundcloud.jpg" width="100%" alt="<?php echo $p->title;?>">
							<span class="thumb-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-volume-up" viewBox="0 0 16 16"><path d="M11.536 14.01A8.47 8.47 0 0 0 14.026 8a8.47 8.47 0 0 0-2.49-6.01l-.708.707A7.48 7.48 0 0 1 13.025 8c0 2.071-.84 3.946-2.197 5.303z"/><path d="M10.121 12.596A6.48 6.48 0 0 0 12.025 8a6.48 6.48 0 0 0-1.904-4.596l-.707.707A5.48 5.48 0 0 1 11.025 8a5.48 5.48 0 0 1-1.61 3.89z"/><path d="M10.025 8a4.5 4.5 0 0 1-1.318 3.182L8 10.475A3.5 3.5 0 0 0 9.025 8c0-.966-.392-1.841-1.025-2.475l.707-.707A4.5 4.5 0 0 1 10.025 8M7 4a.5.5 0 0 0-.812-.39L3.825 5.5H1.5A.5.5 0 0 0 1 6v4a.5.5 0 0 0 .5.5h2.325l2.363 1.89A.5.5 0 0 0 7 12zM4.312 6.39 6 5.04v5.92L4.312 9.61A.5.5 0 0 0 4 9.5H2v-3h2a.5.5 0 0 0 .312-.11"/></svg>
							</span>
						</a>
						<?php } elseif (!empty($img)) {?>
						<a class="thumbnail" href="<?php echo $p->url;?>"><img src="<?php echo $img;?>" width="100%" alt="<?php echo $p->title;?>"></a>
						<?php } ?>
				</dd>
				<?php endif;?>
				
			</dl>
			<div class="space-y-5 xl:col-span-3">
				<div class="space-y-6">
					<div>
						<?php if (!empty($p->link)) {?>
						<h2 class="text-2xl font-bold leading-8 tracking-tight">
							<a class="text-gray-900 dark:text-gray-100" href="<?php echo $p->link;?>" target="_blank"><?php echo $p->title;?>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" style="display:inline-block;margin-left: 3px;" class=" bi bi-link-45deg" viewBox="0 0 16 16">
								  <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
								  <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
								</svg>
							</a>
						</h2>					
						<?php } else { ?>
						<h2 class="text-2xl font-bold leading-8 tracking-tight">
							<a class="text-gray-900 dark:text-gray-100" href="<?php echo $p->url;?>"><?php echo $p->title;?></a>
						</h2>						
						<?php } ?>
						<div class="flex flex-wrap">
							<span class="mr-3 text-sm font-medium uppercase text-primary-500"><span class="tags"><?php echo $p->tag;?></span></span>
						</div>
					</div>
					<div class="prose max-w-none text-gray-500 dark:text-gray-400 dark:prose-invert">
						<?php if ($teaserType !== 'trimmed') :?>
							<?php if (!empty($p->image)) {?>
							<img src="<?php echo $p->image;?>" width="100%" alt="<?php echo $p->title;?>">
							<?php } elseif (!empty($p->video)) {?>
							<div class="relative" style="padding-top: 56.25%">
							<iframe class="absolute inset-0 w-full h-full" src="https://www.youtube.com/embed/<?php echo get_video_id($p->video); ?>" frameborder="0" allowfullscreen></iframe>
							</div>
							<?php } elseif (!empty($p->audio)) {?>
							<iframe width="100%" height="100%" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
							<?php } elseif (!empty($p->quote)) {?>
							<blockquote class="text-2xl italic font-bold leading-8 tracking-tight">
								<?php echo $p->quote;?>
							</blockquote>
							<?php } ?>
						<?php endif;?>

						<?php if (!empty($p->quote) && $teaserType === 'trimmed'):?>
							<blockquote class="text-2xl italic font-bold leading-8 tracking-tight" style="margin: 1.6em 0;">
								<?php echo $p->quote;?>
							</blockquote>
						<?php endif; ?>
						
						<?php echo get_teaser($p->body, $p->url);?>
					</div>
				</div>
				<?php if ($teaserType === 'trimmed'):?>
				<div class="text-base font-medium leading-6">
					<a class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400" aria-label="<?php echo $p->title;?>" href="<?php echo $p->url;?>"><?php echo $readMore;?></a>
				</div>
				<?php endif;?>
			</div>
		</div>
		</article>
	</li>
	<?php endforeach;?>
</ul>
<?php if (config('blog.enable') === 'true'):?>
<div style="border:none;text-align:right;"><a class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400" aria-label="<?php echo i18n('all_blog_posts');?>" href="<?php echo site_url() . blog_path();?>"><?php echo i18n('all_blog_posts');?> →</a></div>
<?php endif;?>