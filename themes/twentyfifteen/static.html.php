<article class="page type-page hentry">
    <header class="entry-header">
        <?php if (login()) { echo tab($p); } ?>
        <h1 class="entry-title"><?php echo $p->title ?></h1>
    </header>
    <div class="entry-content">
        <?php echo $p->body; ?>
    </div>
</article>