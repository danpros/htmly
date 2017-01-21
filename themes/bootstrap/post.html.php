<?php if (!empty($p->image)) { ?>
    <div class="featured featured-image">
        <a href="<?php echo $p->url ?>"><img itemprop="image" src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
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
    <h1 class="title" itemprop="headline"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title;?> <i class="fa fa-external-link"></i></a></h1>
<?php } else { ?>
    <h1 class="title" itemprop="headline"><?php echo $p->title;?></h1>
<?php } ?>
<p class="lead">
    by <a href="<?php echo $p->authorUrl;?>"><?php echo $p->author;?></a>
</p>
<p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo date('d F Y', $p->date) ?> <?php if (login()) { echo editButton($p); } ?></p>
<hr>
<img class="img-responsive" src="http://placehold.it/900x300" alt="">
<hr>
<p>
    <?php echo $p->body; ?>
</p>
<hr/>
<div style="margin-top:30px;position:relative;">
    <span class="tags"><i class="glyphicon glyphicon-tags" style="margin-right: 5px;"></i>  This post tagged with : <?php echo $p->tag;?></span> 
    <span class="share pull-right">
        <a target="    " class="first" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>"><i class="fa fa-facebook"></i></a> 
        <a target="    " href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>"><i class="fa fa-twitter"></i></a> 
        <a target="    " class="last" href="https://plus.google.com/share?url=<?php echo $p->url ?>"><i class="fa fa-google-plus"></i></a> 
    </span>
    <div style="clear:both;"></div>
</div>
<div style="margin-top:30px;position:relative;">
    <hr>
    <ul class="pager">
        <?php if (!empty($prev)): ?>
            <li class="previous">
                <a href="<?php echo($prev['url']); ?>" rel="prev"><i class="glyphicon glyphicon-chevron-left"></i> Previous Post</a>
            </li>
        <?php endif; ?>
        <?php if (!empty($next)): ?>
            <li class="next">
                <a href="<?php echo($next['url']); ?>" rel="next"><i class="glyphicon glyphicon-chevron-right"></i> Next Post</a>
            </li>
        <?php endif; ?>
    </ul>
    <div style="clear:both;"></div>
</div>
<div class="well">
    <h4>Leave a Comment:</h4>
    <div id="disqus_thread"></div>
</div>
<?php
    echo disqus();
?>