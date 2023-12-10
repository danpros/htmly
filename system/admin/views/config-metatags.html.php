<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
global $config_file;
$array = array();
if (file_exists($config_file)) {
  $array = parse_ini_file($config_file, true);
}
?>
<h2><?php echo i18n('Metatags_Settings');?></h2>
<br>
<nav>  
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link active" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Permalink');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-permalink.type" id="permalink.type1" value="default" <?php if (config('permalink.type') === 'default'):?>checked<?php endif;?>>
          <label class="form-check-label" for="permalink.type1">
            <?php echo i18n('year_month_your_post_slug');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-permalink.type" id="permalink.type1" value="post" <?php if (config('permalink.type') === 'post'):?>checked<?php endif;?>>
          <label class="form-check-label" for="permalink.type2">
            <?php echo i18n('post_your_post_slug');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="description.char" class="col-sm-2 col-form-label"><?php echo i18n('Meta_description_character');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-description.char" class="form-control" id="description.char" value="<?php echo config('description.char');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="read.more" class="col-sm-2 col-form-label"><?php echo i18n('Breadcrumb_home_text');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-breadcrumb.home" class="form-control" id="breadcrumb.home" value="<?php echo valueMaker(config('breadcrumb.home'));?>" placeholder="<?php echo i18n('Home');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Sitemap');?></h4>
  <hr>
  <p><?php echo i18n('Valid_values_range_from_0_to_1.0._See');?> <a target="_blank" href="https://www.sitemaps.org/protocol.html">https://www.sitemaps.org/protocol.html</a></p>
  <?php foreach($array as $key => $value) {?>
  <?php if (stripos($key, 'sitemap.priority') !== false):?>
  <div class="form-group row">
    <label for="<?php echo $key;?>" class="col-sm-2 col-form-label"><?php echo $key;?></label>
    <div class="col-sm-10">
      <input step="any" type="number" name="-config-<?php echo $key;?>" class="form-control" id="<?php echo $key;?>" value="<?php echo $value;?>">
    </div>
  </div>  
  <?php endif; ?> 
  <?php } ?>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
