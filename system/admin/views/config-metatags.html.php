<?php 
global $config_file;
$array = array();
if (file_exists($config_file)) {
  $array = parse_ini_file($config_file, true);
}
?>
<h2>Metatags</h2>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
  <div class="form-group row">
    <label class="col-sm-2 col-form-label">Permalink</label>
    <div class="col-sm-10">
      <div class="col-sm-10">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-permalink.type" id="permalink.type1" value="default" <?php if (config('permalink.type') === 'default'):?>checked<?php endif;?>>
          <label class="form-check-label" for="permalink.type1">
            /year/month/your-post-slug
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="-config-permalink.type" id="permalink.type1" value="post" <?php if (config('permalink.type') === 'post'):?>checked<?php endif;?>>
          <label class="form-check-label" for="permalink.type2">
            /post/your-post-slug
          </label>
        </div>
      </div>
	</div>
  </div>
  <div class="form-group row">
    <label for="description.char" class="col-sm-2 col-form-label">Meta description character</label>
    <div class="col-sm-10">
      <input type="number" name="-config-description.char" class="form-control" id="description.char" value="<?php echo config('description.char');?>">
    </div>
  </div>
  <br>
  <h4>Sitemap</h4>
  <hr>
  <p>Valid values range from 0.0 to 1.0. See <a target="_blank" href="https://www.sitemaps.org/protocol.html">https://www.sitemaps.org/protocol.html</a></p>
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
      <button type="submit" class="btn btn-primary">Save config</button>
    </div>
  </div>
</form>
