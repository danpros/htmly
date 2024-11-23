<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?php echo $static->url;?>"><?php echo i18n('View');?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $static->url;?>/edit?destination=admin/pages"><?php echo i18n('Edit');?></a>
    </li>
</ul>
<br>
<div>
    <h2 class="post-index"><?php echo $static->title ?></h2>
    <div><?php echo $static->description;?></div>
</div>
<br>
<a class="btn btn-primary right" href="<?php echo $static->url;?>/add?destination=admin/pages/<?php echo $static->slug;?>"><?php echo i18n('Add_sub');?></a>
<br><br>
<script>
$(function() {
    
    var order;
    $( "tbody" ).sortable({update: function(e, ui) {
        order = $(this).sortable('toArray');
        $("#saveButton").css({"display": "block"});
    }});

    $("#saveButton").click(function(){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url();?>admin/pages/<?php echo $static->slug;?>',
            dataType: 'json',
            data: {'json': order},
            success: function (response) {
                alert(response.message);
                location.reload();
            },
        });  
    });    
    
});
</script>
<?php $posts = find_subpage($p->slug);?>
<?php if (!empty($posts)) { ?>
    <table class="table post-list">
        <thead>
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Description');?></th>
            <?php if (config("views.counter") == "true"): ?>
                <th><?php echo i18n('Views');?></th>
            <?php endif; ?>
            <th><?php echo i18n('Operations');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $sp): ?>
            <tr id="<?php echo $sp->parent;?>/<?php echo $sp->md;?>" style="cursor:move;">
                <td><a href="<?php echo $sp->url ?>"><?php echo $sp->title ?></a></td>
                <td><?php echo $sp->description;?></td>
                <?php if (config("views.counter") == "true"):?><td><?php echo $sp->views;?></td><?php endif;?>
                <td><span><a class="btn btn-primary btn-xs" href="<?php echo $sp->url;?>/edit?destination=admin/pages/<?php echo $static->slug;?>"><?php echo i18n('Edit');?></a> <a class="btn btn-danger btn-xs" href="<?php echo $sp->url;?>/delete?destination=admin/pages/<?php echo $static->slug;?>"><?php echo i18n('Delete');?></a></span></td>
                        
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        <br>
        <button class="btn btn-primary" style="display:none" id="saveButton"><?php echo i18n('save_config');?></button>
<?php } else {
    echo i18n('No_posts_found') . '!';
} ?>