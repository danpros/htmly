<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Static_pages') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>add/page"><?php echo i18n('Add_new_page');?></a>
<br><br>
<?php if (isset($_SESSION[site_url()]['user'])):?>
    <?php $posts = find_page();
    if (!empty($posts)): ?>
        <table class="table post-list">
        <tr class="head">
            <th><?php echo i18n('Title');?> </th>
            <?php if (config("views.counter") == "true"):?>
            <th><?php echo i18n('Views');?></th>
            <?php endif;?>
            <th><?php echo i18n('Operations');?></th>
            <th>Subpages</th>
        </tr>
        <?php foreach ($posts as $p):?>
            <?php $dd = find_subpage($p->md); ?>
            <?php $dr = find_draft_subpage($p->md);?>
            <tr>
                <td><a target="_blank" href="<?php echo $p->url;?>"><?php echo $p->title;?></a></td>
                <?php if (config("views.counter") == "true"):?>
                <td><i class="nav-icon fa fa-line-chart"></i> <?php echo $p->views;?></td>
                <?php endif;?>
                <td><a class="btn btn-primary btn-xs" href="<?php echo $p->url;?>/add?destination=admin/pages"><?php echo i18n('Add_sub');?></a> <a class="btn btn-primary btn-xs" href="<?php echo $p->url;?>/edit?destination=admin/pages"><?php echo i18n('Edit');?></a> <?php if (empty($dd) && empty($dr)):?><a class="btn btn-danger btn-xs" href="<?php echo $p->url;?>/delete?destination=admin/pages"><?php echo i18n('Delete');?></a><?php endif;?></td>
                <td>
                    <table>
                        <?php $subPages = find_subpage($p->md);
                        foreach ($subPages as $sp):?>
                                                        
                        <div class="row">
                            <div class="col-6 col-md-4">
                                <span><a target="_blank" href="<?php echo $sp->url;?>"><?php echo $sp->title;?></a></span>
                            </div>
                            <div class="col-6 col-md-4">
                                <?php if (config("views.counter") == "true"):?>
                                    <span><i class="nav-icon fa fa-line-chart"></i> <?php echo $sp->views;?></span>
                                <?php endif;?></div>
                            <div class="col-6 col-md-4">
                                <span><a class="btn btn-primary btn-xs" href="<?php echo $sp->url;?>/edit?destination=admin/pages"><?php echo i18n('Edit');?></a> <a class="btn btn-danger btn-xs" href="<?php echo $sp->url;?>/delete?destination=admin/pages"><?php echo i18n('Delete');?></a></span>
                            </div>
                        </div>                            
                            
                        <?php endforeach;?>
                    
                    </table>
                </td>
            </tr>

        <?php endforeach;?>
        </table>
    <?php endif;?>
<?php endif;?>