<h1 class="title page-header"><?php echo $p->title;?> <?php if (login()) { echo editButton($p); } ?></h1>
<div class="desc text-left" itemprop="articleBody">
    <?php echo $p->body; ?>
</div><!--//desc-->