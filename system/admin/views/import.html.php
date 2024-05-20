<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<h1><?php echo i18n('Import_RSS_Feed_2.0');?></h1>
<p><?php echo i18n('By_using_this_importer_you_confirm_that_the_feed_is_yours_or_that_at_least_you_have_the_authority_to_publish_it');?></p>
<form method="POST">
    <label><?php echo i18n('Feed_Url');?> <span class="required">*</span></label><input type="url" class="form-control text <?php if (isset($url)) {
        if (empty($url)) {
            echo 'error';
        }
    } ?>" name="url"/><br>
    <?php echo i18n('Add_source_link_optional');?> <input type="checkbox" class="checkbox" name="credit" value="yes"/><br><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Import_Feed');?>"/>
</form>
