<?php
session_start();
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../login.php?msg=Please login to continue');
    exit;
}

require('../../configs/db.php');
require('../../scripts/get_userinfo.php'); // All user info
require('pp_check.php'); // PP Check

// Check user role
$accNo = $_SESSION['AccNo'];
$sql = "SELECT Role FROM userinfo WHERE AccNo = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    header('Location: index.php?msg=Database error: ' . urlencode(mysqli_error($conn)));
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $accNo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$role = $user ? $user['Role'] : '';
mysqli_stmt_close($stmt);

// Check if there is a GET message
$error = '';
$success = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $success = 'Operation Successful';
    } else {
        $error = $_GET['msg'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/logo1.png" type="image/x-icon">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/settings.css">
</head>

<body>
    <div id="wrapper">
        <!-- Navbar Side -->
        <nav class="sidebar">
            <div class="nav-container">
                <a class="navbar-brand" href="./index.php">
                    <div class="sidebar-brand-icon">
                        <img src="../../assets/img/logo.png" height="auto" width="210px">
                    </div>
                </a>
                <hr class="sidebar-divider">
                <ul class="navbar-nav" id="sidebar-ul">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <?php if ($role === 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_panel.php' ? 'active' : ''; ?>" href="admin_panel.php">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Panel</span>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_money.php' ? 'active' : ''; ?>" href="add_money.php">
                            <i class="fas fa-plus-circle"></i>
                            <span>Add Money</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transfer.php' ? 'active' : ''; ?>" href="./transfer.php">
                            <i class="fas fa-money-bill-alt"></i>
                            <span>Transfer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'active' : ''; ?>" href="transactions.php">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                            <i class="fas fa-industry"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                            <i class="fas fa-user"></i><span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                            <i class="fas fa-adjust"></i><span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : ''; ?>" href="support.php">
                            <i class="fas fa-envelope"></i><span>Support</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../scripts/logout.php">
                            <i class="fas fa-sign-out-alt"></i><span>Log out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <div id="content-wrapper">
            <!-- Navbar Top -->
            <div class="navbar-top" id="page-top">
                <h3>Edit Profile</h3>
                <ul class="navbar-nav-ul">

                    <li class="nav-item avatar-n">
                        <div class="avatar-nav" style="background-image: url('<?php echo htmlspecialchars($pp); ?>');"></div>
                    </li>
                </ul>
            </div>
            </br>
            <!-- Main Content -->
            <div class="index-content">
                <div class="overview-row d-flex">
                    <div class="earnings profile">
                        <div class="col-profile-container">
                            <div class="prof-body">
                                <img id="profile-img" src="<?php echo htmlspecialchars($pp) ?>" alt="Profile Picture" />
                                <div>
                                    <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="previewImage(event)">
                                    <button class="button-profile" type="button" onclick="document.getElementById('fileInput').click();">Change Photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </br>
                    <div class="revenue profile-actions">
                        <div class="revenue-container">
                            <div class="user-setting-head">
                                <h6 class="revenue-header-text">Change Account Info</h6>
                            </div>
                            <div class="user-setting-body">
                                <form action="../../scripts/change_accinfo.php" method="POST">
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col">
                                            <label class="form-label" for="name"><strong>Name</strong></label>
                                            <input class="form-control-prof" type="text" id="name" value="<?php echo htmlspecialchars($name); ?>" name="name">
                                        </div>
                                        <div class="form-row-col">
                                            <label class="form-label" for="email"><strong>Email Address</strong></label>
                                            <input class="form-control-prof" type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" name="email">
                                        </div>
                                    </div>
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col">
                                            <label class="form-label" for="address"><strong>Address</strong></label>
                                            <input class="form-control-prof" type="text" id="address" value="<?php echo htmlspecialchars($address); ?>" name="address">
                                        </div>
                                    </div>
                                    <small id="error-code" class="error-font"><?php echo htmlspecialchars($error) ?></small>
                                    <div class="form-row">
                                        <div class="form-row-button">
                                            <button class="button-profile" type="submit" name="change">Save Settings</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </br>
                        <div class="revenue-container">
                            <div class="user-setting-head">
                                <h6 class="revenue-header-text">Change Password</h6>
                            </div>
                            <div class="user-setting-body">
                                <form action="../../scripts/change_password.php" method="POST">
                                    <div class="form-row">
                                        <div class="form-row-col">
                                            <label class="form-label" for="old_password"><strong>Old Password</strong></label>
                                            <input class="form-control-prof" type="password" id="old_password" name="old_password" required>
                                        </div>
                                    </div>
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col">
                                            <label class="form-label" for="new_password"><strong>New Password</strong></label>
                                            <input class="form-control-prof" type="password" id="new_password" name="new_password" required>
                                        </div>
                                        <div class="form-row-col">
                                            <label class="form-label" for="confirm_password"><strong>Confirm New Password</strong></label>
                                            <input class="form-control-prof" type="password" id="confirm_password" name="confirm_password" required>
                                        </div>
                                    </div>
                                    <small id="error-code" class="error-font"><?php echo htmlspecialchars($error) ?></small>
                                    <div class="form-row">
                                        <div class="form-row-button">
                                            <button class="button-profile" type="submit" name="change_password">Change Password</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($success) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo htmlspecialchars($success) ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php } ?>

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('profile-img');
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Toggle sidebar on mobile
        document.querySelector('.navbar-top::before')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Bell icon functionality
        document.getElementById('bell-icon').addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Notifications',
                text: 'No new notifications at the moment.',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
</body>

</html>