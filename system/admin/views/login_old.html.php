<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<?php if (!login()) { ?>
    <h1>Login</h1>
    <form method="POST" action="login">
        User <span class="required">*</span> <br>
        <input type="text" class="<?php if (isset($username)) {
            if (empty($username)) {
                echo 'error';
            }
        } ?>" name="user"/><br><br>
        Password <span class="required">*</span> <br>
        <input type="password" class="<?php if (isset($password)) {
            if (empty($password)) {
                echo 'error';
            }
        } ?>" name="password"/><br><br>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <?php if (config('google.reCaptcha') === 'true'): ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="g-recaptcha" data-sitekey="<?php echo config("google.reCaptcha.public"); ?>"></div>
            <br/>
        <?php endif; ?>
        <input type="submit" name="submit" value="Login"/>
    </form>
<?php } else {
    header('location: admin');
} ?>