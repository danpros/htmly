<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2>Reading Settings</h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config">General</a>
    <a class="nav-item nav-link active" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading">Reading</a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget">Widget</a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags">Metatags</a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance">Performance</a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom">Custom</a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Front page displays</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-static.frontpage" id="static.frontpage1" value="false" <?php if (config('static.frontpage') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="static.frontpage1">
            Your latest blog posts
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-static.frontpage" id="static.frontpage1" value="true" <?php if (config('static.frontpage') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="static.frontpage2">
            Static page
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Enable /blog URL</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-blog.enable" id="blog.enable1" value="true" <?php if (config('blog.enable') === 'true'):?>checked<?php endif;?>>
          <label class="form-check-label" for="blog.enable1">
            Enable
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-blog.enable" id="blog.enable2" value="false" <?php if (config('blog.enable') === 'false'):?>checked<?php endif;?>>
          <label class="form-check-label" for="blog.enable2">
            Disable
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="posts.perpage" class="col-sm-2 col-form-label">Posts in front page show at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-posts.perpage" class="form-control" id="posts.perpage" value="<?php echo config('posts.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Blog posts displayed as</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.type" id="teaser.type1" value="full" <?php if (config('teaser.type') === 'full'):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.type1">
            Full post
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-teaser.type" id="teaser.type2" value="trimmed" <?php if (config('teaser.type') === 'trimmed'):?>checked<?php endif;?>>
          <label class="form-check-label" for="teaser.type2">
            Summary
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="teaser.char" class="col-sm-2 col-form-label">Summary character</label>
    <div class="col-sm-10">
      <input type="number" name="-config-teaser.char" class="form-control" id="teaser.char" value="<?php echo config('teaser.char');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="read.more" class="col-sm-2 col-form-label">Read more text</label>
    <div class="col-sm-10">
      <input type="text" name="-config-read.more" class="form-control" id="read.more" value="<?php echo valueMaker(config('read.more'));?>" placeholder="Read more">
    </div>
  </div>
  <br>
  <h4>Posts index settings</h4>
  <hr>
  <div class="form-group row">
    <label for="category.perpage" class="col-sm-2 col-form-label">Posts in category page at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-category.perpage" class="form-control" id="category.perpage" value="<?php echo config('category.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="archive.perpage" class="col-sm-2 col-form-label">Posts in archive page at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-archive.perpage" class="form-control" id="archive.perpage" value="<?php echo config('archive.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="tag.perpage" class="col-sm-2 col-form-label">Posts in tag page at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-tag.perpage" class="form-control" id="tag.perpage" value="<?php echo config('tag.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="search.perpage" class="col-sm-2 col-form-label">Posts in search result at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-search.perpage" class="form-control" id="search.perpage" value="<?php echo config('search.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="type.perpage" class="col-sm-2 col-form-label">Posts in type page at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-type.perpage" class="form-control" id="type.perpage" value="<?php echo config('type.perpage');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="profile.perpage" class="col-sm-2 col-form-label">Posts in profile page at most</label>
    <div class="col-sm-10">
      <input type="number" name="-config-profile.perpage" class="form-control" id="profile.perpage" value="<?php echo config('profile.perpage');?>">
    </div>
  </div>
  <br>
  <h4>RSS settings</h4>
  <hr>
  <div class="form-group row">
    <label for="rss.count" class="col-sm-2 col-form-label">RSS feeds show the most recent</label>
    <div class="col-sm-10">
      <input type="number" name="-config-rss.count" class="form-control" id="rss.count" value="<?php echo config('rss.count');?>">
    </div>
  </div>
  <div class="form-group row">
    <label for="rss.char" class="col-sm-2 col-form-label">RSS character</label>
    <div class="col-sm-10">
      <input type="number" name="-config-rss.char" class="form-control" id="rss.char" value="<?php echo config('rss.char');?>">
    </div>
  </div>
  <div class="form-group row">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary">Save config</button>
    </div>
  </div>
</form>
