<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<a href="<?php echo $static->url;?>/edit?destination=admin/pages"><?php echo i18n('Edit');?></a>
<h2 class="post-index"><?php echo $static->title ?></h2>
<div><?php echo $static->description;?></div>
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
            <th><?php echo i18n('Operations');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $sp): ?>
            <tr id="<?php echo $sp->parent;?>/<?php echo $sp->md;?>" style="cursor:move;">
                <td><a href="<?php echo $sp->url ?>"><?php echo $sp->title ?></a></td>
                <td><?php echo $sp->description;?></td>
                <td>                                <span><a class="btn btn-primary btn-xs" href="<?php echo $sp->url;?>/edit?destination=admin/pages/<?php echo $static->slug;?>"><?php echo i18n('Edit');?></a> <a class="btn btn-danger btn-xs" href="<?php echo $sp->url;?>/delete?destination=admin/pages/<?php echo $static->slug;?>"><?php echo i18n('Delete');?></a></span></td>
                        
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        <br>
        <button class="btn btn-primary" style="display:none" id="saveButton">Save page order</button>
<?php } else {
    echo i18n('No_posts_found') . '!';
} ?>