<?php if (!empty($breadcrumb)):?><div class="breadcrumb"><?php echo $breadcrumb ?></div><?php endif;?>
<div class="profile" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="Person">
	<h1 class="title-post" itemprop="name"><?php echo $name ?></h1>
	<div class="bio" itemprop="description"><?php echo $bio ?></div>
</div>
<h2 class="post-index">Posts by this author</h2>
<ul class="post-list">
	<?php $i = 0; $len = count($posts);?>
	<?php foreach($posts as $p):?>
		<?php 
			if ($i == 0) {
				$class = 'first';
			} 
			elseif ($i == $len - 1) {
				$class = 'last';
			}
			else {
				$class = '';
			}
			$i++;		
		?>
	<li>
		<span><a href="<?php echo $p->url?>"><?php echo $p->title ?></a></span> on <span><?php echo date('d F Y', $p->date)?></span> - Posted in <span><?php echo $p->tag ?></span>
	</li>
	<?php endforeach;?>
</ul>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])):?>
	<div class="pager">
		<?php if (!empty($pagination['prev'])):?>
			<span><a href="?page=<?php echo $page-1?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
		<?php endif;?>
		<?php if (!empty($pagination['next'])):?>
			<span><a href="?page=<?php echo $page+1?>" class="pagination-arrow older" rel="next">Older</a></span>
		<?php endif;?>
	</div>
<?php endif;?>