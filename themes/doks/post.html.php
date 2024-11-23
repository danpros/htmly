<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article>
    <div class="blog-header">

        <?php if(!empty($post->link)) { ?>
            <h1>
                <a href="<?php echo $post->link;?>" target="_blank"><?php echo $post->title;?> 
                    <svg xmlns="http://www.w3.org/2000/svg" height="28" width="28" fill="currentColor" class="icon icon-tabler bi bi-link" viewBox="0 0 16 16"><path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/><path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z"/></svg>
                </a>
            </h1>
        <?php } else {?>
            <h1><?php echo $post->title;?></h1>
        <?php } ?>

        <p>
            <small>
                <?php echo i18n('posted_on');?> <?php echo format_date($post->date);?> 
                <?php echo i18n('by');?> 
                <a class="stretched-link position-relative" href="<?php echo $post->authorUrl;?>"><?php echo $post->authorName;?></a> 
                <span class="mx-2">—</span> 
                <strong>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentcolor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1018 0A9 9 0 003 12"></path><path d="M12 7v5l3 3"></path></svg> 
                    <?php echo $post->readTime;?> min
                </strong> 
                <?php if (authorized($post)) { echo ' <span class="mx-2">—</span> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828.0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> <span class="edit-post"><a href="'. $post->url .'/edit?destination=post">' . i18n('Edit') . '</a></span>'; } ?>
            </small>
        </p>

    </div>

    <div class="content-media">

        <?php if(!empty($post->image)):?>
            <img alt="<?php echo $post->title;?>" src="<?php echo $post->image;?>"/>
        <?php endif;?>
        <?php if(!empty($post->video)):?>
            <iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo get_video_id($post->video); ?>" frameborder="0" allowfullscreen></iframe>
        <?php endif;?>
        <?php if(!empty($post->audio)):?>
            <iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $post->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
        <?php endif;?>
        <?php if(!empty($post->quote)):?>
            <div class="quote"><blockquote class="quote"><?php echo $post->quote ?></blockquote></div>
        <?php endif;?>

    </div>

    <div class="content-body post-<?php echo $p->date;?>" id="content">

        <?php echo $post->body;?>
        <p class="post-footer">
            <small>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="icon bi bi-folder" viewBox="0 0 18 18"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z"/></svg>
                <span class="cat-meta"><?php echo $post->category;?></span>
                
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-tag" viewBox="0 0 18 18"><path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0"/><path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z"/></svg> 
                <span class="tag-meta"><?php echo $post->tag;?></span>
            </small>
        </p>

    </div>

    <div class="related docs-navigation">
        <small>
            <strong><?php echo i18n("Related_posts");?></strong>
            <?php echo get_related($post->related);?>
        </small>
    </div>

</article>

<?php if (disqus()): ?>
    <?php echo disqus($post->title, $post->url) ?>
<?php endif; ?>

<?php if (facebook() || disqus()): ?>
<div class="comments-area" id="comments">
    <?php if (facebook()): ?>
        <div class="fb-comments" data-href="<?php echo $post->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
    <?php endif; ?>
    <?php if (disqus()): ?>
        <div id="disqus_thread"></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($next) || !empty($prev)): ?>
<div class="docs-navigation d-flex justify-content-between">

    <?php if (!empty($next)): ?>
    <a href="<?php echo($next['url']); ?>">
        <div class="card my-1">
            <div class="card-body py-2">
            ← <?php echo i18n('next_post');?>
            </div>
        </div>
    </a>
    <?php endif;?>

    <?php if (!empty($prev)): ?>
    <a class="ms-auto" href="<?php echo($prev['url']); ?>">
        <div class="card my-1">
            <div class="card-body py-2">
                <?php echo i18n('prev_post');?> →
            </div>
        </div>
    </a>
    <?php endif;?>
</div>
<?php endif;?>