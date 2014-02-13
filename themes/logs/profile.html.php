<?php if (!empty($breadcrumb)):?><div class="breadcrumb"><?php echo $breadcrumb ?></div><?php endif;?>
<div class="profile-wrapper" itemprop="accountablePerson" itemscope="itemscope">
	<div class="profile" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="Person">
		<h1 class="title-post" itemprop="name"><?php echo $name ?></h1>
		<div class="bio" itemprop="description"><?php echo $bio ?></div>
	</div>
</div>
<h2 class="post-index">Posts by this author</h2>
<?php if(!empty($posts)) {?>
<ul class="post-list">
	<?php $i = 0; $len = count($posts);?>
	<?php foreach($posts as $p):?>
		<?php 
			if ($i == 0) {
				$class = 'item first';
			} 
			elseif ($i == $len - 1) {
				$class = 'item last';
			}
			else {
				$class = 'item';
			}
			$i++;		
		?>
	<li class="<?php echo $class;?>">
		<span><a href="<?php echo $p->url?>"><?php echo $p->title ?></a></span> on <span><?php echo date('d F Y', $p->date)?></span> - Posted in <span><?php echo $p->tag ?></span>
	</li>
	<?php endforeach;?>
</ul>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])):?>
	<div class="pager">
		<?php if (!empty($pagination['prev'])):?>
			<span class="newer" >&laquo; <a href="?page=<?php echo $page-1?>" rel="prev">Newer</a></span>
		<?php endif;?>
		<?php if (!empty($pagination['next'])):?>
			<span class="older" ><a href="?page=<?php echo $page+1?>" rel="next">Older</a> &raquo;</span>
		<?php endif;?>
	</div>
<?php endif;?>
<?php } else { echo 'No posts found!'; }?>