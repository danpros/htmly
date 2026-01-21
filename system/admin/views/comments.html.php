<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Comments_Management');?></h2>
<br>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show">
    <?php echo $message['text']; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php endif; ?>

<nav>
  <div class="nav nav-tabs" id="nav-tab">
    <a class="nav-item nav-link <?php echo (!isset($tab) || $tab === 'all') ? 'active' : ''; ?>"
       href="<?php echo site_url();?>admin/comments"><?php echo i18n('All_Comments');?></a>
    <a class="nav-item nav-link <?php echo (isset($tab) && $tab === 'pending') ? 'active' : ''; ?>"
       href="<?php echo site_url();?>admin/comments/pending"><?php echo i18n('Pending_Moderation');?>
       <?php if (isset($pendingCount) && $pendingCount > 0): ?>
       <span class="badge badge-warning"><?php echo $pendingCount; ?></span>
       <?php endif; ?>
    </a>
    <a class="nav-item nav-link <?php echo (isset($tab) && $tab === 'settings') ? 'active' : ''; ?>"
       href="<?php echo site_url();?>admin/comments/settings"><?php echo i18n('Settings');?></a>
  </div>
</nav>
<br><br>

<?php if (!isset($tab) || $tab === 'all' || $tab === 'pending'): ?>
<!-- Comments List -->
<?php if (!empty($comments)): ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo i18n('Author');?></th>
            <th><?php echo i18n('Comment');?></th>
            <th><?php echo i18n('Post_Page');?></th>
            <th><?php echo i18n('Date');?></th>
            <th><?php echo i18n('Status');?></th>
            <th><?php echo i18n('Actions');?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($comments as $comment): ?>
        <tr id="comment-<?php echo $comment['id']; ?>">
            <td>
                <strong><?php echo _h($comment['name']); ?></strong><br>
                <small><?php echo _h($comment['email']); ?></small><br>
                <small class="text-muted">IP: <?php echo _h($comment['ip']); ?></small>
            </td>
            <td>
                <?php
                $preview = mb_substr($comment['comment'], 0, 100);
                echo _h($preview);
                if (mb_strlen($comment['comment']) > 100) echo '...';
                ?>
                <?php if (!empty($comment['parent_id'])): ?>
                <br><small class="text-info"><em><?php echo i18n('Reply_to_comment');?></em></small>
                <?php endif; ?>
                <?php if ($comment['notify']): ?>
                <br><small class="text-success"><em><?php echo i18n('Notifications_enabled');?></em></small>
                <?php endif; ?>
            </td>
            <td>
                <a href="<?php echo site_url() . $comment['url']; ?>" target="_blank">
                    <?php echo _h($comment['url']); ?>
                </a>
            </td>
            <td>
                <?php echo format_date($comment['timestamp']); ?>
                <?php if (isset($comment['modified'])): ?>
                <br><small class="text-muted"><?php echo i18n('Modified');?>: <?php echo $comment['modified']; ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($comment['published']): ?>
                <span class="badge badge-success"><?php echo i18n('Published');?></span>
                <?php else: ?>
                <span class="badge badge-warning"><?php echo i18n('Pending');?></span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$comment['published']): ?>
                <a class="btn btn-success btn-xs"
                   href="<?php echo site_url(); ?>admin/comments/publish/<?php echo rtrim(strtr(base64_encode($comment['file']), '+/', '-_'), '='); ?>/<?php echo $comment['id']; ?>"
                   onclick="return confirm('<?php echo i18n('Confirm_publish_comment'); ?>');">
                    <?php echo i18n('Publish');?>
                </a>
                <?php endif; ?>
                <a class="btn btn-primary btn-xs"
                   href="<?php echo site_url(); ?>admin/comments/edit/<?php echo rtrim(strtr(base64_encode($comment['file']), '+/', '-_'), '='); ?>/<?php echo $comment['id']; ?>">
                    <?php echo i18n('Edit');?>
                </a>
                <a class="btn btn-danger btn-xs"
                   href="<?php echo site_url(); ?>admin/comments/delete/<?php echo rtrim(strtr(base64_encode($comment['file']), '+/', '-_'), '='); ?>/<?php echo $comment['id']; ?>"
                   onclick="return confirm('<?php echo i18n('Confirm_delete_comment'); ?>');">
                    <?php echo i18n('Delete');?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<?php if (!isset($editComment)): ?>
<p><?php echo i18n('No_comments_found'); ?>.</p>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($comments) && (!empty($pagination['prev']) || !empty($pagination['next']))): ?>
<br>
    <div class="pager">
    <ul class="pagination">
        <?php if (!empty($pagination['prev'])) { ?>
            <li class="newer page-item"><a class="page-link" href="?page=<?php echo $page - 1 ?>" rel="prev">&#8592; <?php echo i18n('Newer');?></a></li>
        <?php } else { ?>
        <li class="page-item disabled" ><span class="page-link">&#8592; <?php echo i18n('Newer');?></span></li>
        <?php } ?>
        <li class="page-number page-item disabled"><span class="page-link"><?php echo $pagination['pagenum'];?></span></li>
        <?php if (!empty($pagination['next'])) { ?>
            <li class="older page-item" ><a class="page-link" href="?page=<?php echo $page + 1 ?>" rel="next"><?php echo i18n('Older');?> &#8594;</a></li>
        <?php } else { ?>
            <li class="page-item disabled" ><span class="page-link"><?php echo i18n('Older');?> &#8594;</span></li>
        <?php } ?>
        </ul>
    </div>
<?php endif; ?>

<?php elseif ($tab === 'settings'): ?>
<!-- Settings Form -->
<form method="POST" action="<?php echo site_url(); ?>admin/comments/settings">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">

<!-- // removed by Emidio 20251105
<div class="alert alert-info">
    <strong><?php echo i18n('Note'); ?>:</strong> <?php echo i18n('Enable_comments_in_main_config'); ?>
    <br>
    <code>config/config.ini</code> → <code>comment.system = "local"</code>
</div>
-->

<h4><?php echo i18n('General_Settings');?></h4>
<hr>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Comment_Moderation');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="comments.moderation" value="true"
                   <?php echo comments_config('comments.moderation') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Require_admin_approval');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('Comments_moderation_desc');?></small>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Anti_Spam_Protection');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="comments.honeypot" value="true"
                   <?php echo comments_config('comments.honeypot') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Enable_honeypot');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('Honeypot_desc');?></small>
            
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="comments.jstime" value="true"
                   <?php echo comments_config('comments.jstime') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Enable_jstime');?></label>
        </div>
        <small class="form-text text-muted"><?php echo i18n('Jstime_desc');?></small>
    </div>
</div>

<h4><?php echo i18n('Email_Notifications');?></h4>
<hr>

<div class="form-group row">
    <label class="col-sm-3 col-form-label"><?php echo i18n('Enable_Notifications');?></label>
    <div class="col-sm-9">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="comments.notify" value="true"
                   <?php echo comments_config('comments.notify') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Send_email_notifications');?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="admin-email" class="col-sm-3 col-form-label"><?php echo i18n('Admin_Email');?></label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="admin-email" name="comments.admin.email"
               value="<?php echo _h(comments_config('comments.admin.email')); ?>"
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
            <input type="checkbox" class="form-check-input" name="comments.mail.enabled" value="true"
                   <?php echo comments_config('comments.mail.enabled') === 'true' ? 'checked' : ''; ?>>
            <label class="form-check-label"><?php echo i18n('Enable_SMTP_for_emails');?></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label for="mail-host" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Host');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-host" name="comments.mail.host"
               value="<?php echo _h(comments_config('comments.mail.host')); ?>"
               placeholder="smtp.gmail.com">
    </div>
</div>

<div class="form-group row">
    <label for="mail-port" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Port');?></label>
    <div class="col-sm-9">
        <input type="number" class="form-control" id="mail-port" name="comments.mail.port"
               value="<?php echo _h(comments_config('comments.mail.port')); ?>"
               placeholder="587">
        <small class="form-text text-muted">587 (TLS) or 465 (SSL)</small>
    </div>
</div>

<div class="form-group row">
    <label for="mail-encryption" class="col-sm-3 col-form-label"><?php echo i18n('Encryption');?></label>
    <div class="col-sm-9">
        <select class="form-control" id="mail-encryption" name="comments.mail.encryption">
            <option value="tls" <?php echo comments_config('comments.mail.encryption') === 'tls' ? 'selected' : ''; ?>>TLS</option>
            <option value="ssl" <?php echo comments_config('comments.mail.encryption') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label for="mail-username" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Username');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-username" name="comments.mail.username"
               value="<?php echo _h(comments_config('comments.mail.username')); ?>"
               placeholder="your-email@gmail.com">
    </div>
</div>

<div class="form-group row">
    <label for="mail-password" class="col-sm-3 col-form-label"><?php echo i18n('SMTP_Password');?></label>
    <div class="col-sm-9">
        <input type="password" class="form-control" id="mail-password" name="comments.mail.password"
               value="<?php echo _h(comments_config('comments.mail.password')); ?>"
               placeholder="<?php echo i18n('Enter_password');?>">
    </div>
</div>

<div class="form-group row">
    <label for="mail-from-email" class="col-sm-3 col-form-label"><?php echo i18n('From_Email');?></label>
    <div class="col-sm-9">
        <input type="email" class="form-control" id="mail-from-email" name="comments.mail.from.email"
               value="<?php echo _h(comments_config('comments.mail.from.email')); ?>"
               placeholder="noreply@example.com">
    </div>
</div>

<div class="form-group row">
    <label for="mail-from-name" class="col-sm-3 col-form-label"><?php echo i18n('From_Name');?></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="mail-from-name" name="comments.mail.from.name"
               value="<?php echo _h(comments_config('comments.mail.from.name')); ?>"
               placeholder="<?php echo config('blog.title'); ?>">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <button type="submit" class="btn btn-primary"><?php echo i18n('Save_Settings');?></button>
    </div>
</div>

</form>
<?php endif; ?>

<?php if (isset($editComment)): ?>
<!-- Edit Comment Modal/Page -->
<h3><?php echo i18n('Edit_Comment');?></h3>
<hr>
<form method="POST" action="<?php echo site_url(); ?>admin/comments/update/<?php echo $editComment['file_encoded']; ?>/<?php echo $editComment['id']; ?>">
<input type="hidden" name="csrf_token" value="<?php echo get_csrf(); ?>">

<input type="hidden" name="url" value="<?php echo $editComment['url']; ?>">
<input type="hidden" name="file" value="<?php echo $editComment['file_encoded']; ?>">

<div class="form-group">
    <label for="edit-name"><?php echo i18n('Name');?></label>
    <input type="text" class="form-control" id="edit-name" name="name"
           value="<?php echo _h($editComment['name']); ?>" required>
</div>

<div class="form-group">
    <label for="edit-email"><?php echo i18n('Email');?></label>
    <input type="email" class="form-control" id="edit-email" name="email"
           value="<?php echo _h($editComment['email']); ?>" required>
</div>

<div class="form-group">
    <label for="edit-comment"><?php echo i18n('Comment');?></label>
    <textarea class="form-control" id="edit-comment" name="comment" rows="6" required><?php echo _h($editComment['comment']); ?></textarea>
</div>

<div class="form-group">
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="edit-published" name="published" value="1"
               <?php echo $editComment['published'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="edit-published"><?php echo i18n('Published');?></label>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary"><?php echo i18n('Update_Comment');?></button>
    <a href="<?php echo site_url(); ?>admin/comments" class="btn btn-secondary"><?php echo i18n('Cancel');?></a>
</div>

</form>
<?php endif; ?>
