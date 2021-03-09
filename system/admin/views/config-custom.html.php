<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Custom_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link active" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<p><u>hint:</u> Use <code>Ctrl</code>/<code>CMD</code> + <code>F</code> to search for your config key or value.</p>
<p><u>pro tips:</u> You can creating custom config key and print out your config key value anywhere in your template.</p>
<p><code>&lt;?php echo config('your.key'); ?&gt;</code></p>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <table class="table" id="config">
        <tr>
            <td><input type="text" class="form-control" name="newKey" placeholder="Your New Config Key"></td>
            <td><input type="text" class="form-control" name="newValue" placeholder="Your New Value"></td>
        </tr>
        <?php
        global $config_file;
        $array = array();
        if (file_exists($config_file)) {
            $array = parse_ini_file($config_file, true);
        }
        $configList = json_decode(file_get_contents('content/data/configList.json', true));
        foreach ($array as $key => $value) {
            if (!in_array($key, $configList)) {
                echo '<tr>';
                echo '<td><label for="' . $key . '">' . $key . '</label></td>';
                echo '<td><input class="form-control" type="text" id="' . $key . '" name="-config-' . $key . '" value="' . valueMaker($value) . '"></td>';
               echo '</tr>';
            }
        }
        ?>
    </table>
    <input type="submit" class="form-control btn-primary btn-sm" style="width:100px;">
</form>
