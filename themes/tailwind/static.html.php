<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="divide-y divide-gray-200 dark:divide-gray-700">
	<div class="space-y-2 pb-8 pt-6 md:space-y-5">
		<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-6xl md:leading-14"><?php echo $static->title;?></h1>
		<?php if (authorized($p)):?><span><a href="<?php echo $p->url;?>/edit?destination=post"><?php echo i18n('edit');?></a></span><?php endif;?>
	</div>
	<div class="items-start space-y-2">
		<div class="prose max-w-none pb-8 pt-8 dark:prose-invert">
			<?php echo $static->body;?>
		</div>
	</div>
</div>