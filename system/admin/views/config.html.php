<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('General_Settings')?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link active" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Address_URL');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-site.url" class="form-control" id="site.url" value="<?php echo valueMaker(config('site.url'));?>" placeholder="https://www.htmly.com">
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.title" class="col-sm-2 col-form-label"><?php echo i18n('Blog_Title');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.title" class="form-control" id="blog.title" value="<?php echo valueMaker(config('blog.title'));?>" placeholder="<?php echo i18n('Blog_Title_Placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.tagline" class="col-sm-2 col-form-label"><?php echo i18n('Tagline');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.tagline" class="form-control" id="blog.tagline" value="<?php echo valueMaker(config('blog.tagline'));?>" placeholder="<?php echo i18n('Tagline_Placeholder');?>">
	  <small><em><?php echo i18n('Tagline_description');?></em></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.description" class="col-sm-2 col-form-label"><?php echo i18n('Description');?></label>
    <div class="col-sm-10">
      <textarea id="blog.description" name="-config-blog.description" class="form-control"><?php echo valueMaker(config('blog.description'));?></textarea>   
	  <small><em><?php echo i18n('Blog_Description');?></em></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="language" class="col-sm-2 col-form-label"><?php echo i18n('Language');?></label>
    <div class="col-sm-10">
    <select class="form-control" id="language" name="-config-language">
    <?php foreach (glob('lang/*.ini') as $file) { ?>
       <option value="<?php echo pathinfo($file)['filename'];?>" <?php if (config('language') === pathinfo($file)['filename']):?>selected<?php endif;?>><?php echo pathinfo($file)['filename'];?></option>
    <?php } ?>
    </select> 
	</div>
  </div>
  <div class="form-group row">
    <label for="timezone" class="col-sm-2 col-form-label"><?php echo i18n('Timezone');?></label>
    <div class="col-sm-10">
    <select class="form-control" id="timezone" name="-config-timezone">
    <?php foreach (timezone_identifiers_list() as $zone) { ?>
       <option value="<?php echo $zone;?>" <?php if (config('timezone') === $zone):?>selected<?php endif;?>><?php echo $zone;?></option>
    <?php } ?>
    </select> 
	</div>
  </div>
  <div class="form-group row">
  <?php $time = new DateTime('NOW'); $date = $time->format("Y-m-d H:i:s");?>
    <label class="col-sm-2 col-form-label"><?php echo i18n('Date_Format');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format1" value="dd MMMM yyyy" <?php if (config('date.format') === 'dd MMMM yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format1">
            <?php echo format_date(strtotime($date), 'dd MMMM yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format2" value="MMMM dd, yyyy" <?php if (config('date.format') === 'MMMM dd, yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format2">
            <?php echo format_date(strtotime($date), 'MMMM dd, yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format3" value="dd MMM yyyy" <?php if (config('date.format') === 'dd MMM yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format3">
            <?php echo format_date(strtotime($date), 'dd MMM yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format4" value="MMM dd, yyyy" <?php if (config('date.format') === 'MMM dd, yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format4">
            <?php echo format_date(strtotime($date), 'MMM dd, yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format5" value="dd/MM/yyyy" <?php if (config('date.format') === 'dd/MM/yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format5">
            <?php echo format_date(strtotime($date), 'dd/MM/yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format6" value="MM/dd/yyyy" <?php if (config('date.format') === 'MM/dd/yyyy'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format6">
            <?php echo format_date(strtotime($date), 'MM/dd/yyyy'); ?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-date.format" id="date.format6" value="yyyy-MM-dd" <?php if (config('date.format') === 'yyyy-MM-dd'):?>checked<?php endif;?>>
          <label class="form-check-label" for="date.format6">
            <?php echo format_date(strtotime($date), 'yyyy-MM-dd'); ?>
          </label>
        </div>

      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="views.root" class="col-sm-2 col-form-label"><?php echo i18n('Blog_Theme');?></label>
    <div class="col-sm-10">
    <select class="form-control" id="views.root" name="-config-views.root">
    <?php foreach (glob('themes/*/layout.html.php') as $folder) { ?>
	   <?php $theme = explode('/',pathinfo($folder)['dirname']); global $config_file; $this_config = parse_ini_file($config_file, true);?>
       <option value="<?php echo pathinfo($folder)['dirname'];?>" <?php if ($this_config['views.root'] === pathinfo($folder)['dirname']):?>selected<?php endif;?>><?php echo $theme['1'];?></option>
    <?php } ?>
    </select> 
	</div>
  </div>
  <div class="form-group row">
    <label for="blog.copyright" class="col-sm-2 col-form-label"><?php echo i18n('Copyright_Line');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.copyright" class="form-control" id="blog.copyright" value="<?php echo valueMaker(config('blog.copyright'));?>" placeholder="<?php echo i18n('Copyright_Line_Placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>