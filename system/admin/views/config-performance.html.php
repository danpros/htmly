<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Performance_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <a class="nav-item nav-link active" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label for="cache.expiration" class="col-sm-2 col-form-label"><?php echo i18n('Cache_expiration');?></label>
    <div class="col-sm-10">
      <input type="number" name="-config-cache.expiration" class="form-control" id="cache.expiration" value="<?php echo config('cache.expiration');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Cache_off');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.off" id="cache.off1" value="true" <?php if (config('cache.off') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.off1">
            <?php echo i18n('Yes_not_recommended');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.off" id="cache.off2" value="false" <?php if (config('cache.off') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.off2">
            <?php echo i18n('Not');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Cache_timestamp');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.timestamp" id="cache.timestamp1" value="true" <?php if (config('cache.timestamp') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.timestamp1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.timestamp" id="cache.timestamp2" value="false" <?php if (config('cache.timestamp') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Page_generation_time');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-generation.time" id="generation.time1" value="true" <?php if (config('generation.time') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-generation.time" id="generation.time2" value="false" <?php if (config('generation.time') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <br>
  <h4>Multisite</h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Multisite</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-multi.site" id="multi.site1" value="true" <?php if (config('multi.site') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="multi.site1">
            <?php echo i18n('Enable');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-multi.site" id="multi.site2" value="false" <?php if (config('multi.site') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="multi.site2">
            <?php echo i18n('Disable');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <br>
  <h4><?php echo i18n('Github_pre_release');?></h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label"><?php echo i18n('Pre_release');?></label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-prerelease" id="prerelease1" value="true" <?php if (config('prerelease') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="prerelease1">
            <?php echo i18n('Yes_Im_in');?>
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-prerelease" id="prerelease2" value="false" <?php if (config('prerelease') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="prerelease2">
            <?php echo i18n('Nope');?>
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
    </div>
  </div>
</form>
