<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<nav class="docs-toc d-none d-xl-block col-xl-3" aria-label="Secondary navigation">
    <div class="page-links">
        <p class="h3">On this page</p>
        <nav id="toc"></nav>
        <p class="link-to-top"><a href="#main-top-link"><span aria-hidden="true">↑︎</span> Back to top</a></p>
    </div>
</nav>

<main class="docs-content col-lg-11 col-xl-9" id="main-top-link">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php echo $breadcrumb ?>
        </ol>
    </nav>

    <?php if (authorized($static)):?>
    <div class="edit-page"><a href="<?php echo $static->url;?>/edit?destination=post"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828.0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg><?php echo i18n('Edit');?></a></div>        
    <?php endif;?>

    <h1><?php echo $static->title;?></h1>

    <div id="content">

        <?php echo $static->body;?>

        <?php if (isset($is_page)):?>
        <div class="subpages">
        <?php $subpages = find_subpage($static->slug);?>
        <?php if (!empty($subpages)):?>
            <div class="card-list">
            <h2 class="h4">Sub <?php echo i18n('pages');?></h2>
            <?php foreach ($subpages as $sp):?>
                <div class="card my-3">
                    <div class="card-body">
                        <a class="stretched-link" href="<?php echo $sp->url;?>"><?php echo $sp->title;?> →</a>
                        <br><small><?php echo $sp->description;?></small>
                    </div>
                </div>
            <?php endforeach;?>
            </div>
        <?php endif;?>
        </div>
        <?php endif;?>

    </div>

    <div class="docs-navigation d-flex justify-content-between">

        <?php if (isset($is_page)):?>

            <?php if (!empty($next)): ?>
            <?php $nextSub = find_subpage($next['slug']); $last = end($nextSub);?>
                <?php if (!empty($nextSub)) { ?>
                <a class="me-auto" href="<?php echo($last->url); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                        ← <?php echo($last->title); ?>
                        </div>
                    </div>
                </a>
                <?php } else { ?>
                    <a class="me-auto" href="<?php echo($next['url']); ?>">
                        <div class="card my-1">
                            <div class="card-body py-2">
                            ← <?php echo($next['title']); ?>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            <?php endif;?>

        <?php endif;?>

        <?php if (isset($is_subpage)):?>
            <?php if (!empty($next)) { ?>
                <a class="me-auto" href="<?php echo($next['url']); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                            ← <?php echo($next['title']); ?>
                        </div>
                    </div>
                </a>                
            <?php } else { ?>
                <?php if (!empty($parent['next'])): ?>
                <?php $nextSub = find_subpage($parent['next']->slug); $last = end($nextSub);?>
                    <?php if (!empty($nextSub)) { ?>
                        <a class="me-auto" href="<?php echo($last->url); ?>">
                            <div class="card my-1">
                                <div class="card-body py-2">
                                    ← <?php echo($last->title); ?>
                                </div>
                            </div>
                        </a>
                    <?php } else { ?>
                        <a class="me-auto" href="<?php echo($parent['next']->url); ?>">
                            <div class="card my-1">
                                <div class="card-body py-2">
                                    ← <?php echo($parent['next']->title); ?>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                <?php endif;?>                
            <?php }?>
        <?php endif;?>

        <?php if (isset($is_subpage)):?>
        <a href="<?php echo($parent['current']->url); ?>">
            <div class="card my-1">
                <div class="card-body py-2">
                    Back to <?php echo($parent['current']->title); ?>
                </div>
            </div>
        </a>
        <?php endif;?>

        <?php if (isset($is_page)):?>
            <?php if (!empty($subpages[0])) { ?>
                <a class="ms-auto" href="<?php echo($subpages[0]->url); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                            <?php echo($subpages[0]->title); ?> →
                        </div>
                    </div>
                </a>                
            <?php } else { ?>
                <?php if (!empty($prev)): ?>
                <a class="ms-auto" href="<?php echo($prev['url']); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                            <?php echo($prev['title']); ?> →
                        </div>
                    </div>
                </a>
                <?php endif;?>                
            <?php }?>
        <?php endif;?>

        <?php if (isset($is_subpage)):?>
            <?php if (!empty($prev)) { ?>
                <a class="ms-auto" href="<?php echo($prev['url']); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                            <?php echo($prev['title']); ?> →
                        </div>
                    </div>
                </a>                
            <?php } else { ?>
                <?php if (!empty($parent['prev'])): ?>
                <a class="ms-auto" href="<?php echo($parent['prev']->url); ?>">
                    <div class="card my-1">
                        <div class="card-body py-2">
                            <?php echo($parent['prev']->title); ?> →
                        </div>
                    </div>
                </a>
                <?php endif;?>                
            <?php }?>
        <?php endif;?>

    </div>

</main>
