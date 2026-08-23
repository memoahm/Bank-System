<?php
ob_start(); // Start output buffering to prevent stray output
header('Content-Type: application/json; charset=utf-8');

require('../configs/db.php');

// Enable error logging for debugging
ini_set('display_errors', 0); // Prevent errors from being displayed
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/errors.log');

// Receive JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check if required fields are present
if (!isset($data['accNo']) || !isset($data['fname']) || !isset($data['email']) || !isset($data['address']) || !isset($data['role'])) {
    error_log("Missing required fields in edit_client.php: " . print_r($data, true));
    echo json_encode(['success' => false, 'message' => 'All fields (account number, name, email, address, role) are required.']);
    ob_end_flush();
    exit;
}

// Sanitize and validate input
$accNo = $data['accNo'];
$fname = trim($data['fname']);
$email = trim($data['email']);
$address = trim($data['address']);
$role = trim($data['role']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format in edit_client.php: " . $email);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    ob_end_flush();
    exit;
}

// Validate accNo is numeric
if (!is_numeric($accNo)) {
    error_log("Invalid accNo in edit_client.php: " . $accNo);
    echo json_encode(['success' => false, 'message' => 'Account number must be numeric']);
    ob_end_flush();
    exit;
}

// Validate role
if (!in_array($role, ['user', 'admin'])) {
    error_log("Invalid role in edit_client.php: " . $role);
    echo json_encode(['success' => false, 'message' => 'Invalid role specified']);
    ob_end_flush();
    exit;
}

// Check if email is already used by another account
$sql_check_email = "SELECT AccNo FROM userinfo WHERE Email = ? AND AccNo != ?";
$stmt_check_email = mysqli_prepare($conn, $sql_check_email);
if (!$stmt_check_email) {
    error_log("Failed to prepare email check query in edit_client.php: " . mysqli_error($conn));
    echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات: ' . mysqli_error($conn)]);
    ob_end_flush();
    exit;
}
mysqli_stmt_bind_param($stmt_check_email, "si", $email, $accNo);
mysqli_stmt_execute($stmt_check_email);
$result_check_email = mysqli_stmt_get_result($stmt_check_email);
if (mysqli_num_rows($result_check_email) > 0) {
    mysqli_stmt_close($stmt_check_email);
    error_log("Email already in use in edit_client.php: " . $email);
    echo json_encode(['success' => false, 'message' => 'The email address is already used for another account.']);
    ob_end_flush();
    exit;
}
mysqli_stmt_close($stmt_check_email);

// Update userinfo table
$sql = "UPDATE userinfo SET Name = ?, Email = ?, Address = ?, Role = ? WHERE AccNo = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Failed to prepare update query in edit_client.php: " . mysqli_error($conn));
    echo json_encode(['success' => false, 'message' => 'خطأ في إعداد الاستعلام: ' . mysqli_error($conn)]);
    ob_end_flush();
    exit;
}
mysqli_stmt_bind_param($stmt, "ssssi", $fname, $email, $address, $role, $accNo);
if (mysqli_stmt_execute($stmt)) {
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        error_log("No rows updated in edit_client.php for AccNo: " . $accNo);
        echo json_encode(['success' => false, 'message' => 'No account with this number was found or no changes were made.']);
    }
} else {
    $error = mysqli_error($conn);
    error_log("Update query failed in edit_client.php: " . $error);
    echo json_encode(['success' => false, 'message' => 'Failed to update customer data: ' . $error]);
    mysqli_stmt_close($stmt);
}

// Close database connection and flush output
mysqli_close($conn);
ob_end_flush();
?>