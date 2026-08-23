<?php
session_start();

// Enable error logging, disable display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/errors.log');

ob_start(); // Start output buffering to prevent stray output

// Check if user is logged in
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../pages/dashboard/login.php?msg=يرجى تسجيل الدخول للمتابعة');
    ob_end_flush();
    exit;
}

// Check if user is admin
require('../configs/db.php');
$accNo = $_SESSION['AccNo'];
$sql = "SELECT Role FROM userinfo WHERE AccNo = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Failed to prepare role check query in create_account.php: " . mysqli_error($conn));
    header('Location: ../pages/dashboard/admin_panel.php?msg=خطأ في إعداد الاستعلام: ' . urlencode(mysqli_error($conn)));
    ob_end_flush();
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $accNo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || $user['Role'] !== 'admin') {
    error_log("Unauthorized access attempt in create_account.php: AccNo=$accNo");
    header('Location: ../pages/dashboard/index.php?msg=الوصول ممنوع');
    ob_end_flush();
    exit;
}

// Sanitize and validate input
$fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Validate input
if (empty($fname) || empty($email) || empty($password) || empty($address)) {
    error_log("Missing required fields in create_account.php: fname=$fname, email=$email, password=" . (empty($password) ? 'empty' : 'set') . ", address=$address");
    header('Location: ../pages/dashboard/admin_panel.php?msg=جميع الحقول (الاسم، البريد الإلكتروني، كلمة المرور، العنوان) مطلوبة');
    ob_end_flush();
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format in create_account.php: $email");
    header('Location: ../pages/dashboard/admin_panel.php?msg=تنسيق البريد الإلكتروني غير صالح');
    ob_end_flush();
    exit;
}

// Check if email is already used
$sql = "SELECT Email FROM userinfo WHERE Email = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Failed to prepare email check query in create_account.php: " . mysqli_error($conn));
    header('Location: ../pages/dashboard/admin_panel.php?msg=خطأ في إعداد الاستعلام: ' . urlencode(mysqli_error($conn)));
    ob_end_flush();
    exit;
}
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    mysqli_stmt_close($stmt);
    error_log("Email already in use in create_account.php: $email");
    header('Location: ../pages/dashboard/admin_panel.php?msg=البريد الإلكتروني مستخدم بالفعل');
    ob_end_flush();
    exit;
}
mysqli_stmt_close($stmt);

// Hash the password
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Generate unique account number
$accNoExists = true;
$maxAttempts = 10;
$attempt = 0;
$newAccNo = 0;

while ($accNoExists && $attempt < $maxAttempts) {
    $newAccNo = rand(100000, 999999);
    $sql = "SELECT AccNo FROM credentials WHERE AccNo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log("Failed to prepare account number check query in create_account.php: " . mysqli_error($conn));
        header('Location: ../pages/dashboard/admin_panel.php?msg=خطأ في إعداد الاستعلام: ' . urlencode(mysqli_error($conn)));
        ob_end_flush();
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $newAccNo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        $accNoExists = false;
    }
    mysqli_stmt_close($stmt);
    $attempt++;
}

if ($accNoExists) {
    error_log("Failed to generate unique AccNo after $maxAttempts attempts in create_account.php");
    header('Location: ../pages/dashboard/admin_panel.php?msg=فشل في إنشاء رقم حساب فريد بعد عدة محاولات');
    ob_end_flush();
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert into credentials table
    $sql_credentials = "INSERT INTO credentials (AccNo, Pass) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql_credentials);
    if (!$stmt) {
        throw new Exception('فشل إعداد الجملة لجدول credentials: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "is", $newAccNo, $password_hashed);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('فشل الإدخال في جدول credentials: ' . mysqli_error($conn));
    }
    $affected_rows_credentials = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected_rows_credentials !== 1) {
        throw new Exception('لم يتم إدخال سجل في جدول credentials');
    }

    // Insert into userinfo table
    $sql_userinfo = "INSERT INTO userinfo (AccNo, Name, Email, Address, Role) VALUES (?, ?, ?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $sql_userinfo);
    if (!$stmt) {
        throw new Exception('فشل إعداد الجملة لجدول userinfo: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "isss", $newAccNo, $fname, $email, $address);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('فشل الإدخال في جدول userinfo: ' . mysqli_error($conn));
    }
    $affected_rows_userinfo = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected_rows_userinfo !== 1) {
        throw new Exception('لم يتم إدخال سجل في جدول userinfo');
    }

    // Insert into balance table
    $sql_balance = "INSERT INTO balance (AccNo, Balance) VALUES (?, 0)";
    $stmt = mysqli_prepare($conn, $sql_balance);
    if (!$stmt) {
        throw new Exception('فشل إعداد الجملة لجدول balance: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $newAccNo);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('فشل الإدخال في جدول balance: ' . mysqli_error($conn));
    }
    $affected_rows_balance = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected_rows_balance !== 1) {
        throw new Exception('لم يتم إدخال سجل في جدول balance');
    }

    // Commit transaction
    mysqli_commit($conn);
    header('Location: ../pages/dashboard/admin_panel.php?msg=success');
    ob_end_flush();
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    error_log("Error in create_account.php: " . $e->getMessage());
    header('Location: ../pages/dashboard/admin_panel.php?msg=فشل إنشاء الحساب: ' . urlencode($e->getMessage()));
    ob_end_flush();
    exit;
}

// Close database connection
mysqli_close($conn);
ob_end_flush();
?>