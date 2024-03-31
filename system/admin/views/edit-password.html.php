<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($_SESSION[site_url()]['user'])) {
    $user = $_SESSION[site_url()]['user'];
}
?>
<h2><?php echo i18n('change_password'); echo ': ' . $user; ?></h2>
<br>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <div class="form-group row">
        <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Username');?></label>
        <div class="col-sm-10">
          <input type="text" name="username" readonly class="form-control" id="username-id" value="<?php echo $user;?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Password');?></label>
        <div class="col-sm-10">
          <input type="password" name="password" class="form-control" id="password" value="" placeholder="<?php echo i18n('change_password');?>">
        </div>
    </div>
    <input type="submit" class="btn btn-primary" style="width:100px;" value="<?php echo i18n('Save');?>">
    <span><a class="btn btn-primary" href="<?php echo site_url();?>admin"><?php echo i18n('Cancel');?></a></span>
</form>