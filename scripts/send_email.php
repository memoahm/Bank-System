<?php
session_start();
require('../configs/db.php'); // تعديل المسار للاتصال بقاعدة البيانات

// تأكد إن المستخدم مسجل دخول
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../pages/dashboard/login.php.php?msg=Please login to continue');
    exit;
}

// لو تم الضغط على زر الإرسال وفيه رسالة
if (isset($_POST['send']) && isset($_POST['signature'])) {
    $accNo = $_SESSION['AccNo'];
    $message = trim($_POST['signature']);

    // التأكد إن الرسالة مش فاضية
    if (empty($message)) {
        header('Location: ../pages/dashboard/support.php?msg=The message cannot be empty.');
        exit;
    }

    // حماية من الهجمات (SQL Injection)
    $message = mysqli_real_escape_string($conn, $message);

    // إضافة الرسالة لجدول support_messages
    $query = "INSERT INTO support_messages (AccNo, Message) VALUES ('$accNo', '$message')";
    if (mysqli_query($conn, $query)) {
        header('Location: ../pages/dashboard/support.php?msg=success');
    } else {
        header('Location: ../pages/dashboard/support.php?msg=Failed to send message');
    }
    exit;
} else {
    header('Location: ../pages/dashboard/support.php?msg=Invalid request');
    exit;
}
?>