<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<section class="mx-auto max-w-3xl sm:px-6 xl:max-w-5xl xl:px-0">
    <article>
        <div class="xl:divide-y xl:divide-gray-200 xl:dark:divide-gray-700">
            <header class="pt-6 xl:pb-6">
                <div class="space-y-1 text-center">
                    <dl class="space-y-10">
                        <div>
                            <dt class="sr-only"><?php echo i18n('posted_on');?></dt>
                            <dd class="text-base font-medium leading-6 text-gray-500 dark:text-gray-400"><time><?php echo format_date($p->date);?></time> • <span class="text-primary-500"><?php echo $p->category;?></span>
							<?php if (authorized($p)):?> • <span><a href="<?php echo $p->url;?>/edit?destination=post"><?php echo i18n('edit');?></a></span><?php endif;?>
							</dd>
                        </div>
                    </dl>
                    <div>
					<?php if (!empty($p->link)) {?>
					<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-5xl md:leading-14">
						<a href="<?php echo $p->link;?>" target="_blank"><?php echo $p->title;?>
						<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" style="display:inline-block;margin-left: 3px;" class=" bi bi-link-45deg" viewBox="0 0 16 16">
						  <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
						  <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
						</svg>
						</a>
					</h1>
					<?php } else { ?>
					<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-5xl md:leading-14"><?php echo $p->title;?></h1>					
					<?php } ?>
					</div>
                </div>
            </header>
            <div class="grid-rows-[auto_1fr] divide-y divide-gray-200 pb-8 dark:divide-gray-700 xl:grid xl:grid-cols-4 xl:gap-x-6 xl:divide-y-0">
                <dl class="pb-10 pt-6 xl:border-b xl:border-gray-200 xl:pt-11 xl:dark:border-gray-700">
                    <dt class="sr-only"><?php echo i18n('author');?></dt>
                    <dd>
                        <ul class="flex flex-wrap justify-center gap-4 sm:space-x-12 xl:block xl:space-x-0 xl:space-y-8">
                            <li class="flex items-center space-x-2">
                                <img src="<?php echo $p->authorAvatar;?>" width="40" height="40"/>
                                <dl class="whitespace-nowrap text-sm font-medium leading-5">
                                    <dt class="sr-only"><?php echo i18n('user');?></dt>
                                    <dd class="text-gray-900 dark:text-gray-100"><?php echo $p->authorName;?></dd>
                                    <dt class="sr-only"><?php echo i18n('post_by_author');?></dt>
                                    <dd><a class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400" href="<?php echo $p->authorUrl;?>"><?php echo i18n('post_by_author');?></a></dd>
                                </dl>
                            </li>
                        </ul>
                    </dd>
                </dl>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 xl:col-span-3 xl:row-span-2 xl:pb-0">
                    <div class="prose max-w-none pb-8 pt-10 dark:prose-invert">
					
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
					
						<?php echo $p->body;?>
                    </div>
                    <div class="prose max-w-none pb-6 pt-6 text-sm text-gray-700 dark:text-gray-300 related-posts">
						<h2 class="text-xl text-gray-700 dark:text-gray-300"><?php echo i18n('related_posts');?></h2>
						<?php echo get_related($p->related);?>
                    </div>
					<?php if (facebook() || disqus()): ?>
					<div class="pb-6 pt-6 text-center text-gray-700 dark:text-gray-300" id="comment">
						<?php if (facebook()): ?>
							<div class="fb-comments" data-href="<?php echo $p->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
						<?php endif; ?>
						<?php if (disqus()): ?>
							<?php echo disqus($p->title, $p->url) ?>
							<div id="disqus_thread"></div>
						<?php endif; ?>
					</div>
					<?php endif;?>
                </div>
                <footer>
                    <div class="divide-gray-200 text-sm font-medium leading-5 dark:divide-gray-700 xl:col-start-1 xl:row-start-2 xl:divide-y">
                        <div class="py-4 xl:py-8">
                            <h2 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400"><?php echo i18n('tags');?></h2>
                            <div class="flex flex-wrap">
								<span class="mr-3 text-sm font-medium uppercase text-primary-500" ><span class="tags"><?php echo $p->tag;?></span></span>
                            </div>
                        </div>
                        <div class="flex justify-between py-4 xl:block xl:space-y-8 xl:py-8">
							<?php if (!empty($prev)): ?>
                            <div>
                                <h2 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400"><?php echo i18n('prev_post');?></h2>
                                <div class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400"><a class="break-words" href="<?php echo($prev['url']); ?>"><?php echo($prev['title']); ?></a></div>
                            </div>
							<?php endif;?>
							<?php if (!empty($next)): ?>
                            <div>
                                <h2 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400"><?php echo i18n('next_post');?></h2>
                                <div class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400"><a class="break-words" href="<?php echo($next['url']); ?>"><?php echo($next['title']); ?></a></div>
                            </div>
							<?php endif;?>
                        </div>
                        <div class="py-4 xl:py-8">
                            <h2 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tag Cloud</h2>
                            <div class="flex flex-wrap">
								<span class="mr-3 text-sm font-medium uppercase text-primary-500"><span class="tags"><?php echo tag_cloud();?></span></span>
                            </div>
                        </div>		
                    </div>
					<?php if (config('blog.enable') === 'true') {?>
                    <div class="pt-4 xl:pt-8"><a class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400" aria-label="Back to the blog" href="<?php echo site_url() . blog_path();?>">← <?php echo i18n('back_to');?> <?php echo blog_string();?></a></div>
					<?php } else {?>
					<div class="pt-4 xl:pt-8"><a class="text-primary-500 hover:text-primary-600 dark:hover:text-primary-400" aria-label="Back to homepage" href="<?php echo site_url();?>">← <?php echo i18n('back_to');?> <?php echo i18n('homepage');?></a></div>
					<?php } ?>
                </footer>
            </div>
        </div>
    </article>
</section>
