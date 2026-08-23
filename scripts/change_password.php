<?php
session_start();
require('../configs/db.php');

if (!isset($_SESSION['AccNo'])) {
    header('Location: ../pages/dashboard/login.php?msg=Please login to continue');
    exit;
}

if (isset($_POST['change_password']) && isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $accNo = $_SESSION['AccNo'];
    $oldPassword = trim($_POST['old_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate passwords
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        header('Location: ../pages/dashboard/settings.php?msg=All fields are required');
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        header('Location: ../pages/dashboard/settings.php?msg=New passwords do not match');
        exit;
    }

    if (strlen($newPassword) < 6) {
        header('Location: ../pages/dashboard/settings.php?msg=New password must be at least 6 characters');
        exit;
    }

    // Fetch current password hash from database
    $query = "SELECT Pass FROM credentials WHERE AccNo = '$accNo'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['Pass'];

        // Verify old password
        if (password_verify($oldPassword, $storedPassword)) {
            // Hash the new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in database
            $updateQuery = "UPDATE credentials SET Pass = '$newPasswordHash' WHERE AccNo = '$accNo'";
            if (mysqli_query($conn, $updateQuery)) {
                header('Location: ../pages/dashboard/settings.php?msg=success');
            } else {
                header('Location: ../pages/dashboard/settings.php?msg=Failed to update password');
            }
        } else {
            header('Location: ../pages/dashboard/settings.php?msg=Old password is incorrect');
        }
    } else {
        header('Location: ../pages/dashboard/settings.php?msg=User not found');
    }
    exit;
} else {
    header('Location: ../pages/dashboard/settings.php?msg=Invalid request');
    exit;
}
?>