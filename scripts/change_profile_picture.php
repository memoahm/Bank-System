<?php
session_start();
require('../configs/db.php');

if (!isset($_SESSION['AccNo'])) {
    header('Location: ../pages/dashboard/settings.php?msg=Please login to continue');
    exit;
}

if (!isset($_POST['upload_picture']) || !isset($_FILES['profile_picture'])) {
    header('Location: ../pages/dashboard/settings.php?msg=Please select an image to upload');
    exit;
}

// تحديد المجلد الذي سيتم حفظ الصور فيه
$uploadDir = '../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // إنشاء المجلد إذا لم يكن موجودًا
}

// تحديد رقم الحساب
$accNo = $_SESSION['AccNo'];

// معالجة الصورة المرفوعة
$file = $_FILES['profile_picture'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$fileType = $file['type'];

// التحقق من وجود خطأ في الرفع
if ($fileError !== UPLOAD_ERR_OK) {
    header('Location: ../pages/dashboard/settings.php?msg=Error uploading file');
    exit;
}

// التحقق من حجم الصورة (مثلاً 5MB كحد أقصى)
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($fileSize > $maxFileSize) {
    header('Location: ../pages/dashboard/settings.php?msg=File size exceeds 5MB limit');
    exit;
}

// التحقق من نوع الصورة
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($fileType, $allowedTypes)) {
    header('Location: ../pages/dashboard/settings.php?msg=Only JPEG, PNG, and GIF files are allowed');
    exit;
}

// إنشاء اسم ملف فريد باستخدام AccNo وتاريخ الرفع
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$newFileName = $accNo . '_' . time() . '.' . $fileExt;
$destination = $uploadDir . $newFileName;

// نقل الصورة إلى المجلد
if (!move_uploaded_file($fileTmpName, $destination)) {
    header('Location: ../pages/dashboard/settings.php?msg=Failed to move uploaded file');
    exit;
}

// تحديث عمود pp في جدول userinfo
$sql = "UPDATE userinfo SET pp = ? WHERE AccNo = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    header('Location: ../pages/dashboard/settings.php?msg=Database error: ' . urlencode(mysqli_error($conn)));
    exit;
}
mysqli_stmt_bind_param($stmt, "si", $newFileName, $accNo);
if (!mysqli_stmt_execute($stmt)) {
    header('Location: ../pages/dashboard/settings.php?msg=Failed to update profile picture');
    exit;
}
mysqli_stmt_close($stmt);

// إعادة توجيه المستخدم مع رسالة نجاح
header('Location: ../pages/dashboard/settings.php?msg=success');
exit;
?>