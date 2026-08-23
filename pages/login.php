<?php
session_start();
if (isset($_SESSION['AccNo'])) {
    header('Location: ../pages/dashboard/index.php');
    exit;
}

$error = '';
if (isset($_GET['msg'])) {
    $error = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="icon" href="../assets/img/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="dashboard/css/login.css">
</head>

<body>
    <div class="login-container">
        <a href="home.php"><img class="logo" src="../assets/img/logo.png" alt="OUT Logo" /></a>
        <h1>Welcome to <spam>OUT</spam> Bank</h1>
        <form action="../scripts/login_auth.php" method="POST">
            <label for="accountNumber">Account Number</label>
            <input type="text" id="accountNumber" name="accountNumber" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />

            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <button type="submit" name="submit">Login</button>
        </form>
        <div class="footer">
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </div>
    <script>
        // Hide loading screen after page load
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loading-screen');
            loadingScreen.style.display = 'none';
        });
    </script>

</body>

</html>