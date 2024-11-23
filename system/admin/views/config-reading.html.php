<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Reading_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link active" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Front_page_displays');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-static.frontpage" id="static.frontpage1" value="false" <?php if (config('static.frontpage') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="static.frontpage1">
            <?php echo i18n('Your_latest_blog_posts');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-static.frontpage" id="static.frontpage2" value="true" <?php if (config('static.frontpage') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="static.frontpage2">
            <?php echo i18n('Static_page');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="posts.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_front_page_show_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-posts.perpage" class="form-control" id="posts.perpage" value="<?php echo config('posts.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Blog_posts_displayed_as');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.type" id="teaser.type1" value="full" <?php if (config('teaser.type') === 'full'):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.type1">
            <?php echo i18n('Full_post');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.type" id="teaser.type2" value="trimmed" <?php if (config('teaser.type') === 'trimmed'):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.type2">
            <?php echo i18n('Summary');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="teaser.char" class="col-sm-2 col-form-label"><?php echo i18n('Summary_character');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-teaser.char" class="form-control" id="teaser.char" value="<?php echo config('teaser.char');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('summary_behavior');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.behave" id="teaser.behave1" value="default" <?php if (config('teaser.behave') === 'default' || is_null(config('teaser.behave'))):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.behave1">
            <?php echo i18n('Default');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.behave" id="teaser.behave2" value="check" <?php if (config('teaser.behave') === 'check'):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.behave2">
            <?php echo i18n('Check_shortcode');?>
          </label>
        </div>
      </div>
      <small><em><?php echo i18n('in_summary_mode_whether_check_the_shortcode_first_or_not_before_trim_the_content_to_x_char');?></em></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="read.more" class="col-sm-2 col-form-label"><?php echo i18n('Read_more_text');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-read.more" class="form-control" id="read.more" value="<?php echo valueMaker(config('read.more'));?>" placeholder="<?php echo i18n('Read_more_text_placeholder');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Posts_index_settings');?></h4>
  <hr>
  <div class="form-group row">
    <label for="category.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_category_page_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-category.perpage" class="form-control" id="category.perpage" value="<?php echo config('category.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="archive.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_archive_page_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-archive.perpage" class="form-control" id="archive.perpage" value="<?php echo config('archive.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="tag.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_tag_page_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-tag.perpage" class="form-control" id="tag.perpage" value="<?php echo config('tag.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="search.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_search_result_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-search.perpage" class="form-control" id="search.perpage" value="<?php echo config('search.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="type.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_type_page_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-type.perpage" class="form-control" id="type.perpage" value="<?php echo config('type.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="profile.perpage" class="col-sm-2 col-form-label"><?php echo i18n('Posts_in_profile_page_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-profile.perpage" class="form-control" id="profile.perpage" value="<?php echo config('profile.perpage');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('RSS_settings');?></h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('RSS_feeds_description_select');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-rss.description" id="rss.description1" value="body" <?php if (config('rss.description') === 'body'):?>checked<?php endif;?>>
          <label class="form-check-label" for="rss.description1">
            <?php echo i18n('RSS_description_body');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-rss.description" id="rss.description2" value="meta" <?php if (config('rss.description') === 'meta'):?>checked<?php endif;?>>
          <label class="form-check-label" for="rss.description2">
            <?php echo i18n('RSS_description_meta');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="rss.count" class="col-sm-2 col-form-label"><?php echo i18n('RSS_feeds_show_the_most_recent');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-rss.count" class="form-control" id="rss.count" value="<?php echo config('rss.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="rss.char" class="col-sm-2 col-form-label"><?php echo i18n('RSS_character');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-rss.char" class="form-control" id="rss.char" value="<?php echo config('rss.char');?>">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
