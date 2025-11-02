<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
$theme = $theme;
$configPath = 'themes/' . $theme . '/theme.json';
$theme_path = 'themes/' . $theme;
$image = ($m = glob($theme_path . '/screenshot.*')) ? site_url() . $m[0] : site_url() . 'system/resources/images/default-screenshot.jpg';
$themeConfig = array();

if (file_exists($configPath)) {
    $json = file_get_contents($configPath);
    $themeConfig = json_decode($json, true);
}
?>
<?php if (!empty($themeConfig)): ?>
<div class="row">
    <div class="col">
        <div class="mb-3">
            <img class="card-img-top" style="object-fit: cover;" height="400px" src="<?php echo $image;?>"/>
        </div>
    </div>
    <div class="col">
        <div class="mb-3">
            <strong><?php echo i18n('name');?>:</strong>
            <div><?php echo $themeConfig['name'] ?: $theme; ?></div>
        </div>

        <div class="mb-3">
            <strong><?php echo i18n('version');?>:</strong>
            <div><?php echo $themeConfig['version'] ?: HTMLY_VERSION; ?></div>
        </div>

        <div class="mb-3">
            <strong><?php echo i18n('author');?>:</strong>
            <div><?php echo $themeConfig['author'] ?: 'Contributor'; ?></div>
        </div>
        
        <div class="mb-3">
            <strong><?php echo i18n('homepage');?>:</strong>
            <div><a target="_blank" href="<?php echo $themeConfig['homepage'] ?: site_url(); ?>"><?php echo $themeConfig['homepage'] ?: site_url(); ?></a></div>
        </div>
        
        <div class="mb-3">
            <strong><?php echo i18n('description');?>:</strong>
            <div><?php echo $themeConfig['description'] ?: 'HTMLy ' . $theme; ?></div>
        </div>
        
    </div>
</div>
<br><br>
<?php if (!empty($themeConfig['settings'])): ?>
<h2><?php echo i18n('settings');?></h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <?php foreach ($themeConfig['settings'] as $setting):?>
        <div class="form-group row">
            <label class='col-sm-2 col-form-label'><?php echo $setting['label'];?></label>
            <div class="col-sm-10">
                <?php if ($setting['type'] === 'select'): ?>
                    <select class='form-control' name='-config-<?php echo $setting['name'];?>'>
                    <?php foreach ($setting['options'] as $option):?>
                        <?php $selected = $option === theme_config($setting['name']) ? 'selected' : '';?>
                        <option value='<?php echo $option;?>' <?php echo $selected;?>><?php echo $option;?></option>
                    <?php endforeach;?>
                    </select>
                <?php elseif ($setting['type'] === 'checkbox'): ?>
                    <?php $checked = theme_config($setting['name']) ? 'checked' : '';?>
                    <input type="hidden" name="-config-<?php echo $setting['name'];?>" value="0" />
                    <input type='checkbox' name='-config-<?php echo $setting['name'];?>' <?php echo $checked;?> value="1"/>
                    <br>
                <?php else: ?>
                    <input class="form-control" type="<?php echo $setting['type'];?>" name="-config-<?php echo $setting['name'];?>" value="<?php echo theme_config($setting['name']);?>"/>
                <?php endif;?>
                <small><?php echo $setting['info'];?></small>
            </div>
        </div>
        <?php endforeach;?>
        <div class="form-group row">
          <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Config');?></button>
          </div>
        </div>
</form>
<?php endif;?>
<?php endif; ?>