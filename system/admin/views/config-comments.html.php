<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Comments_Settings');?></h2>
<br>
<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link" id="nav-general-tab" href="<?php echo site_url();?>admin/config"><?php echo i18n('General');?></a>
    <a class="nav-item nav-link" id="nav-profile-tab" href="<?php echo site_url();?>admin/config/reading"><?php echo i18n('Reading');?></a>
    <a class="nav-item nav-link" id="nav-writing-tab" href="<?php echo site_url();?>admin/config/writing"><?php echo i18n('Writing');?></a>
    <a class="nav-item nav-link" id="nav-widget-tab" href="<?php echo site_url();?>admin/config/widget"><?php echo i18n('Widget');?></a>
    <a class="nav-item nav-link" id="nav-metatags-tab" href="<?php echo site_url();?>admin/config/metatags"><?php echo i18n('Metatags');?></a>
    <a class="nav-item nav-link" id="nav-security-tab" href="<?php echo site_url();?>admin/config/security"><?php echo i18n('Security');?></a>
    <?php if(config('comment.system') === 'local') { ?>
    <a class="nav-item nav-link active" id="nav-comments-tab" href="<?php echo site_url();?>admin/config/comments"><?php echo i18n('comments');?></a>
    <?php } ?>
    <a class="nav-item nav-link" id="nav-performance-tab" href="<?php echo site_url();?>admin/config/performance"><?php echo i18n('Performance');?></a>
    <a class="nav-item nav-link" id="nav-custom-tab" href="<?php echo site_url();?>admin/config/custom"><?php echo i18n('Custom');?></a>
  </div>
</nav>
<br><br>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">
<h4><?php echo i18n('General_Settings');?></h4>
<hr>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Comment_Moderation');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="hidden" name="-config-comments_moderation" value="false">
            <input type="checkbox" class="form-check-input" name="-config-comments_moderation" value="true"
                   <?php echo config('comments.moderation') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Require_admin_approval');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('Comments_moderation_desc');?></small>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Anti_Spam_Protection');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="hidden" name="-config-comments_honeypot" value="false">
            <input type="checkbox" class="form-check-input" name="-config-comments_honeypot" value="true"
                   <?php echo config('comments.honeypot') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Enable_honeypot');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('Honeypot_desc');?></small>
            
        <div class="form-check">
            <input type="hidden" name="-config-comments_jstime" value="false">
            <input type="checkbox" class="form-check-input" name="-config-comments_jstime" value="true"
                   <?php echo config('comments.jstime') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('enable_jstime');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('jstime_desc');?></small>
    </div>
</div>

<h4><?php echo i18n('Email_Notifications');?></h4>
<hr>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Enable_Notifications');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="hidden" name="-config-comments_notify" value="false">
            <input type="checkbox" class="form-check-input" name="-config-comments_notify" value="true"
                   <?php echo config('comments.notify') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Send_email_notifications');?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="admin-email" class="col-sm-3 col-form-label"><?php echo i18n('Admin_Email');?></label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="admin-email" name="-config-comments_admin_email"
               value="<?php echo _h(config('comments.admin.email')); ?>"
               placeholder="admin@example.com">
        <small class="form-text text-muted"><?php echo i18n('Admin_email_desc');?></small>
    </div>
</div>

<h4><?php echo i18n('SMTP_Settings');?></h4>
<hr>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Enable_SMTP');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="hidden" name="-config-comments_mail_enabled" value="false">
            <input type="checkbox" class="form-check-input" name="-config-comments_mail_enabled" value="true"
                   <?php echo config('comments.mail.enabled') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Enable_SMTP_for_emails');?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="mail-host" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Host');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-host" name="-config-comments_mail_host"
               value="<?php echo _h(config('comments.mail.host')); ?>"
               placeholder="smtp.gmail.com">
    </div>
</div>

<div class="form-group row">
    <label for="mail-port" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Port');?></label>
    <div class="col-sm-9">
        <input type="number" class="form-control" id="mail-port" name="-config-comments_mail_port"
               value="<?php echo _h(config('comments.mail.port')); ?>"
               placeholder="587">
        <small class="form-text text-muted">587 (TLS) or 465 (SSL)</small>
    </div>
</div>

<div class="form-group row">
    <label for="mail-encryption" class="col-sm-3 col-form-label"><?php echo i18n('Encryption');?></label>
    <div class="col-sm-9">
        <select class="form-control" id="mail-encryption" name="-config-comments_mail_encryption">
            <option value="tls" <?php echo config('comments.mail.encryption') === 'tls' ? 'selected' : ''; ?>>TLS</option>
            <option value="ssl" <?php echo config('comments.mail.encryption') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label for="mail-username" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Username');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-username" name="-config-comments_mail_username"
               value="<?php echo _h(config('comments.mail.username')); ?>"
               placeholder="your-email@gmail.com" autocomplete="off">
    </div>
</div>

<div class="form-group row">
    <label for="mail-password" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Password');?></label>
    <div class="col-sm-9">
        <input type="password" class="form-control" id="mail-password" name="-config-comments_mail_password"
               value="<?php echo _h(config('comments.mail.password')); ?>"
               placeholder="<?php echo i18n('Enter_password');?>" autocomplete="off">
    </div>
</div>

<div class="form-group row">
    <label for="mail-from-email" class="col-sm-3 col-form-label"><?php echo i18n('From_Email');?></label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="mail-from-email" name="-config-comments_mail_from_email"
               value="<?php echo _h(config('comments.mail.from.email')); ?>"
               placeholder="noreply@example.com">
    </div>
</div>

<div class="form-group row">
    <label for="mail-from-name" class="col-sm-3 col-form-label"><?php echo i18n('From_Name');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-from-name" name="-config-comments_mail_from_name"
               value="<?php echo _h(config('comments.mail.from.name')); ?>"
               placeholder="<?php echo config('blog.title'); ?>">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Settings');?></button>
    </div>
</div>
</form>