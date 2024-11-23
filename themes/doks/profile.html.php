<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="row justify-content-center" style="padding-top: 4rem;">
    <div class="col-md-12 text-center">
        <h1 class="mt-0"><?php echo $author->name;?></h1>
    </div>
    <div class="col-md-12 text-center">
        <div class="lead"><?php echo $author->about;?></div>
    </div>
</div>
<?php $teaserType = config('teaser.type'); $readMore = config('read.more');?>
<?php if (!empty($posts)):?>
    <?php foreach ($posts as $post):?>
    <?php $img = get_image($post->body);?>
        <article>
            <div class="card-list">
                <div class="card">

                    <?php if (!empty($post->image)) {?>
                        <a href="<?php echo $post->url;?>"><img alt="<?php echo $post->title;?>" src="<?php echo $post->image;?>" width="100%"></a>
                    <?php } elseif (!empty($img) && $teaserType === 'trimmed' && empty($post->quote) && empty($post->video) && empty($post->audio)) {?>
                        <a href="<?php echo $post->url;?>"><img alt="<?php echo $post->title;?>" src="<?php echo $img;?>" width="100%"></a>
                    <?php } ?>
                    
                    <?php if(!empty($post->video)):?>
                        <iframe width="100%" height="315px" class="embed-responsive-item media-wrapper" src="https://www.youtube.com/embed/<?php echo get_video_id($post->video); ?>" frameborder="0" allowfullscreen></iframe>
                    <?php endif;?>
                    <?php if(!empty($post->audio)):?>
                        <iframe width="100%" height="200px" class="embed-responsive-item media-wrapper" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $post->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
                    <?php endif;?>

                    <?php if(!empty($post->quote)):?>
                        <div class="quote">
                            <blockquote class="quote"><?php echo $post->quote ?></blockquote>
                        </div>
                    <?php endif;?>

                    <div class="card-body">

                        <?php if(!empty($post->link)) { ?>
                            <h2>
                                <a class="text-body" href="<?php echo $post->link;?>" target="_blank"><?php echo $post->title;?> 
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>
                                </a>
                            </h2>
                        <?php } else {?>
                            <h2><a class="text-body" href="<?php echo $post->url;?>"><?php echo $post->title;?></a></h2>
                        <?php } ?>

                        <div class="content-body">
                            <?php echo get_teaser($post->body, $post->url); ?>
                            <?php if ($teaserType === 'trimmed'):?>[...] <a class="more-link" href="<?php echo $post->url; ?>"><?php echo $readMore; ?></a><?php endif;?>
                        </div>

                        <p>
                            <small>
                                <?php echo i18n('posted_on');?> <?php echo format_date($post->date);?> 
                                <?php echo i18n('by');?> 
                                <a class="position-relative" href="<?php echo $post->authorUrl;?>">
                                    <?php echo $post->authorName;?>
                                </a> 
                                <span class="mx-2">—</span> 
                                <strong>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentcolor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1018 0A9 9 0 003 12"></path><path d="M12 7v5l3 3"></path></svg> 
                                    <?php echo $post->readTime;?> min
                                </strong>
                                <?php if (authorized($post)) { echo ' <span class="mx-2">—</span> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828.0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> <span class="edit-post"><a href="'. $post->url .'/edit?destination=post">' . i18n('Edit') . '</a></span>'; } ?>
                            </small>
                        </p>

                    </div>
                </div>
            </div>
        </article>
    <?php endforeach;?>
<?php endif;?>

<?php if (!empty($pagination['next']) || !empty($pagination['prev'])): ?>
<div class="docs-navigation d-flex justify-content-between">

    <?php if (!empty($pagination['prev'])): ?>
    <a href="?page=<?php echo $page - 1 ?>">
        <div class="card my-1">
            <div class="card-body py-2">
            ← <?php echo i18n('Newer');?>
            </div>
        </div>
    </a>
    <?php endif;?>

    <?php if (!empty($pagination['next'])): ?>
    <a class="ms-auto" href="?page=<?php echo $page + 1 ?>">
        <div class="card my-1">
            <div class="card-body py-2">
                <?php echo i18n('Older');?> →
            </div>
        </div>
    </a>
    <?php endif;?>
</div>
<?php endif;?>
