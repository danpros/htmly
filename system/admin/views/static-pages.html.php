<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Static_pages') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>add/page"><?php echo i18n('Add_new_page');?></a>
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
            url: '<?php echo site_url();?>admin/pages',
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
<?php if (isset($_SESSION[site_url()]['user'])):?>
    <?php $posts = find_page();
    if (!empty($posts)): ?>
        <table class="table post-list" id="sortable">
        <thead>
        <tr class="head" id="head">
            <th><?php echo i18n('Title');?> </th>
            <?php if (config("views.counter") == "true"): ?>
                <th><?php echo i18n('Views');?></th>
            <?php endif; ?>
            <th><?php echo i18n('Operations');?></th>
            <th>Sub <?php echo i18n('pages');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $p):?>
            <?php $dd = find_subpage($p->slug); ?>
            <?php $dr = find_draft_subpage($p->slug);?>
            <tr id="<?php echo $p->md;?>" class="sort-item" style="cursor:move;">
                <td><a href="<?php echo site_url();?>admin/pages/<?php echo $p->slug;?>"><?php echo $p->title;?></a></td>
                <?php if (config("views.counter") == "true"):?><td><?php echo $p->views;?></td><?php endif;?>
                <td><a class="btn btn-primary btn-xs" href="<?php echo site_url();?>admin/pages/<?php echo $p->slug;?>"><?php echo i18n('page');?> <?php echo i18n('settings');?></a> <a class="btn btn-primary btn-xs" href="<?php echo $p->url;?>/add?destination=admin/pages/<?php echo $p->slug;?>"><?php echo i18n('Add_sub');?></a> <a class="btn btn-primary btn-xs" href="<?php echo $p->url;?>/edit?destination=admin/pages"><?php echo i18n('Edit');?></a> <?php if (empty($dd) && empty($dr)):?><a class="btn btn-danger btn-xs" href="<?php echo $p->url;?>/delete?destination=admin/pages"><?php echo i18n('Delete');?></a><?php endif;?></td>
                <td>
                    <?php foreach ($dd as $sp):?>                            
                    <div class="row">
                        <div class="col-sm">
                            <span><a target="_blank" href="<?php echo $sp->url;?>"><?php echo $sp->title;?></a></span>
                        </div>
                        <?php if (config("views.counter") == "true"):?><div class="col-sm"><i class="fa fa-line-chart" aria-hidden="true"></i> <?php echo $sp->views;?></div><?php endif;?>
                        <div class="col-sm">
                            <span><a class="btn btn-primary btn-xs" href="<?php echo $sp->url;?>/edit?destination=admin/pages/<?php echo $sp->parentSlug;?>"><?php echo i18n('Edit');?></a> <a class="btn btn-danger btn-xs" href="<?php echo $sp->url;?>/delete?destination=admin/pages/<?php echo $sp->parentSlug;?>"><?php echo i18n('Delete');?></a></span>
                        </div>
                    </div>                            
                    <?php endforeach;?>    
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
        </table>
        <br>
        <button class="btn btn-primary" style="display:none" id="saveButton"><?php echo i18n('save_config');?></button>
    <?php endif;?>
<?php endif;?>