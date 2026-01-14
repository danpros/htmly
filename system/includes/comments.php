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
        $url = get_url_from_file($file);
        foreach ($comments as $comment) {
            $comment['file'] = $file;
            $comment['url'] = $url;
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


function getPublishedComments($limit = 5)
{
    $comments = array();
    $counter = 0;
    $allComments = getAllComments();
    foreach ($allComments as $comment) {
        if ($comment['published'] == 1) {
            $comments[] = $comment;
        }
        if (count($comments) >= $limit) break;
    }
    return $comments;
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
 * Calculate seconds difference from now
 *
 * @param int/string $timestamp
 * @return difference in seconds
 */
function secondsGenerationSubmit($timestamp) {
    if (!is_numeric($timestamp)) {
        return null; // invalid value
    }

    $timestampJS = (int) $timestamp;
    $timestampServer = time();

    return $timestampServer - $timestampJS;
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

    // Validate js and time (if enabled) - minimum 2 seconds, maximum 600 seconds
    if (comments_config('comments.jstime') === 'true') {
        if (!$data['company'] || secondsGenerationSubmit($data['company']) < 3 || secondsGenerationSubmit($data['company']) > 3600) {
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
 * @param string $url or $mdfile (the content md file) - if mdfile not provided falls back using url
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
    
    // Subscription handling
    if ($comment['notify']) {
        setSubscription($comment['email'], 'subscribe');
    }

    // Clearing cache if comment is published, otherwise doesn't display on page
    if ($comment['published']) {
        rebuilt_cache('all');
        clear_cache();
    }


    // Send notifications - notify admin always, notify subscribers only if published
    sendCommentNotifications($url, $comment, $comments, true, $comment['published']);

    return array(
        'success' => true,
        'comment_id' => $commentId,
        'message' => $comment['published'] ? 'comment_submission_success' : 'comment_submission_moderation'
    );
}



// action can be subscribe, confirm, unsubscribe
function setSubscription($email, $action) {
    $subscriptions_dir = 'content/comments/.subscriptions';
    if (!is_dir($subscriptions_dir)) {
        mkdir($subscriptions_dir);
    }
    $subscription_file = $subscriptions_dir . '/' . encryptEmailForFilename($email, comments_config('comments.salt'));
    
    $subscription = getSubscription($email);

    if ($action == 'subscribe') {
        if ($subscription['status'] == 'subscribed') {
            return true;
        }
        elseif ($subscription['status'] == 'waiting') {
            sendSubscriptionEmail($email);
        }
        else {
            $subscription['status'] = 'waiting';
            $json = json_encode($subscription, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            file_put_contents($subscription_file, $json);
            sendSubscriptionEmail($email);
            return true;
        }

    }
    elseif ($action == 'confirm' && $subscription['status'] == 'waiting') {
        $subscription['status'] = 'subscribed';
        $json = json_encode($subscription, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($subscription_file, $json);
        return true;
    }
    elseif ($action == 'unsubscribe') {
        @unlink($subscription_file);
        return true;
    }
    else {
        // nothing here
        return false;
    }    
}


// returns array
function getSubscription($email) {
    $subscriptions_dir = 'content/comments/.subscriptions';
    $subscription_file = $subscriptions_dir . '/' . encryptEmailForFilename($email, comments_config('comments.salt'));
    if (!file_exists($subscription_file)) {
        $subscription['status'] = 'no';
        $subscription['date'] = date('Y-m-d H:i:s');
        $subscription['email'] = $email;
        return $subscription;
    }
    else {
        $subscription = json_decode(file_get_data($subscription_file), true);
        return $subscription;
    }
}



function confirmSubscription($filename) {
    $subscriptions_dir = 'content/comments/.subscriptions';
    $subscription_file = $subscriptions_dir . '/' . $filename;
    if (sanitizedSubscriptionFile($filename) && file_exists($subscription_file)) {
        $subscription = json_decode(file_get_data($subscription_file), true);
        setSubscription($subscription['email'], 'confirm');
        return true;
    }
    return false;
}


function sanitizedSubscriptionFile($filename) {
    // no path traversal, sanitizing filename
    $filename = basename($filename);
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
        return false;
    }

    $subscriptions_dir = 'content/comments/.subscriptions';
    $subscription_file = $subscriptions_dir . '/' . $filename;

    // check if path is invalid
    $real_file = realpath($subscription_file);
    $real_dir = realpath($subscriptions_dir);
    
    if ($real_file === false || $real_dir === false) {
        return false;
    }
    
    // check if path outside .subscriptions dir (we are DELETING files!)
    if (strpos($real_file, $real_dir . DIRECTORY_SEPARATOR) !== 0) {
        return false; 
    }

    return true;
    
}



function deleteSubscription($filename) {
    $subscriptions_dir = 'content/comments/.subscriptions';
    $subscription_file = $subscriptions_dir . '/' . $filename;

    if (sanitizedSubscriptionFile($filename) && file_exists($subscription_file)) {
        @unlink($subscription_file);
        return true;
    }
    return false;
}




function encryptEmailForFilename(string $email, string $secretKey) {
    // Normalize email
    $email = strtolower(trim($email));
    // Create HMAC hash
    $hash = hash_hmac('sha256', $email, $secretKey, true);

    // URL-safe Base64 (filename-safe)
    $safe = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    return $safe;
}



function sendSubscriptionEmail($email) {
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
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = i18n('comment_subscribe_confirmation') . ' '.config('blog.title');
        $mail->Body = "
            <h3>" . i18n('comment_subscribe_thread') . ": ".config('site.url')."</h3>
            <p>" . i18n('comment_subscribe_request') . " ".config('blog.title')."</p>
            <p>" . i18n('comment_subscribe_never_requested') . "</p>
            <p>" . i18n('comment_subscribe_click') . " <a href=\"".config('site.url')."?subscribe=".encryptEmailForFilename($email, comments_config('comments.salt'))."\"><b>" . i18n('comment_subscribe_here') . "</b></a> " . i18n('comment_subscribe_confirm_message') . "</p>
            <p>&nbsp;</p>
            <p>" . i18n('comment_subscribe_unsubscribe_message') . " ".config('blog.title')." " . i18n('comment_subscribe_unsubscribe_anytime') . ": <a href=\"".config('site.url')."?unsubscribe=".encryptEmailForFilename($email, comments_config('comments.salt'))."\"><b>" .  i18n('comment_unsubscribe') . "</b></a>.</p>
            <p>&nbsp;</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Subscription notification email failed: {$mail->ErrorInfo}");
        return false;
    }
}





/**
 * Publish a comment (approve from moderation)
 *
 * @param string $file comment file
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

            $url = get_url_from_file($file);

            // Send notifications only to subscribers when publishing (admin already saw it in moderation)
            sendCommentNotifications($url, $comment, $comments, false, true);
            break;
        }
    }

    if (!$updated) {
        return false;
    }

    $json = json_encode($comments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    rebuilt_cache('all');
    clear_cache();
    
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Delete a comment
 *
 * @param string $mdfile (content md file)
 * @param string $commentId Comment ID
 * @return bool Success status
 */
function commentDelete($mdfile, $commentId)
{
    $file = get_comments_file(null, $mdfile);
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

    rebuilt_cache('all');
    clear_cache();

    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Modify a comment
 *
 * @param string $file (comments json file)
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
 * @param string $url Post or page url
 * @param array $newComment The new comment
 * @param array $allComments All comments for this post
 * @param bool $notifyAdmin Notify admin (default true)
 * @param bool $notifySubscribers Notify subscribers (default true)
 * @return void
 */
function sendCommentNotifications($url, $newComment, $allComments, $notifyAdmin = true, $notifySubscribers = true)
{
    // Check if mail is enabled
    if (comments_config('comments.mail.enabled') !== 'true') {
        return;
    }

    $recipients = array();

    // Add admin email - notify if comments.notifyadmin = "true" OR comments.moderation = "true"
    if ($notifyAdmin) {
        $shouldNotifyAdmin = (comments_config('comments.notifyadmin') === 'true') ||
                            (comments_config('comments.moderation') === 'true');

        if ($shouldNotifyAdmin) {
        $adminEmail = comments_config('comments.admin.email');
        if (!empty($adminEmail) && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $recipients[$adminEmail] = array(
                'name' => 'Administrator',
                'type' => 'admin'
            );
        }
    }
    }

    // Add subscribers only if notifySubscribers is true AND comments.notify is enabled
    if ($notifySubscribers && comments_config('comments.notify') === 'true') {
    // Add parent comment author (if replying)
    if (!empty($newComment['parent_id'])) {
        foreach ($allComments as $comment) {
            if ($comment['id'] === $newComment['parent_id'] &&
                $comment['notify'] &&
                $comment['email'] !== $newComment['email']) {
                    $subscrition = getSubscription($comment['email']);
                    if ($subscrition['status'] == 'subscribed') {
                $recipients[$comment['email']] = array(
                    'name' => $comment['name'],
                    'type' => 'parent'
                );
            }
        }
    }
        }

        // Add all commenters in same thread (same JSON file) who want notifications
    foreach ($allComments as $comment) {
        if ($comment['notify'] &&
            $comment['email'] !== $newComment['email'] &&
            $comment['id'] !== $newComment['id']) {
                $subscrition = getSubscription($comment['email']);
                if ($subscrition['status'] == 'subscribed') {
                $recipients[$comment['email']] = array(
                    'name' => $comment['name'],
                    'type' => 'thread'
                );
            }
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
            if (comments_config('comments.moderation') === 'true') {
                $mail->Subject = i18n('comment_email_admin_awaiting') . " - " . config('blog.title');
            }
            else {
                $mail->Subject = i18n('comment_email_admin_new') . " - " . config('blog.title');    
            }
            $mail->Body = "
                <h3>".i18n('comment_email_new').": {$url}</h3>
                <p><strong>" . i18n('comment_email_from') . ":</strong> {$comment['name']} ({$comment['email']})</p>
                <p><strong>" . i18n('comment') . ":</strong></p>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <p><a href='" . site_url() . "admin/comments'>" . i18n('comment_email_moderate'). "</a></p>
            ";
        } else {
            $mail->Subject = i18n('comment_email_new_subscribed') . " - " . config('blog.title');
            $mail->Body = "
                <h3>" . i18n('comment_email_new_replied') .": " . site_url() . "{$url}</h3>
                <p><strong>" . i18n('comment_email_from') . ":</strong> {$comment['name']}</p>
                <p><strong>" . i18n('comment') . ":</strong></p>
                <p>" . nl2br(htmlspecialchars($comment['comment'])) . "</p>
                <p><a href='" . site_url() . "{$url}#comment-{$comment['id']}'>" . i18n('comment_email_view_comment') . "</a></p>
                <p>&nbsp;</p>
                <p>" . i18n('comment_subscribe_unsubscribe_message') . " ".config('blog.title')." " . i18n('comment_subscribe_unsubscribe_anytime') . ": <a href=\"".config('site.url')."?unsubscribe=".encryptEmailForFilename($to, comments_config('comments.salt'))."\"><b>" .  i18n('comment_unsubscribe') . "</b></a>.</p>
                <p>&nbsp;</p>
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
 * @param string $url Post or page url
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




if (isset($_GET['subscribe'])) {
    confirmSubscription($_GET['subscribe']);
}

if (isset($_GET['unsubscribe'])) {
    deleteSubscription($_GET['unsubscribe']);
}



?>