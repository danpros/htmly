<h1 class="page-header">Categories</h1>
<?php $desc = get_category_info(null); ?>
<div class="table-responsive">
    <a type="button" class="btn btn-md btn-primary pull-right" href="<?php echo site_url();?>add/category"><i class="glyphicon glyphicon-plus"></i> Add category</a>
    <table id="category-list" class="table table-striped">
        <thead>
            <tr class="head">
                <th>Name</th>
                <th>Description</th>
                <th>Contents</th>
                <th>Operations</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?php echo site_url();?>category/uncategorized" target="_blank">Uncategorized</a></td>
                <td><p>Topics that don't need a category, or don't fit into any other existing category.</p></td>
                <td><?php $total = get_draftcount('uncategorized') + get_categorycount('uncategorized'); echo $total?></td>
                <td></td>
            </tr>
            <?php foreach ($desc as $d):?>
            <tr>
                <td><a href="<?php echo $d->url;?>" target="_blank"><?php echo $d->title;?></a></td>
                <td><?php echo $d->body;?></td>
                <td><?php $total = get_draftcount($d->md) + get_categorycount($d->md); echo $total?></td>
                <td>
                    <a type="button" class="btn btn-md btn-warning" href="<?php echo $d->url;?>/edit?destination=admin/categories"><i class="glyphicon glyphicon-pencil"></i></a> 
                    <?php if (get_categorycount($d->md) == 0 && get_draftcount($d->md) == 0 ){echo '<a type="button" class="btn btn-md btn-danger" href="' . $d->url . '/delete?destination=admin/categories"><i class="glyphicon glyphicon-trash"></i></a>';}?>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
