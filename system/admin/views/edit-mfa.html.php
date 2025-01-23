<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($_SESSION[site_url()]['user'])) {
    $user = $_SESSION[site_url()]['user'];
}

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

$domain = parse_url(site_url());
$domain = rtrim($domain['host'] . $domain['path'], "/");
$mfa_state = user('mfa_secret', $user);

if (is_null($mfa_state) || $mfa_state == 'disabled') {
    
    $google2fa = new Google2FA();
    $mfasecret = $google2fa->generateSecretKey();
    
    if (version_compare(PHP_VERSION, '8.1', '>=')) {

        $g2faUrl = $google2fa->getQRCodeUrl(
            $user,
            $domain,
            $mfasecret
        );

        $renderer = new GDLibRenderer(400);
        $writer = new Writer($renderer);

        $qrcode_image = base64_encode($writer->writeString($g2faUrl));
    }
}
?>
<h2><?php echo i18n('config_mfa'); echo ': ' . $user; ?></h2>
<br>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <input type="hidden" name="username" value="<?php echo $user; ?>">
    <?php if (is_null($mfa_state) || $mfa_state == 'disabled') {?>
        <?php if (version_compare(PHP_VERSION, '8.1', '>=')) {?>
        <div style="text-align:center;width:100%;"><img style="margin:-10px auto;" src="data:image/png;base64, <?php echo $qrcode_image; ?>"/></div>
        <span style="text-align:center;width:100%;float:left;"><small><?php echo i18n('manualsetupkey') . ': ' . $mfasecret; ?></small></span>
        <?php } else {?>
        <span style="text-align:center;width:100%;float:left;"><small>Setup Key</small></span>
        <span style="text-align:center;width:100%;float:left;"><h4><?php echo $mfasecret;?></h4></span>
        <br><br>
        <?php } ?>
        <div class="form-group row">
            <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('MFACode');?></label>
            <div class="col-sm-10">
              <input type="text" name="mfacode" class="form-control" id="mfacode" value="" placeholder="<?php echo i18n('verify_code');?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Password');?></label>
            <div class="col-sm-10">
              <input type="password" name="password" class="form-control" id="password" value="" placeholder="<?php echo i18n('verify_password');?>">
            </div>
        </div>
        <input type="hidden" name="mfa_secret" value="<?php echo $mfasecret;?>">
        <input type="submit" class="btn btn-primary" style="width:100px;" value="<?php echo i18n('Save');?>">
    <?php } else { ?>
        <input type="hidden" name="mfa_secret" value="disabled">    
            <div class="form-group row">
                <label for="site.url" class="col-sm-2 col-form-label"><?php echo i18n('Password');?></label>
                <div class="col-sm-10">
                  <input type="password" name="password" class="form-control" id="password" value="" placeholder="<?php echo i18n('verify_password');?>">
                </div>
            </div>
            <input type="submit" class="btn btn-primary" value="<?php echo i18n('disablemfa');?>">
    <?php } ?>
</form>
