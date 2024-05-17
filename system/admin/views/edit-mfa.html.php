<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($_SESSION[site_url()]['user'])) {
    $user = $_SESSION[site_url()]['user'];
}

	use PragmaRX\Google2FA\Google2FA;
	use BaconQrCode\Renderer\GDLibRenderer;
	use BaconQrCode\Writer;

if (user('mfa_secret', $user) == 'disabled') {
	$google2fa = new Google2FA();
	$mfasecret = $google2fa->generateSecretKey();

	$g2faUrl = $google2fa->getQRCodeUrl(
		$user,
		site_url(),
		$mfasecret
	);

	$renderer = new GDLibRenderer(400);
	$writer = new Writer($renderer);

	$qrcode_image = base64_encode($writer->writeString($g2faUrl));
}
?>
<h2><?php echo i18n('config_mfa'); echo ': ' . $user; ?></h2>
<br>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
    <input type="hidden" name="username" value="<?php echo $user; ?>">
	<?php if (user('mfa_secret', $user) == 'disabled') {
		echo '<div style="text-align:center;width:100%;"><img style="margin:-10px auto;" src="data:image/png;base64, '.$qrcode_image.' "/></div>
			<span style="text-align:center;width:100%;float:left;"><small>'.i18n('manualsetupkey').': '.$mfasecret.'</small></span>
			<div class="form-group row">
				<label for="site.url" class="col-sm-2 col-form-label">'.i18n('MFACode').'</label>
				<div class="col-sm-10">
				  <input type="text" name="mfacode" class="form-control" id="mfacode" value="" placeholder="'.i18n('verify_code').'">
				</div>
			</div>
			<div class="form-group row">
				<label for="site.url" class="col-sm-2 col-form-label">'.i18n('Password').'</label>
				<div class="col-sm-10">
				  <input type="password" name="password" class="form-control" id="password" value="" placeholder="'.i18n('verify_password').'">
				</div>
			</div>
			<input type="hidden" name="mfa_secret" value="'.$mfasecret.'">
			<input type="submit" class="btn btn-primary" style="width:100px;" value="'.i18n('Save').'">';
	} else {
		echo '<input type="hidden" name="mfa_secret" value="disabled">	
			<div class="form-group row">
				<label for="site.url" class="col-sm-2 col-form-label">'.i18n('Password').'</label>
				<div class="col-sm-10">
				  <input type="password" name="password" class="form-control" id="password" value="" placeholder="'.i18n('verify_password').'">
				</div>
			</div>
			<input type="submit" class="btn btn-primary" style="width:100px;" value="'.i18n('disablemfa').'">';
	} ?>	
</form>