<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Security_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link active" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <h4><?php echo i18n('Recaptcha');?></h4>
  <hr>
  <p><?php echo i18n('Get_one_here');?>  <a target="_blank" href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>
  <p><?php echo i18n('Cloudflare_info');?>  <a target="_blank" href="https://developers.cloudflare.com/turnstile/">https://developers.cloudflare.com/turnstile/</a>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Recaptcha');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-login.protect.system" id="login.protect.system1" value="disable" <?php if (config('login.protect.system') === 'disable'):?>checked<?php endif;?>>
          <label class="form-check-label" for="login.protect.system1">
            <?php echo i18n('Disabled');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-login.protect.system" id="login.protect.system2" value="google" <?php if (config('login.protect.system') === 'google'):?>checked<?php endif;?>>
          <label class="form-check-label" for="login.protect.system2">
            Google reCaptcha
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-login.protect.system" id="login.protect.system3" value="cloudflare" <?php if (config('login.protect.system') === 'cloudflare'):?>checked<?php endif;?>>
          <label class="form-check-label" for="login.protect.system3">
            Cloudflare Turnstile
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label for="login.protect.public" class="col-sm-2 col-form-label"><?php echo i18n('Site_Key');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-login.protect.public" class="form-control" id="login.protect.public" value="<?php echo valueMaker(config('login.protect.public'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="login.protect.private" class="col-sm-2 col-form-label"><?php echo i18n('Secret_Key');?></label>
    <div class="col-sm-10">
      <input type="text" name="-config-login.protect.private" class="form-control" id="login.protect.private" value="<?php echo valueMaker(config('login.protect.private'));?>" placeholder="<?php echo i18n('widget_key_placeholder');?>">
    </div>
  </div>
  <br>
  <h4><?php echo i18n('mfa_config');?></h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('set_mfa_globally');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-mfa.state" id="mfa.state1" value="true" <?php if (config('mfa.state') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="mfa.state1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-mfa.state" id="mfa.state2" value="false" <?php if (config('mfa.state') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="mfa.state2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
	  <small><em><?php echo i18n('explain_mfa');?></em></small>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
