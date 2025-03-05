<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="type-post hentry section">
	<header class="entry-header">
		<h1 class="entry-title"><?php echo i18n('Categories');?></h1>
	</header>
	<div class="entry-content section-inner">
	<?php foreach ($categories as $category): ?>
		<div class="category-item post type-post">
			<?php if ($category->count > 0): ?>
			<h2><a href="<?php echo $category->url; ?>"><?php echo $category->title; ?></a></h2>
			<?php else: ?>
			<h2><?php echo $category->title; ?></h2>
			<?php endif; ?>
			<p><?php echo $category->description; ?></p>
		</div>
	<?php endforeach; ?>
	</div>
</article>