<?php
require 'system/includes/dispatch.php';
require 'system/includes/session.php';

// Load the configuration file
config('source', 'config/config.ini');

// Set the timezone
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

$whitelist = array('jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif', 'webp');
$name      = null;
$dir       = 'content/images/';
$dirThumb  = 'content/images/thumbnails/';
$error     = null;
$timestamp = date('YmdHis');
$path      = null;
$width     = config('thumbnail.width');

if (login()) {

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (is_null($width) || empty($width)) {
        $width = 500;
    }
    
    if (isset($_FILES) && isset($_FILES['file'])) {
        $uploadError = $_FILES['file']['error'];
        $tmp_name    = $_FILES['file']['tmp_name'];

        if ($uploadError !== UPLOAD_ERR_OK || empty($tmp_name) || !is_uploaded_file($tmp_name)) {
            $error = 'Upload failed or file not received.';
        } else {
            // Sanitize file name to prevent path traversal and special character issues
            $rawName   = basename($_FILES['file']['name']);
            $extension = strtolower(pathinfo($rawName, PATHINFO_EXTENSION));

            if (!in_array($extension, $whitelist, true)) {
                $error = 'Invalid file type uploaded.';
            } else {
                // Check if file is a valid image
                $check = @getimagesize($tmp_name);
                if ($check === false) {
                    $error = 'File is not a valid image.';
                } else {
                    $cleanName = preg_replace('/[^a-zA-Z0-9\._-]/', '', $rawName);
                    $name      = $cleanName;
                    $path      = $dir . $timestamp . '-' . $cleanName;

                    if (move_uploaded_file($tmp_name, $path)) {
                        $imageFile = pathinfo($path, PATHINFO_FILENAME);
                        $thumbFile = $dirThumb . $imageFile . '-' . $width . '.webp';
                        if (!file_exists($thumbFile)) {
                            create_thumb($path, $width);
                        }
                    } else {
                        $error = 'Failed to save uploaded file.';
                    }
                }
            }
        }
    } else {
        $error = 'No file attached.';
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'path'  => $path,
        'name'  => $name,
        'error' => $error,
    ));

    die();

} else {
    $login = site_url() . 'login';
    header("location: $login");
}

