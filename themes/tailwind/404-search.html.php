<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="divide-y divide-gray-200 dark:divide-gray-700">
	<div class="space-y-2 pb-8 pt-6 md:space-y-5">
		<h1 class="text-3xl font-extrabold leading-9 tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl sm:leading-10 md:text-6xl md:leading-14"><?php echo i18n('search_results_not_found');?></h1>
	</div>
	<div class="items-start space-y-2 xl:grid xl:gap-x-8 xl:space-y-0">
		<div class="prose max-w-none pb-8 pt-8 dark:prose-invert">
		    <p><a style="text-decoration:none;" aria-label="Back to homepage" href="<?php echo site_url();?>">← <?php echo i18n('back_to');?> <?php echo i18n('homepage');?></a></p>
		</div>
		
		<div class="relative max-w-lg">
			<label>
				<span class="sr-only">Search articles</span>
				<form><input aria-label="Search articles" name="search" placeholder="<?php echo i18n('type_to_search');?>" class="block w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-900 dark:bg-gray-800 dark:text-gray-100" type="text"></form>
			</label>
			<svg class="absolute right-3 top-3 h-5 w-5 text-gray-400 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
		</div>
	</div>
</div>