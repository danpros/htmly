<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Handle POST /api/v1/media/upload
 */
function api_upload_media()
{
    if (empty($_FILES['file'])) {
        api_error('No file uploaded in form field "file"', 400, 'NO_FILE');
    }

    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        api_error('Upload error code: ' . $file['error'], 400, 'UPLOAD_ERROR');
    }

    // Security check extension
    $allowedExt = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'zip');
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        api_error('File extension not allowed', 400, 'INVALID_EXTENSION');
    }

    $year = date('Y');
    $month = date('m');
    $uploadDir = 'content/uploads/' . $year . '/' . $month . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file['name']);
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        api_error('Failed to move uploaded file', 500, 'SERVER_ERROR');
    }

    $fileUrl = site_url() . $targetPath;
    $markdownSnippet = in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))
        ? "![" . htmlspecialchars($file['name']) . "](" . $fileUrl . ")"
        : "[" . htmlspecialchars($file['name']) . "](" . $fileUrl . ")";

    api_response(array(
        'success' => true,
        'message' => 'File successfully uploaded',
        'data' => array(
            'filename' => $filename,
            'file_url' => $fileUrl,
            'size' => $file['size'],
            'mime' => $file['type'],
            'markdown_snippet' => $markdownSnippet
        )
    ), 201);
}
