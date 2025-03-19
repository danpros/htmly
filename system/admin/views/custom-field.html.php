<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo i18n('custom_fields');?></h2>
<br>
<p>Custom fields enable users to add extra, specific data fields to their content, allowing for more detailed and flexible content management.</p>

<p>Use <code>get_field()</code> function in your template.
<ul><li>Post, Page, Subpage: <code>&lt;?php echo get_field('field_name', $p-&gt;raw);?&gt;</code></code></li>
<li>Profile: <code>&lt;?php echo get_field('field_name', $author-&gt;raw);?&gt;</code></code></li></ul>
</p>

<table class="table post-list">
    <thead>
    <tr class="head">
        <th>Type</th>
        <th><?php echo i18n('Operations');?></th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>Post</td>
            <td><a class="btn btn-primary btn-xs" href="<?php echo site_url();?>admin/field/post"><?php echo i18n('edit');?></a></td>
        </tr>
        <tr>
            <td>Page</td>
            <td><a class="btn btn-primary btn-xs" href="<?php echo site_url();?>admin/field/page"><?php echo i18n('edit');?></a></td>
        </tr>
        <tr>
            <td>Subpage</td>
            <td><a class="btn btn-primary btn-xs" href="<?php echo site_url();?>admin/field/subpage"><?php echo i18n('edit');?></a></td>
        </tr>
        <tr>
            <td>Profile</td>
            <td><a class="btn btn-primary btn-xs" href="<?php echo site_url();?>admin/field/profile"><?php echo i18n('edit');?></a></td>
        </tr>
    </tbody>
</table>