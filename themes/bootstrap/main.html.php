<?php $i = 0; $len = count($posts); ?>
<?php foreach ($posts as $p): ?>
    <?php if ($i == 0) {
        $class = 'post first';
    } elseif ($i == $len - 1) {
        $class = 'post last';
    } else {
        $class = 'post';
    }
    $i++; ?>

<?php if (!empty($p->image)) { ?>
    <div class="featured featured-image">
        <a href="<?php echo $p->url ?>"><img  itemprop="image" src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
    </div>
<?php } ?>
<?php if (!empty($p->video)) { ?>
    <div class="featured featured-video embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $p->video; ?>" frameborder="0" allowfullscreen></iframe>
    </div>
<?php } ?>
<?php if (!empty($p->audio)) { ?>
    <div class="featured featured-audio embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
    </div>
<?php } ?>
<?php if (!empty($p->quote)) { ?>
    <div class="featured featured-quote">
        <blockquote class="quote"><i class="fa fa-quote-left"></i> <?php echo $p->quote ?> <i class="fa fa-quote-right"></i></blockquote>
    </div>
<?php } ?>

<?php if (!empty($p->link)) { ?>
    <h2 class="title" itemprop="headline"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title;?> <i class="fa fa-external-link"></i></a></h2>
<?php } else {?>
    <h2 class="title" itemprop="headline"><a href="<?php echo $p->url;?>"><?php echo $p->title;?></a></h2>
<?php } ?>

<p class="lead">
    by <a href="<?php echo $p->authorUrl;?>"><?php echo $p->author;?></a>
</p>

<p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo date('d F Y', $p->date) ?></p>
<p>
    <img class="img-responsive" src="http://placehold.it/900x300" alt="">
    <?php echo get_teaser($p->body, $p->url) ?>
</p>
<?php if (config('teaser.type') === 'trimmed'):?>
    <a class="btn btn-primary" href="<?php echo $p->url;?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
<?php endif;?>
 <span class="share pull-right">
     <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>"><i class="fa fa-facebook"></i></a> 
     <a target="_blank" href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>"><i class="fa fa-twitter"></i></a> 
     <a target="_blank" class="last" href="https://plus.google.com/share?url=<?php echo $p->url ?>"><i class="fa fa-google-plus"></i></a> 
 </span>
<div style="clear:both;"></div>

<?php if ($class !== 'post last'): ?>
    <hr/>
<?php endif; ?>

<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pagination"><?php echo $pagination['html'];?></div>
<?php endif; ?>
<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>