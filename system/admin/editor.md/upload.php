<?php
require '../../includes/dispatch.php';
require '../../includes/session.php';

// Load the configuration file
config('source', '../../../config/config.ini');

// Set the timezone
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

$whitelist = array('jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif');
$name      = null;
$dir       = '../../../content/images/';
$error     = null;
$timestamp = date('YmdHis');
$path      = null;

if (login()) {

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    if (isset($_FILES) && isset($_FILES['editormd-image-file'])) {
        $tmp_name = $_FILES['editormd-image-file']['tmp_name'];
        $name     = basename($_FILES['editormd-image-file']['name']);
        $error    = $_FILES['editormd-image-file']['error'];
        $path     = $dir . $timestamp . '-' . $name;
        $success  = 0;
        $check = getimagesize($tmp_name);

        if($check !== false) {
            if ($error === UPLOAD_ERR_OK) {
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                if (!in_array(strtolower($extension), $whitelist)) {
                    $message = 'Invalid file type uploaded.';
                } else {
                    move_uploaded_file($tmp_name, $dir . $timestamp . '-' . $name);
                    $success = 1;
                }
            }
        } else {
            $message = "File is not an image.";
        }
    }else{
        die();
    }

    header('Content-Type: application/json');
    echo json_encode(array(
        'success' => $success,
        'message' => $message,
        'url' => site_url() . 'content/images/' . $timestamp . '-' . $name
    ));
} else {
    $login = site_url() . 'login';
    header("location: $login");
}