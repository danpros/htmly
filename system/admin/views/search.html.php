<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo $heading ?></h2>
<br>
<?php $search_index = array(); if (!empty($posts)) { ?>
<form method="POST" action="<?php echo site_url();?>admin/search/reindex">
<p><?php echo i18n('unindexed_posts');?></p>
<input type="submit" class="btn btn-primary" value="<?php echo i18n('add_search_index');?>">
<br><br>
    <table class="table post-list">
        <thead>
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Published');?></th><?php if (config("views.counter") == "true"): ?>
                <th><?php echo i18n('Views');?></th><?php endif; ?>
            <th><?php echo i18n('Author');?></th>
            <th><?php echo i18n('Category');?></th>
            <th><?php echo i18n('Tags');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title ?></a></td>
                <td><?php echo format_date($p->date) ?></td>
                <?php if (config("views.counter") == "true"): ?>
                    <td><?php echo $p->views ?></td><?php endif; ?>
                <td><a target="_blank" href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></td>
                <td><a href="<?php echo site_url() . 'admin/categories/' . $p->categorySlug; ?>"><?php echo $p->categoryTitle;?></a></td>
                <td><?php echo str_replace('rel="tag"', 'rel="tag" class="badge badge-light text-primary font-weight-normal"', $p->tag); ?></td>
            </tr>
		<?php $search_index[] = array($p->slug, $p->file);?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <input type="submit" class="btn btn-primary" value="<?php echo i18n('add_search_index');?>">
	<br><br>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<br>
    <div class="pager">
    <ul class="pagination">
        <?php if (!empty($pagination['prev'])) { ?>
            <li class="newer page-item"><a class="page-link" href="?page=<?php echo $page - 1 ?>" rel="prev">&#8592; <?php echo i18n('Newer');?></a></li>
        <?php } else { ?>
        <li class="page-item disabled" ><span class="page-link">&#8592; <?php echo i18n('Newer');?></span></li>
        <?php } ?>
        <li class="page-number page-item disabled"><span class="page-link"><?php echo $pagination['pagenum'];?></span></li>
        <?php if (!empty($pagination['next'])) { ?>
            <li class="older page-item" ><a class="page-link" href="?page=<?php echo $page + 1 ?>" rel="next"><?php echo i18n('Older');?> &#8594;</a></li>
        <?php } else { ?>
            <li class="page-item disabled" ><span class="page-link"><?php echo i18n('Older');?> &#8594;</span></li>
        <?php } ?>
        </ul>
    </div>
<?php endif; ?>
<input type="hidden" name="search_index" value="<?php print_r(htmlspecialchars(json_encode($search_index)));?>">
</form>
<?php } else {?>
<script>
$(function() {

    var data = 'content/data/search.json';
    $("#clearButton").click(function(){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url();?>admin/search',
            dataType: 'json',
            data: {'json': data},
            success: function (response) {
                alert(response.message);
                location.reload();
            },
        });  
    });    
    
});
</script>
<p><?php echo count(get_blog_posts()); ?> <?php echo i18n('indexed_posts');?></p>
<p><button class="btn btn-primary" id="clearButton"><?php echo i18n('clear_search_index');?></button></p>
<?php } ?>