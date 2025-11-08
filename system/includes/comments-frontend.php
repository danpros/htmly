<?php
if (!defined('HTMLY')) die('HTMLy');

/**
 * Display comments form
 *
 * @param string $postId Post or page ID
 * @param string $parentId Parent comment ID for replies (optional)
 * @return void
 */
function displayCommentsForm($postId, $parentId = null)
{
    if (!local()) {
        return;
    }

    $formId = $parentId ? 'reply-form-' . $parentId : 'comment-form';
    $submitUrl = site_url() . 'comments/submit';
    ?>
    <form id="<?php echo $formId; ?>" method="POST" action="<?php echo $submitUrl; ?>" class="comment-form">
        <input type="hidden" name="post_id" value="<?php echo _h($postId); ?>">
        <?php if ($parentId): ?>
        <input type="hidden" name="parent_id" value="<?php echo _h($parentId); ?>">
        <?php endif; ?>

        <!-- Honeypot field (hidden from users) -->
        <div style="position:absolute;left:-5000px;" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" value="" autocomplete="off">
        </div>


        <div class="form-group" style="width: 100%">
            <label for="name-<?php echo $formId; ?>"><?php echo i18n('Name'); ?> <span class="required">*</span></label>
            <input type="text" class="form-control" id="name-<?php echo $formId; ?>" name="name" required>

            <label for="email-<?php echo $formId; ?>"><?php echo i18n('Email'); ?> <span class="required">*</span></label>
            <input type="email" class="form-control" id="email-<?php echo $formId; ?>" name="email" required>
            <br><small class="form-text text-muted"><?php echo i18n('Email_not_published'); ?></small>
        </div>
<br clear="all">
        <div class="form-group">
            <label for="comment-<?php echo $formId; ?>"><?php echo i18n('Comment'); ?> <span class="required">*</span></label>
            <textarea class="form-control" id="comment-<?php echo $formId; ?>" name="comment" rows="5" required></textarea>
            <small class="form-text text-muted"><?php echo i18n('Comment_formatting_help'); ?></small>
        </div>
<!-- Emidio 20251105 - temporarily disabled
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="notify-<?php echo $formId; ?>" name="notify" value="1">
            <label class="form-check-label" for="notify-<?php echo $formId; ?>">
                <?php echo i18n('Notify_new_comments'); ?>
            </label>
        </div>
-->
        <br>
        <div class="form-group">
            <button type="submit" class="btn btn-primary submit-comment"><?php echo $parentId ? i18n('Post_Reply') : i18n('Post_Comment'); ?></button>
            <?php if ($parentId): ?>
            <button type="button" class="btn btn-secondary cancel-reply" onclick="cancelReply('<?php echo $parentId; ?>')"><?php echo i18n('Cancel'); ?></button>
            <?php endif; ?>
        </div>
    </form>
    <?php
}

/**
 * Display single comment
 *
 * @param array $comment Comment data
 * @param string $postId Post ID
 * @return void
 */
function displayComment($comment, $postId)
{
    $indent = isset($comment['level']) ? $comment['level'] : 0;
    $marginLeft = $indent * 0; // 40px per level - changed to 0 Emidio 20251106

    // Add visual depth indicator
    $depthClass = 'comment-level-' . min($indent, 5); // Max 5 for styling
    $borderColor = $indent > 0 ? '#ddd' : '#007bff';
    ?>
    <div id="comment-<?php echo $comment['id']; ?>" class="comment-item <?php echo $depthClass; ?>"
         style="margin-left: <?php echo $marginLeft; ?>px; border-left: 3px solid <?php echo $borderColor; ?>;"
         data-level="<?php echo $indent; ?>">
        <div class="comment-header">
            <strong class="comment-author"><?php echo _h($comment['name']); ?></strong>
            <span class="comment-date"><?php echo format_date($comment['timestamp']); ?></span>
            <!---
            <?php if ($indent > 0): ?>
            <span class="comment-level-badge"><?php echo i18n('Level'); ?> <?php echo $indent; ?></span>
            <?php endif; ?>
            --->
        </div>
        <div class="comment-body">
            <?php echo formatCommentText($comment['comment']); ?>
        </div>
        <div class="comment-footer">
            <button class="btn btn-sm btn-link reply-button" onclick="showReplyForm('<?php echo $comment['id']; ?>', '<?php echo $postId; ?>')">
                <i class="fa fa-reply"></i> <?php echo i18n('Reply'); ?>
            </button>
        </div>
        <div id="reply-container-<?php echo $comment['id']; ?>" class="reply-container" style="display:none; margin-top:15px;">
            <!-- Reply form will be inserted here via JavaScript -->
        </div>

        <?php
        // Display child comments (recursive - unlimited depth)
        if (!empty($comment['children'])) {
            echo '<div class="comment-children">';
            foreach ($comment['children'] as $child) {
                displayComment($child, $postId);
            }
            echo '</div>';
        }
        ?>
    </div>
    <?php
}

/**
 * Display all comments for a post
 *
 * @param string $postId Post or page ID
 * @return void
 */
function displayComments($postId)
{
    if (!local()) {
        return;
    }

    $comments = getComments($postId);

    if (empty($comments)) {
        return;
    }

    // Build comment tree
    $commentTree = buildCommentTree($comments);

    ?>
    <div class="comments-list">
        <!--- <h4><?php echo i18n('Comments'); ?> (<?php echo count($comments); ?>)</h4> --->
        <?php
        foreach ($commentTree as $comment) {
            displayComment($comment, $postId);
        }
        ?>
    </div>
    <?php
}

/**
 * Display complete comments section (list + form)
 *
 * @param string $postId Post or page ID
 * @return void
 */
function displayCommentsSection($postId)
{
    if (!local()) {
        return;
    }
    ?>
    <section class="comments comment-box" id="comments">
        <!---
        <div class="comments-number">
            <h3><?php echo i18n("Comments"); ?></h3>
        </div>
        --->

        <?php
        // Show success/error messages
        $hash = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_FRAGMENT) : '';
        if ($hash === 'comment-success'):
        ?>
        <div class="alert alert-success">
            <?php
            if (comments_config('comments.moderation') === 'true') {
                echo i18n('Comment_submitted_moderation');
            } else {
                echo i18n('Comment_submitted_success');
            }
            ?>
        </div>
        <?php elseif ($hash === 'comment-error'): ?>
        <div class="alert alert-danger">
            <?php echo i18n('Comment_submission_error'); ?>
        </div>
        <?php endif; ?>

        <?php displayComments($postId); ?>

        <div class="comment-form-section">
            <h4><?php echo i18n('Leave_a_comment'); ?></h4>
            <?php displayCommentsForm($postId); ?>
        </div>
    </section>

    <script>
    function showReplyForm(commentId, postId) {
        // Hide all other reply forms
        document.querySelectorAll('.reply-container').forEach(function(el) {
            el.style.display = 'none';
            el.innerHTML = '';
        });

        // Show this reply form
        var container = document.getElementById('reply-container-' + commentId);
        if (container) {
            container.style.display = 'block';

            // Build form HTML
            var submitUrl = '<?php echo site_url(); ?>comments/submit';
            var formId = 'reply-form-' + commentId;

            var formHtml = '<form id="' + formId + '" method="POST" action="' + submitUrl + '" class="comment-form">' +
                '<input type="hidden" name="post_id" value="' + postId + '">' +
                '<input type="hidden" name="parent_id" value="' + commentId + '">' +
                '<div style="position:absolute;left:-5000px;" aria-hidden="true">' +
                '<input type="text" name="website" tabindex="-1" value="" autocomplete="off">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="name-' + formId + '"><?php echo i18n("Name"); ?> <span class="required">*</span></label>' +
                '<input type="text" class="form-control" id="name-' + formId + '" name="name" required>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="email-' + formId + '"><?php echo i18n("Email"); ?> <span class="required">*</span></label>' +
                '<input type="email" class="form-control" id="email-' + formId + '" name="email" required>' +
                '<small class="form-text text-muted"><?php echo i18n("Email_not_published"); ?></small>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="comment-' + formId + '"><?php echo i18n("Comment"); ?> <span class="required">*</span></label>' +
                '<textarea class="form-control" id="comment-' + formId + '" name="comment" rows="5" required></textarea>' +
                '<small class="form-text text-muted"><?php echo i18n("Comment_formatting_help"); ?></small>' +
                '</div>' +
                '<!-- Emidio 20251105 - temporarily disabled ' +
                '<div class="form-group form-check">' +
                '<input type="checkbox" class="form-check-input" id="notify-' + formId + '" name="notify" value="1">' +
                '<label class="form-check-label" for="notify-' + formId + '"><?php echo i18n("Notify_new_comments"); ?></label>' +
                '</div>' +
                ' -->' +
                '<br><div class="form-group">' +
                '<button type="submit" class="btn btn-primary submit-reply"><?php echo i18n("Post_Reply"); ?></button> ' +
                '<button type="button" class="btn btn-secondary cancel-reply" onclick="cancelReply(\'' + commentId + '\')"><?php echo i18n("Cancel"); ?></button>' +
                '</div>' +
                '</form>';

            container.innerHTML = formHtml;
        }
    }

    function cancelReply(commentId) {
        var container = document.getElementById('reply-container-' + commentId);
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    }
    </script>
    <?php
}

?>