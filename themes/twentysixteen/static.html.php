<article class="page type-page hentry">

    <header class="entry-header">
        <?php if (login()) { echo tab($p); } ?>
        <h1 class="entry-title"><?php echo $p->title;?></h1>    
    </header><!-- .entry-header -->

    <div class="entry-content">
        <div class="content">
            <div class="clearfix text-formatted field field--name-body">
                <div class="content">
                    <?php echo $p->body;?>
                </div>
            </div>
        </div>
    </div><!-- .entry-content -->
    
</article><!-- #post-## -->