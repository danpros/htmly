<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
global $config_file;
$array = array();
if (file_exists($config_file)) {
  $array = parse_ini_file($config_file, true);
}

$homeFormat = config('home.title.format');
if (empty($homeFormat)) {
    $homeFormat = '%blog_title% - %blog_tagline%';
}
$postFormat = config('post.title.format');
if (empty($postFormat)) {
    $postFormat = '%post_title% - %blog_title%';
}
$pageFormat = config('page.title.format');
if (empty($pageFormat)) {
    $pageFormat = '%page_title% - %blog_title%';
}
$categoryFormat = config('category.title.format');
if (empty($categoryFormat)) {
    $categoryFormat = '%category_title% - %blog_title%';
}
$tagFormat = config('tag.title.format');
if (empty($tagFormat)) {
    $tagFormat = '%tag_title% - %blog_title%';
}
$searchFormat = config('search.title.format');
if (empty($searchFormat)) {
    $searchFormat = '%search_title% - %blog_title%';
}
$archiveFormat = config('archive.title.format');
if (empty($archiveFormat)) {
    $archiveFormat = '%archive_title% - %blog_title%';
}
$typeFormat = config('type.title.format');
if (empty($typeFormat)) {
    $typeFormat = '%type_title% - %blog_title%';
}
$blogFormat = config('blog.title.format');
if (empty($blogFormat)) {
    $blogFormat = 'Blog - %blog_title%';
}
$profileFormat = config('profile.title.format');
if (empty($profileFormat)) {
    $profileFormat = '%author_name% - %blog_title%';
}
$defaultFormat = config('default.title.format');
if (empty($defaultFormat)) {
    $defaultFormat = '%page_title% - %blog_title%';
}

?>
<h2><?php echo i18n('Metatags_Settings');?></h2>
<br>
<?php if (!extension_loaded('gd')) { ?>
<div class="callout callout-info">
<h5><i class="fa fa-info"></i> Note:</h5>
Please install and enable the GD extension to use the thumbnail feature.
</div>
<?php } ?>
<nav>  
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link active" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <h4><?php echo i18n('Permalink');?></h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Enable_blog_URL');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-blog.enable" id="blog.enable1" value="true" <?php if (config('blog.enable') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="blog.enable1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-blog.enable" id="blog.enable2" value="false" <?php if (config('blog.enable') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="blog.enable2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.path" class="col-sm-2 col-form-label">Blog Path</label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.path" class="form-control" id="blog.path" placeholder="blog" value="<?php echo config('blog.path');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.string" class="col-sm-2 col-form-label">Blog String</label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.string" class="form-control" id="blog.string" placeholder="Blog" value="<?php echo config('blog.string');?>">
    </div>
  </div>
  <div class="form-group row">
	<label for="custom.permalink" class="col-sm-2 col-form-label"><?php echo i18n('Permalink');?> Prefix</label>
	<div class="col-sm-10">
	  <input type="text" name="-config-permalink.type" class="form-control" id="permalink.type" value="<?php echo permalink_type();?>" placeholder="default">
	  <p class="title-format" style="margin-bottom:5px;"><code>default</code> <?php echo i18n('year_month_your_post_slug');?></p>
	  <p class="title-format" style="margin-bottom:5px;"><code>post</code> <?php echo i18n('post_your_post_slug');?></p>
	</div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Transliterate Slug</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-transliterate.slug" id="transliterate.slug1" value="true" <?php if (config('transliterate.slug') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="transliterate.slug1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-transliterate.slug" id="transliterate.slug2" value="false" <?php if (config('transliterate.slug') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="transliterate.slug2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Metatags');?></h4>
  <hr>
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
  <div class="form-group row">
    <label for="favicon.image" class="col-sm-2 col-form-label">Favicon Image</label>
    <div class="col-sm-10">
      <input type="text" name="-config-favicon.image" class="form-control" id="favicon.image" value="<?php echo config('favicon.image');?>" placeholder="<?php echo site_url();?>favicon.png">
    </div>
  </div>
  <div class="form-group row">
    <label for="default.image" class="col-sm-2 col-form-label"><?php echo i18n('default');?> Image</label>
    <div class="col-sm-10">
      <input type="text" name="-config-default.image" class="form-control" id="default.image" value="<?php echo config('default.image');?>" placeholder="<?php echo site_url();?>system/resources/images/logo-big.png">
    </div>
  </div>
  <div class="form-group row">
    <label for="thumbnail.width" class="col-sm-2 col-form-label">Thumbnail Width</label>
    <div class="col-sm-10">
      <input type="number" name="-config-thumbnail.width" class="form-control" id="thumbnail.width" value="<?php echo config('thumbnail.width');?>">
    </div>
  </div>
  <br>
  <h4>Title formats</h4>
  <hr>
  <style>.title-format {margin-top:5px;font-size:95%} .title-format code {display:inline-block;margin-left:10px; margin-bottom:5px;color: #333; background: #fbf7f0; padding: 5px; border-radius: .25rem; border: 1px solid #e4e4e4;}</style>
  <div class="form-group row">
    <label for="home.title.format" class="col-sm-2 col-form-label"><?php echo i18n('home');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-home.title.format" class="form-control" id="home.title.format" value="<?php echo $homeFormat;?>" placeholder="%blog_title% - %blog_tagline%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="post.title.format" class="col-sm-2 col-form-label"><?php echo i18n('posts');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-post.title.format" class="form-control" id="post.title.format" value="<?php echo $postFormat;?>" placeholder="%post_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%post_title%</code> <code>%post_description%</code> <code>%post_category%</code> <code>%post_tag%</code> <code>%post_author%</code> <code>%post_type%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="page.title.format" class="col-sm-2 col-form-label"><?php echo i18n('static_page');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-page.title.format" class="form-control" id="page.title.format" value="<?php echo $pageFormat;?>" placeholder="%page_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%page_title%</code> <code>%page_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="category.title.format" class="col-sm-2 col-form-label"><?php echo i18n('category');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-category.title.format" class="form-control" id="category.title.format" value="<?php echo $categoryFormat;?>" placeholder="%category_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%category_title%</code> <code>%category_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="tag.title.format" class="col-sm-2 col-form-label"><?php echo i18n('tag');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-tag.title.format" class="form-control" id="tag.title.format" value="<?php echo $tagFormat;?>" placeholder="%tag_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%tag_title%</code> <code>%tag_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="archive.title.format" class="col-sm-2 col-form-label"><?php echo i18n('archives');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-archive.title.format" class="form-control" id="archive.title.format" value="<?php echo $archiveFormat;?>" placeholder="%archive_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%archive_title%</code> <code>%archive_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="search.title.format" class="col-sm-2 col-form-label"><?php echo i18n('search');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-search.title.format" class="form-control" id="search.title.format" value="<?php echo $searchFormat;?>" placeholder="%search_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%search_title%</code> <code>%search_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="type.title.format" class="col-sm-2 col-form-label">Type</label>
    <div class="col-sm-10">
      <input type="text" name="-config-type.title.format" class="form-control" id="type.title.format" value="<?php echo $typeFormat;?>" placeholder="%type_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%type_title%</code> <code>%type_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="profile.title.format" class="col-sm-2 col-form-label"><?php echo i18n('author');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-profile.title.format" class="form-control" id="profile.title.format" value="<?php echo $profileFormat;?>" placeholder="%author_name% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%author_name%</code> <code>%author_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="blog.title.format" class="col-sm-2 col-form-label">Blog</label>
    <div class="col-sm-10">
      <input type="text" name="-config-blog.title.format" class="form-control" id="blog.title.format" value="<?php echo $blogFormat;?>" placeholder="Blog - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code></p>
    </div>
  </div>
  <div class="form-group row">
    <label for="default.title.format" class="col-sm-2 col-form-label"><?php echo i18n('default');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-default.title.format" class="form-control" id="default.title.format" value="<?php echo $defaultFormat;?>" placeholder="%page_title% - %blog_title%">
      <p class="title-format">Available shortcode: <code>%blog_title%</code> <code>%blog_tagline%</code> <code>%blog_description%</code> <code>%page_title%</code></p>
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Sitemap');?></h4>
  <hr>
  <p><?php echo i18n('Valid_values_range_from_0_to_1.0._See');?> <a target="_blank" href="https://www.sitemaps.org/protocol.html">https://www.sitemaps.org/protocol.html</a>. Disable specific sitemap: <code>-1</code></p>
  <?php foreach($array as $key => $value) {?>
  <?php if (stripos($key, 'sitemap.priority') !== false):?>
  <?php if ($key !== 'sitemap.priority.archiveDay'):?>
  <div class="form-group row">
    <label for="<?php echo $key;?>" class="col-sm-2 col-form-label"><?php echo $key;?></label>
    <div class="col-sm-10">
      <input step="any" type="number" name="-config-<?php echo $key;?>" class="form-control" id="<?php echo $key;?>" value="<?php echo $value;?>">
    </div>
  </div>
  <?php endif;?>
  <?php endif; ?> 
  <?php } ?>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
