<h2>Widget Settings</h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config">General</a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading">Reading</a>
    <a class="nav-item nav-link active" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget">Widget</a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags">Metatags</a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance">Performance</a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom">Custom</a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label for="related.count" class="col-sm-2 col-form-label">Related widget posts at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-related.count" class="form-control" id="related.count" value="<?php echo config('related.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="recent.count" class="col-sm-2 col-form-label">Recent posts widget at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-recent.count" class="form-control" id="recent.count" value="<?php echo config('recent.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Popular posts widget</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-views.counter" id="views.counter1" value="true" <?php if (config('views.counter') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="views.counter1">
            Enable popular posts widget
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-views.counter" id="views.counter2" value="false" <?php if (config('views.counter') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="views.counter2">
            Disable popular posts widget
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="popular.count" class="col-sm-2 col-form-label">Popular posts widget at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-popular.count" class="form-control" id="popular.count" value="<?php echo config('popular.count');?>">
    </div>
  </div>
  <br>
  <h4>Comments</h4>
  <hr>
  <p>To using Disqus or Facebook comment you need to provide Disqus shortname or Facebook App ID.</p>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Comment system</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-comment.system" id="comment.system1" value="disable" <?php if (config('comment.system') === 'disable'):?>checked<?php endif;?>>
          <label class="form-check-label" for="comment.system1">
            Disabled
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
    <label for="disqus.shortname" class="col-sm-2 col-form-label">Disqus shortname</label>
    <div class="col-sm-10">
      <input type="text" name="-config-disqus.shortname" class="form-control" id="disqus.shortname" value="<?php echo valueMaker(config('disqus.shortname'));?>" placeholder="htmly">
    </div>
  </div>
  <div class="form-group row">
    <label for="fb.appid" class="col-sm-2 col-form-label">Facebook App ID</label>
    <div class="col-sm-10">
      <input type="text" name="-config-fb.appid" class="form-control" id="fb.appid" value="<?php echo valueMaker(config('fb.appid'));?>" placeholder="12345abcde">
    </div>
  </div>
  <br>
  <h4>Recaptcha</h4>
  <hr>
  <p>Get one here <a target="_blank" href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Recaptcha</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-google.reCaptcha" id="google.reCaptcha1" value="true" <?php if (config('google.reCaptcha') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="google.reCaptcha1">
            Enable recaptcha
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-google.reCaptcha" id="google.reCaptcha2" value="false" <?php if (config('google.reCaptcha') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="google.reCaptcha2">
            Disable recaptcha
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="google.reCaptcha.public" class="col-sm-2 col-form-label">Site Key</label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.reCaptcha.public" class="form-control" id="google.reCaptcha.public" value="<?php echo valueMaker(config('google.reCaptcha.public'));?>" placeholder="12345abcde">
    </div>
  </div>
  <div class="form-group row">
    <label for="google.reCaptcha.private" class="col-sm-2 col-form-label">Secret Key</label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.reCaptcha.private" class="form-control" id="google.reCaptcha.private" value="<?php echo valueMaker(config('google.reCaptcha.private'));?>" placeholder="12345abcde">
    </div>
  </div>
  <br>
  <h4>Google Analytics</h4>
  <hr>
  <div class="form-group row">
    <label for="google.gtag.id" class="col-sm-2 col-form-label">Universal Analytics (gtag.js)</label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.gtag.id" class="form-control" id="google.gtag.id" value="<?php echo valueMaker(config('google.gtag.id'));?>" placeholder="12345abcde">
    </div>
  </div>
  <div class="form-group row">
    <label for="google.analytics.id" class="col-sm-2 col-form-label">Google Analytics (legacy)</label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.analytics.id" class="form-control" id="google.analytics.id" value="<?php echo valueMaker(config('google.analytics.id'));?>" placeholder="12345abcde">
	  <small><em>This is legacy code. Usually new created analyics using gtag.js</em></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="google.wmt.id" class="col-sm-2 col-form-label">Google Search Console</label>
    <div class="col-sm-10">
      <input type="text" name="-config-google.wmt.id" class="form-control" id="google.wmt.id" value="<?php echo valueMaker(config('google.wmt.id'));?>" placeholder="12345abcde">
	  <small><em>For google-site-verification meta</em></small>
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary">Save config</button>
    </div>
  </div>
</form>
