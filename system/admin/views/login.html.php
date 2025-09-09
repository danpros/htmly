<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<style>.error-message ul {margin:0;padding:0;}</style>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<?php if (!login()) { ?>
    <h1><?php echo i18n('Login');?></h1>
    <form method="POST" action="login">
        <label><?php echo i18n('User');?> <span class="required">*</span></label>
        <input type="text" class="form-control <?php if (isset($username)) {
            if (empty($username)) {
                echo 'error';
            }
        } ?>" name="user" placeholder="<?php echo i18n('User'); ?>"/>
        <br>
        <label><?php echo i18n('Password');?> <span class="required">*</span></label>
        <input type="password" class="form-control <?php if (isset($password)) {
            if (empty($password)) {
                echo 'error';
            }
        } ?>" name="password" placeholder="<?php echo i18n('Password'); ?>"/>
        <br>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <?php if (config('login.protect.system') === 'google'): ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="g-recaptcha" data-sitekey="<?php echo config("login.protect.public"); ?>"></div>
            <br/>
        <?php endif; ?>
        <?php if (config('login.protect.system') === 'cloudflare'): ?>
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
            <div style="text-align:center;" class="cf-turnstile" data-sitekey="<?php echo config("login.protect.public"); ?>"></div>
            <br/>
        <?php endif; ?>
        <input type="submit" class="btn btn-primary" name="submit" value="<?php echo i18n('Login');?>"/>
    </form>
<?php } else {
    header('location: admin');
} ?>
