<?php if (!empty($breadcrumb)):?><div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php echo $breadcrumb ?></div><?php endif;?>
<div class="post" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="main">
		<?php if ($type == 'blogpost'):?>
			<a name="more"></a>
		<?php endif;?>
		<h1 class="title-post" itemprop="name"><?php echo $p->title ?></h1>
		<?php if ($type == 'blogpost'):?>
			<div class="date"><span itemprop="datePublished"><a href="<?php echo $p->archive ?>" title="Show all posts made on this day"><?php echo date('d F Y', $p->date)?></a></span> - Posted in <span itemprop="articleSection"><a href="<?php echo $p->tagurl ?>"><?php echo $p->tag ?></a></span> by <span itemprop="author"><a rel="author" href="<?php echo $p->authorurl ?>"><?php echo $p->author ?></a></span> - <span><a href="<?php echo $p->url ?>" rel="permalink">Permalink</a></span></div>
		<?php endif;?>
		<div itemprop="articleBody">
			<?php echo $p->body; ?>
		</div>
	</div>
    <div class="comments border">
		<?php if ($type == 'blogpost'):?>
			<?php if (disqus(null, null) == true):?>
				<div id="disqus_thread"></div>
			<?php endif;?>
		<?php endif;?>
	</div>
	<?php if ($type == 'blogpost'):?>
		<div class="postnav">
			<?php if (!empty($next)):?>
				<span><a href="<?php echo ($next['url']);?>" class="pagination-arrow newer" rel="next"><?php echo ($next['title']);?></a></span>
			<?php endif;?>

			<?php if (!empty($prev)):?>
				<span><a href="<?php echo ($prev['url']); ?>" class="pagination-arrow older" rel="prev"><?php echo ($prev['title']); ?></a></span>
			<?php endif;?>
		</div>
	<?php endif;?>
	<?php if ($type == 'blogpost'):?>
		<?php if (disqus(null, null) == true):?>
			<?php echo disqus($p->title, $p->url) ?>
		<?php endif;?>
	<?php endif;?>
</div>
