<h2>Performance Settings</h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config">General</a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading">Reading</a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget">Widget</a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags">Metatags</a>
    <a class="nav-item nav-link active" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance">Performance</a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom">Custom</a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label for="cache.expiration" class="col-sm-2 col-form-label">Cache expiration</label>
    <div class="col-sm-10">
      <input type="number" name="-config-cache.expiration" class="form-control" id="cache.expiration" value="<?php echo config('cache.expiration');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Cache off</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.off" id="cache.off1" value="true" <?php if (config('cache.off') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.off1">
            Yes (not recommended)
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.off" id="cache.off2" value="false" <?php if (config('cache.off') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.off2">
            No
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Cache timestamp</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.timestamp" id="cache.timestamp1" value="true" <?php if (config('cache.timestamp') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="cache.timestamp1">
            Enable
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-cache.timestamp" id="cache.timestamp2" value="false" <?php if (config('cache.timestamp') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time2">
            Disable
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Page generation time</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-generation.time" id="generation.time1" value="true" <?php if (config('generation.time') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time1">
            Enable
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-generation.time" id="generation.time2" value="false" <?php if (config('generation.time') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="generation.time2">
            Disable
          </label>
        </div>
      </div>
	</div>
  </div>
  <br>
  <h4>Github pre-release</h4>
  <hr>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Pre-release</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-prerelease" id="prerelease1" value="true" <?php if (config('prerelease') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="prerelease1">
            Yes I'm in
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-prerelease" id="prerelease2" value="false" <?php if (config('prerelease') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="prerelease2">
            Nope
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary">Save config</button>
    </div>
  </div>
</form>
