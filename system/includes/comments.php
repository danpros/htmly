<?php
if (!defined('HTMLY')) die('HTMLy');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Check if local comments system is enabled
 *
 * @return bool
 */
function local()
{
    return config('comment.system') === 'local';
}

/**
 * Get comments configuration value
 *
 * @param string $key Configuration key (use 'reload' to force cache reload)
 * @return mixed Configuration value or null
 */
function comments_config($key)
{
    static $_config = array();

    $config_file = 'config/comments.ini';

    // Allow cache reload
    if ($key === 'reload') {
        $_config = array();
        return null;
    }

    if (empty($_config) && file_exists($config_file)) {
        $_config = parse_ini_file($config_file, false);
    }

    return isset($_config[$key]) ? $_config[$key] : null;
}

/**
 * Save comments configuration
 *
 * @param array $data Configuration data to save
 * @return bool Success status
 */
function save_comments_config($data = array())
{
    $config_file = 'config/comments.ini';

    if (!file_exists($config_file)) {
        return false;
    }

    $string = file_get_contents($config_file);

    foreach ($data as $word => $value) {
        // Ensure null and empty values are saved as empty strings
        if ($value === null || $value === '') {
            $value = '""';
        } else {
            // Encode value
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $map = array('\r\n' => ' \n ', '\r' => ' \n ');
        $value = trim(strtr($value, $map));

        // Escape dots in the key for regex
        $escapedWord = str_replace('.', '\.', $word);

        // Try to replace existing line
        $pattern = "/^" . $escapedWord . " = .*/m";
        if (preg_match($pattern, $string)) {
            $string = preg_replace($pattern, $word . ' = ' . $value, $string);
        } else {
            // If line doesn't exist, add it at the end
            $string = rtrim($string) . "\n" . $word . ' = ' . $value . "\n";
        }
    }

    $string = rtrim($string) . "\n";
    $result = file_put_contents($config_file, $string, LOCK_EX);

    // Clear PHP opcache for this file
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($config_file, true);
    }

    // Clear cache after saving
    if ($result !== false) {
        comments_config('reload');
    }

    return $result;
}

/**
 * Get comments file path for a post/page
 * Replicates content file path inside comments folder
 *
 * @param string $url is the page url (e.g., "post/htmly-simple-comment-system" or "author/emidio")
 * @param string $file is the content markdown file (optional)
 * @return string File path (e.g., "content/comments/post/htmly-simple-comment-system.json")
 */

function get_comments_file($url, $mdfile = null)
{
    // $view -> static/main/post/profile
    if ($mdfile) {
        if (preg_match('#comments[/\\\\].+\.json#', $mdfile)) {
            if (is_file($mdfile)) {
                return $mdfile;
    }
    }
        else {
            $comments_file =  get_comments_file_from_md($mdfile);
    }
    }
    else {
        $comments_file = get_comments_file_from_url($url);
    }
    return $comments_file;
}


function get_comments_file_from_md($mdfile) {
    $file_parts = explode('_', pathinfo($mdfile, PATHINFO_FILENAME));

    if (count($file_parts) > 1) {
        $file = reset($file_parts) . '_' . end($file_parts) . '.json';
    }
    else {
        $file = reset($file_parts) . '.json';
    }

    $path = preg_replace('/content/', 'content/comments', pathinfo($mdfile, PATHINFO_DIRNAME), 1);
    return $path . '/' . $file;
}

function get_comments_file_from_url($url) {
    // not using $view actually

    // strip site_url prefix and query string if present
    $url = trim(parse_url($url, PHP_URL_PATH), '/');

    // 0. test pattern: author
    if (preg_match('#^author/([^/]+)$#', $url, $matches)) {
        $authorfile = 'content/' . $matches[1] . '/author.md';
        $file = pathinfo($authorfile, PATHINFO_FILENAME) . '.json';
        $path = preg_replace('/content/', 'content/comments', pathinfo($authorfile, PATHINFO_DIRNAME), 1);
        return $path . '/' . $file;
    }

    // 1. test pattern: /YYYY/MM/name (post with default permalink)
    if (preg_match('#^(\d{4})/(\d{2})/(.+)$#', $url, $matches)) {
        $post = find_post($matches[1], $matches[2], $matches[3]);
        if ($post && isset($post['current']->file)) {

            $file_parts = explode('_', pathinfo($post['current']->file, PATHINFO_FILENAME));

            $file = reset($file_parts) . '_' . end($file_parts) . '.json';
            $path = preg_replace('/content/', 'content/comments', pathinfo($post['current']->file, PATHINFO_DIRNAME), 1);

            return $path . '/' . $file;        }
    }

    // 2. test pattern with custom permalink /[prefix]/name
    $permalink_prefix = permalink_type();
    if ($permalink_prefix != 'default' && strpos($url, $permalink_prefix . '/') === 0) {
        $name = substr($url, strlen($permalink_prefix) + 1);
        $post = find_post(null, null, $name);
        if ($post && isset($post['current']->file)) {

            $file_parts = explode('_', pathinfo($post['current']->file, PATHINFO_FILENAME));

            $file = reset($file_parts) . '_' . end($file_parts) . '.json';
            $path = preg_replace('/content/', 'content/comments', pathinfo($post['current']->file, PATHINFO_DIRNAME), 1);

            return $path . '/' . $file;
        }
    }

    // 4. test pattern static page: /slug
    if (strpos($url, '/') === false) {
        $page = find_page($url);
        if ($page && isset($page['current']->file)) {

            $file = pathinfo($page['current']->file, PATHINFO_FILENAME) . '.json';
            $path = preg_replace('/content/', 'content/comments', pathinfo($page['current']->file, PATHINFO_DIRNAME), 1);

            return $path . '/' . $file;
        }
    }

    // 2. test pattern sub page: /parent/sub
    if (substr_count($url, '/') == 1) {
        list($parent, $sub) = explode('/', $url, 2);
        $subpage = find_subpage($parent, $sub);
        if ($subpage && isset($subpage['current']->file)) {
            
            $file = pathinfo($subpage['current']->file, PATHINFO_FILENAME) . '.json';
            $path = preg_replace('/content/', 'content/comments', pathinfo($subpage['current']->file, PATHINFO_DIRNAME), 1);

            return $path . '/' . $file;
        }
    }

    // not found
    return null;

}





/**
 * Get all comments for a post/page
 *
 * @param string $postId Post or page ID
 * @param bool $includeUnpublished Include unpublished comments (for admin)
 * @return array Comments array
 */
function getComments($url, $mdfile = null, $includeUnpublished = false)
{
    $file = get_comments_file($url, $mdfile);

    if (!file_exists($file)) {
        return array();
    }

    $content = file_get_contents($file);
    $comments = json_decode($content, true);

    if (!is_array($comments)) {
        return array();
    }

    // Filter unpublished comments if not admin view
    if (!$includeUnpublished) {
        $comments = array_filter($comments, function($comment) {
            return isset($comment['published']) && $comment['published'] === true;
        });
    }

    // Sort by date (oldest first)
    usort($comments, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    return $comments;
}

/**
 * Get all comments across all posts (for admin)
 *
 * @param int $page Page number (default: null for all comments)
 * @param int $perpage Comments per page (default: null for all comments)
 * @return array All comments with post info, or array with paginated comments and total count
 */

function getAllComments($page = null, $perpage = null)
{
    $commentsDir = 'content/comments/';
    if (!is_dir($commentsDir)) {
        return array();
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($commentsDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $files = array();
    foreach ($iterator as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'json') {
            // $files[] = $file->getPathname();
            $files[] = str_replace('\\', '/', $file->getPathname());
        }
    }

    $allComments = array();


    foreach ($files as $file) {
        $comments = getComments('', $file, true);
        foreach ($comments as $comment) {
            $comment['file'] = $file;
            $allComments[] = $comment;
        }
    }

    // Sort by date (newest first)
    usort($allComments, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

    // If pagination parameters are provided, return paginated results
    if ($page !== null && $perpage !== null) {
        $total = count($allComments);
        $offset = ($page - 1) * $perpage;
        $paginatedComments = array_slice($allComments, $offset, $perpage);
        return array($paginatedComments, $total);
    }

    return $allComments;
}

/**
 * Generate unique comment ID
 *
 * @return string Unique comment ID
 */
function generateCommentId()
{
    return uniqid('comment_', true);
}

/**
 * Build comment tree for nested display
 *
 * @param array $comments Flat array of comments
 * @param string $parentId Parent comment ID (null for root)
 * @param int $level Nesting level
 * @return array Tree structure
 */
function buildCommentTree($comments, $parentId = null, $level = 0)
{
    $tree = array();

    foreach ($comments as $comment) {
        $commentParent = isset($comment['parent_id']) ? $comment['parent_id'] : null;

        if ($commentParent === $parentId) {
            $comment['level'] = $level;
            $comment['children'] = buildCommentTree($comments, $comment['id'], $level + 1);
            $tree[] = $comment;
        }
    }

    return $tree;
}

/**
 * Validate comment data
 *
 * @param array $data Comment data
 * @return array Array with 'valid' boolean and 'errors' array
 */
function validateComment($data)
{
    $errors = array();

    // Validate name
    if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
        $errors[] = 'comment_submission_error_shortname';
    }

    // Validate email
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'comment_submission_error_email';
    }

    // Validate comment text
    if (empty($data['comment']) || strlen(trim($data['comment'])) < 3) {
        $errors[] = 'comment_submission_error_short';
    }

    // Validate honeypot (if enabled)
    if (comments_config('comments.honeypot') === 'true') {
        if (!empty($data['website'])) {
            $errors[] = 'comment_submission_error_spam';
        }
    }

    return array(
        'valid' => empty($errors),
        'errors' => $errors
    );
}

/**
 * Insert a new comment
 *
 * @param string $postId Post or page ID
 * @param array $data Comment data (name, email, comment, parent_id, notify)
 * @return array Result with 'success' boolean and 'message' or 'comment_id'
 */
function commentInsert($data, $url, $mdfile = null)
{
    // Validate comment
    $validation = validateComment($data);
    if (!$validation['valid']) {
        return array(
            'success' => false,
            'message' => implode(', ', $validation['errors'])
        );
    }

    // Get existing comments
    $file = get_comments_file($url, $mdfile = null);
    $comments = array();
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $comments = json_decode($content, true);
        if (!is_array($comments)) {
            $comments = array();
        }
    }

    // Create new comment
    $commentId = generateCommentId();
    $timestamp = time();

    $comment = array(
        'id' => $commentId,
        'name' => trim($data['name']),
        'email' => trim($data['email']),
        'comment' => trim($data['comment']),
        'timestamp' => $timestamp,
        'date' => date('Y-m-d H:i:s', $timestamp),
        'parent_id' => isset($data['parent_id']) && !empty($data['parent_id']) ? $data['parent_id'] : null,
        'notify' => isset($data['notify']) && $data['notify'] === '1',
        'published' => comments_config('comments.moderation') !== 'true', // Auto-publish if moderation disabled
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    );

    // Add comment to array
    $comments[] = $comment;

    // Save to file
    $json = json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    // Create path if not exists
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0755, true); // true = recursively
    }
    if (file_put_contents($file, $json, LOCK_EX) === false) {
        return array(
            'success' => false,
            'message' => 'comment_submission_error'
        );
    }

    // Send notifications
    sendCommentNotifications($url, $comment, $comments);

    return array(
        'success' => true,
        'comment_id' => $commentId,
        'message' => $comment['published'] ? 'comment_submission_success' : 'comment_submission_moderation'
    );
}

/**
 * Publish a comment (approve from moderation)
 *
 * @param string $postId Post or page ID
 * @param string $commentId Comment ID
 * @return bool Success status
 */
function commentPublish($file, $commentId)
{

    if (!file_exists($file)) {
        return false;
    }

    $content = file_get_contents($file);
    $comments = json_decode($content, true);

    if (!is_array($comments)) {
        return false;
    }

    $updated = false;
    foreach ($comments as &$comment) {
        if ($comment['id'] === $commentId) {
            $comment['published'] = true;
            $updated = true;

            // Send notifications to other commenters
            sendCommentNotifications($comment, $comments, false);
            break;
        }
    }

    if (!$updated) {
        return false;
    }

    $json = json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Delete a comment
 *
 * @param string $postId Post or page ID
 * @param string $commentId Comment ID
 * @return bool Success status
 */
function commentDelete($url, $mdfile=null, $commentId)
{
    $file = get_comments_file($url, $mdfile=null);
    if (!file_exists($file)) {
        return false;
    }

    $content = file_get_contents($file);
    $comments = json_decode($content, true);

    if (!is_array($comments)) {
        return false;
    }

    // Remove comment and its children
    $comments = array_filter($comments, function($comment) use ($commentId) {
        return $comment['id'] !== $commentId &&
               (empty($comment['parent_id']) || $comment['parent_id'] !== $commentId);
    });

    // Reindex array
    $comments = array_values($comments);

    $json = json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Modify a comment
 *
 * @param string $postId Post or page ID
 * @param string $commentId Comment ID
 * @param array $data New comment data
 * @return bool Success status
 */
function commentModify($file, $commentId, $data)
{
    if (!file_exists($file)) {
        return false;
    }

    $content = file_get_contents($file);
    $comments = json_decode($content, true);

    if (!is_array($comments)) {
        return false;
    }

    $updated = false;
    foreach ($comments as &$comment) {
        if ($comment['id'] === $commentId) {
            // Update fields
            if (isset($data['name'])) {
                $comment['name'] = trim($data['name']);
            }
            if (isset($data['email'])) {
                $comment['email'] = trim($data['email']);
            }
            if (isset($data['comment'])) {
                $comment['comment'] = trim($data['comment']);
            }
            if (isset($data['published'])) {
                $comment['published'] = (bool)$data['published'];
            }

            $comment['modified'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        return false;
    }

    $json = json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Send comment notifications
 *
 * @param string $postId Post or page ID
 * @param array $newComment The new comment
 * @param array $allComments All comments for this post
 * @param bool $notifyAdmin Notify admin (default true)
 * @return void
 */
function sendCommentNotifications($url, $newComment, $allComments, $notifyAdmin = true)
{
    // TODO: function to be fixed, still using postId variable
    
    // Check if notifications are enabled
    if (comments_config('comments.notify') !== 'true' ||
        comments_config('comments.mail.enabled') !== 'true') {
        return;
    }

    $recipients = array();

    // Add admin email
    if ($notifyAdmin) {
        $adminEmail = comments_config('comments.admin.email');
        if (!empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $recipients[$adminEmail] = array(
                'name' => 'Administrator',
                'type' => 'admin'
            );
        }
    }

    // Add parent comment author (if replying)
    if (!empty($newComment['parent_id'])) {
        foreach ($allComments as $comment) {
            if ($comment['id'] === $newComment['parent_id'] &&
                $comment['notify'] &&
                $comment['email'] !== $newComment['email']) {
                $recipients[$comment['email']] = array(
                    'name' => $comment['name'],
                    'type' => 'parent'
                );
            }
        }
    }

    // Add other commenters in same thread who want notifications
    foreach ($allComments as $comment) {
        if ($comment['notify'] &&
            $comment['email'] !== $newComment['email'] &&
            $comment['id'] !== $newComment['id']) {
            // Same thread = same parent or no parent
            if ($comment['parent_id'] === $newComment['parent_id']) {
                $recipients[$comment['email']] = array(
                    'name' => $comment['name'],
                    'type' => 'thread'
                );
            }
        }
    }

    // Send emails
    foreach ($recipients as $email => $info) {
        sendCommentEmail($email, $info['name'], $url, $newComment, $info['type']);
    }
}

/**
 * Send a single comment notification email
 *
 * @param string $to Recipient email
 * @param string $toName Recipient name
 * @param string $postId Post ID
 * @param array $comment Comment data
 * @param string $type Notification type (admin, parent, thread)
 * @return bool Success status
 */
function sendCommentEmail($to, $toName, $url, $comment, $type = 'admin')
{
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = comments_config('comments.mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = comments_config('comments.mail.username');
        $mail->Password = comments_config('comments.mail.password');
        $mail->Port = comments_config('comments.mail.port');

        $encryption = comments_config('comments.mail.encryption');
        if ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }

        // Recipients
        $mail->setFrom(
            comments_config('comments.mail.from.email'),
            comments_config('comments.mail.from.name')
        );
        $mail->addAddress($to, $toName);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        if ($type === 'admin') {
            $mail->Subject = 'New comment awaiting moderation';
            $mail->Body = "
                <h3>New comment on: {$url}</h3>
                <p><strong>From:</strong> {$comment['name']} ({$comment['email']})</p>
                <p><strong>Comment:</strong></p>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <p><a href='" . site_url() . "admin/comments'>Moderate comments</a></p>
            ";
        } else {
            $mail->Subject = 'New reply to your comment';
            $mail->Body = "
                <h3>Someone replied to your comment on: {$url}</h3>
                <p><strong>From:</strong> {$comment['name']}</p>
                <p><strong>Comment:</strong></p>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <p><a href='" . site_url() . "{$url}#comment-{$comment['id']}'>View comment</a></p>
            ";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Comment notification email failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Get comment count for a post
 *
 * @param string $postId Post or page ID
 * @param bool $includeUnpublished Include unpublished comments
 * @return int Comment count
 */
function getCommentsCount($url, $mdfile = null, $includeUnpublished = false)
{
    $comments = getComments($url, $mdfile, $includeUnpublished);
    return count($comments);
}

/**
 * Get pending comments count (for admin)
 *
 * @return int Pending comments count
 */
function getPendingCommentsCount()
{
    $allComments = getAllComments();
    $pending = array_filter($allComments, function($comment) {
        return !$comment['published'];
    });
    return count($pending);
}

/**
 * Format comment text (allow basic formatting)
 *
 * @param string $text Comment text
 * @return string Formatted text
 */
function formatCommentText($text)
{
    // Convert line breaks
    $text = nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));

    // Allow simple bold with **text** or __text__
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);

    return $text;
}

?>