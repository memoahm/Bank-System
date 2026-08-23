<?php
if (!isset($_POST['submit'])) {
    header('Location: ../pages/register.php');
    exit;
}

require('../configs/db.php');

$fullname = $_POST['fullName'];
$address = $_POST['address'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check for Duplicate Account
$stmt_dupCheck = $conn->prepare("SELECT * FROM userinfo WHERE Email = ?");
$stmt_dupCheck->bind_param("s", $email);
$stmt_dupCheck->execute();
$result_dubCheck = $stmt_dupCheck->get_result();

if (!$result_dubCheck) {
    header('Location: ../pages/register.php?msg=Cannot connect to database');
    exit;
}

$dubCount = $result_dubCheck->num_rows;
if ($dubCount != 0) {
    header('Location: ../pages/register.php?msg=Account Already Exists');
    exit;
}

// Generate a new Account Number (using AUTO_INCREMENT in credentials table)
$stmt_genCreds = $conn->prepare("INSERT INTO credentials (Pass) VALUES (?)");
$stmt_genCreds->bind_param("s", $hashedPassword);
if (!$stmt_genCreds->execute()) {
    header('Location: ../pages/register.php?msg=Failed to create account');
    exit;
}

// Retrieve the generated Account Number
$stmt_getAccNo = $conn->prepare("SELECT AccNo FROM credentials ORDER BY AccNo DESC LIMIT 1");
$stmt_getAccNo->execute();
$result_getAccNo = $stmt_getAccNo->get_result();
$accNo = $result_getAccNo->fetch_assoc()['AccNo'];

// Generate Balance for the Account
$stmt_genBal = $conn->prepare("INSERT INTO balance (AccNo, Balance) VALUES (?, ?)");
$balance = 69;
$stmt_genBal->bind_param("id", $accNo, $balance);
if (!$stmt_genBal->execute()) {
    header('Location: ../pages/register.php?msg=Failed to create balance');
    exit;
}

// Save the User Info for the Account
$stmt_saveUserInfo = $conn->prepare("INSERT INTO userinfo (AccNo, Name, Address, Email) VALUES (?, ?, ?, ?)");
$stmt_saveUserInfo->bind_param("isss", $accNo, $fullname, $address, $email);
if (!$stmt_saveUserInfo->execute()) {
    header('Location: ../pages/register.php?msg=Failed to save user info');
    exit;
}

session_start();
$_SESSION['AccNo'] = $accNo;
header('Location: ../pages/dashboard/index.php');
exit();

// Close all statements and connection
$stmt_dupCheck->close();
$stmt_genCreds->close();
$stmt_getAccNo->close();
$stmt_genBal->close();
$stmt_saveUserInfo->close();
$conn->close();
?>