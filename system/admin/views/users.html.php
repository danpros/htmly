<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo i18n('Manage_users'); ?></h2>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/add/user"><?php echo i18n('Add_user');?></a>
<br><br>
<?php 
$users = glob('config/users/*.ini', GLOB_NOSORT);
ksort($users);
?>

<table class="table post-list">
    <thead>
    <tr class="head">
        <th><?php echo i18n('username');?></th>
        <th><?php echo i18n('role');?></th>
        <th><?php echo i18n('Operations');?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <?php $username = pathinfo($u, PATHINFO_FILENAME); $role = user('role', $username);?>
        <tr>
            <td><?php echo $username; ?></td>
            <td><?php echo $role; ?></td>
            <td><a class="btn btn-primary btn-xs" href="<?php echo site_url() . 'admin/users/' . $username;?>/edit?destination=admin/users"><?php echo i18n('Edit');?></a> <?php if ($role !== 'admin'):?><a class="btn btn-danger btn-xs" href="<?php echo site_url() . 'admin/users/' . $username;?>/delete?destination=admin/users"><?php echo i18n('Delete');?></a><?php endif;?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>