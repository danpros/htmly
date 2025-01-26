<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Custom_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link active" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>  
</nav>
<br><br>
<p><?php echo i18n('hint_Use_CtrlCMDF_to_search_for_your_config_key_or_value');?></p>
<p><?php echo i18n('pro_tips_You_can_create_custom_config_key_and_print_out_your_config_key_value_anywhere_in_your_template');?></p>
<p><code>&lt;?php echo config('<?php echo i18n('your_key');?>'); ?&gt;</code></p>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <table class="table" id="config">
        <tr>
            <td><input type="text" class="form-control" name="newKey" placeholder="<?php echo i18n('Your_New_Config_Key');?>"></td>
            <td><input type="text" class="form-control" name="newValue" placeholder="<?php echo i18n('Your_New_Value');?>"></td>
        </tr>
        <?php
        global $config_file;
        $array = array();
        if (file_exists($config_file)) {
            $array = parse_ini_file($config_file, true);
        }
        $configList = json_decode(file_get_contents('system/configList.json', true));
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
    <input type="submit" class="form-control btn-primary btn-sm" style="width:100px;" value="<?php echo i18n('Save');?>">
</form>
