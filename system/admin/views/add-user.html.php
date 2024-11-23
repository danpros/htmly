<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Add_user'); ?></h2>
<br>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <div class="form-group row">
        <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Username');?></label>
        <div class="col-sm-10">
          <input type="text" name="username" class="form-control" id="username-id" value="<?php if (isset($username)) { echo $username;} ?>" placeholder="<?php echo i18n('username');?>">
        </div>
    </div>
    <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Role');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="user-role" id="admin-id" value="admin" >
          <label class="form-check-label" for="admin-id">
            Admin
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="user-role" id="editor-id" value="editor">
          <label class="form-check-label" for="editor-id">
            Editor
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="user-role" id="author-id" value="author" checked>
          <label class="form-check-label" for="author-id">
            Author
          </label>
        </div>
      </div>
    </div>
    </div>
    <div class="form-group row">
        <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Password');?></label>
        <div class="col-sm-10">
          <input type="password" name="password" class="form-control" id="password" value="<?php if (isset($password)) { echo $password;} ?>" placeholder="<?php echo i18n('password');?>">
        </div>
    </div>
    <input type="submit" class="btn btn-primary" style="width:100px;" value="<?php echo i18n('Save');?>">
    <span><a class="btn btn-primary" href="<?php echo site_url();?>admin/users"><?php echo i18n('Cancel');?></a></span>
</form>