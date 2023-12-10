<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Widget_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link active" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label for="related.count" class="col-sm-2 col-form-label"><?php echo i18n('Related_widget_posts_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-related.count" class="form-control" id="related.count" value="<?php echo config('related.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="recent.count" class="col-sm-2 col-form-label"><?php echo i18n('Recent_posts_widget_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-recent.count" class="form-control" id="recent.count" value="<?php echo config('recent.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Popular_posts_widget');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-views.counter" id="views.counter1" value="true" <?php if (config('views.counter') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="views.counter1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-views.counter" id="views.counter2" value="false" <?php if (config('views.counter') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="views.counter2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="popular.count" class="col-sm-2 col-form-label"><?php echo i18n('Popular_posts_widget_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-popular.count" class="form-control" id="popular.count" value="<?php echo config('popular.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="tagcloud.count" class="col-sm-2 col-form-label"><?php echo i18n('Tagcloud_widget_at_most');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-tagcloud.count" class="form-control" id="tagcloud.count" value="<?php echo config('tagcloud.count');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Comments');?></h4>
  <hr>
  <p><?php echo i18n('To_using_Disqus_or_Facebook_comment_you_need_to_provide_Disqus_shortname_or_Facebook_App_ID');?></p>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Comment_system');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-comment.system" id="comment.system1" value="disable" <?php if (config('comment.system') === 'disable'):?>checked<?php endif;?>>
          <label class="form-check-label" for="comment.system1">
            <?php echo i18n('Disabled');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-comment.system" id="comment.system2" value="disqus" <?php if (config('comment.system') === 'disqus'):?>checked<?php endif;?>>
          <label class="form-check-label" for="comment.system2">
            Disqus
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-comment.system" id="comment.system3" value="facebook" <?php if (config('comment.system') === 'facebook'):?>checked<?php endif;?>>
          <label class="form-check-label" for="comment.system3">
            Facebook
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="disqus.shortname" class="col-sm-2 col-form-label"><?php echo i18n('Disqus_shortname');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-disqus.shortname" class="form-control" id="disqus.shortname" value="<?php echo valueMaker(config('disqus.shortname'));?>" placeholder="<?php echo i18n('Disqus_shortname_placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="fb.appid" class="col-sm-2 col-form-label"><?php echo i18n('Facebook_App_ID');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-fb.appid" class="form-control" id="fb.appid" value="<?php echo valueMaker(config('fb.appid'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Recaptcha');?></h4>
  <hr>
  <p><?php echo i18n('Get_one_here');?>  <a target="_blank" href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Recaptcha');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-google.reCaptcha" id="google.reCaptcha1" value="true" <?php if (config('google.reCaptcha') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="google.reCaptcha1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-google.reCaptcha" id="google.reCaptcha2" value="false" <?php if (config('google.reCaptcha') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="google.reCaptcha2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="google.reCaptcha.public" class="col-sm-2 col-form-label"><?php echo i18n('Site_Key');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.reCaptcha.public" class="form-control" id="google.reCaptcha.public" value="<?php echo valueMaker(config('google.reCaptcha.public'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="google.reCaptcha.private" class="col-sm-2 col-form-label"><?php echo i18n('Secret_Key');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.reCaptcha.private" class="form-control" id="google.reCaptcha.private" value="<?php echo valueMaker(config('google.reCaptcha.private'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Google_Analytics');?></h4>
  <hr>
  <div class="form-group row">
    <label for="google.gtag.id" class="col-sm-2 col-form-label"><?php echo i18n('Universal_Analytics');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.gtag.id" class="form-control" id="google.gtag.id" value="<?php echo valueMaker(config('google.gtag.id'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="google.analytics.id" class="col-sm-2 col-form-label"><?php echo i18n('Google_Analytics_legacy');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.analytics.id" class="form-control" id="google.analytics.id" value="<?php echo valueMaker(config('google.analytics.id'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
      <small><em><?php echo i18n('This_is_legacy_code_usually_new_created_analytics_using_gtag_js');?></em></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="google.wmt.id" class="col-sm-2 col-form-label"><?php echo i18n('Google_Search_Console');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.wmt.id" class="form-control" id="google.wmt.id" value="<?php echo valueMaker(config('google.wmt.id'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
      <small><em><?php echo i18n('For_google_site_verification_meta');?></em></small>
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Social_Media');?></h4>
  <hr>
  <div class="form-group row">
    <label for="google.reCaptcha.private" class="col-sm-2 col-form-label"><?php echo i18n('Twitter_account');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-social.twitter" class="form-control" id="social.twitter" value="<?php echo valueMaker(config('social.twitter'));?>" placeholder="https://twitter.com/gohtmly">
    </div>
  </div>
  <div class="form-group row">
    <label for="google.reCaptcha.private" class="col-sm-2 col-form-label"><?php echo i18n('Facebook_page');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-social.facebook" class="form-control" id="social.facebook" value="<?php echo valueMaker(config('social.facebook'));?>" placeholder="https://www.facebook.com/gohtmly">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
