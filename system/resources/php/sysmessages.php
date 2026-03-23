<?php
session_start();
include_once('../../includes/dispatch.php');
include_once('../../includes/comments.php');

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = array();

try {
    // check if any message to be displayed
    if (isset($_SESSION['sysmessages']) && is_array($_SESSION['sysmessages'])) {
        $response = json_encode($_SESSION['sysmessages']);
        $_SESSION['sysmessages'] = array();
    }
   
    
} catch (Exception $e) {
    // Handle any exceptions
    $msg['message'] = 'An unexpected error occurred.';
    $msg['class'] = 'error';
    $response[] = $msg;

    // Log the error (in production, don't expose error details to client)
    error_log('Backend error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
exit;

?>