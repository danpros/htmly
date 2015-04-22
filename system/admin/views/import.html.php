<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<h1>Import RSS Feed 2.0</h1>
<p>By using this importer you are agree if the feed is yours, or at least you have the authority to publish it.</p>
<form method="POST">
    Feed Url <span class="required">*</span> <br><input type="url" class="text <?php if (isset($url)) {
        if (empty($url)) {
            echo 'error';
        }
    } ?>" name="url"/><br><br>
    Add source link (optional) <input type="checkbox" class="checkbox" name="credit" value="yes"/><br><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" name="submit" class="submit" value="Import"/>
</form>