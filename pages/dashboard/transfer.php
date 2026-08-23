<?php
session_start();
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../login.php?msg=Please login to continue');
    exit;
}

require('../../configs/db.php');
require('pp_check.php'); // PP Check
require('../../scripts/get_userinfo.php'); // $fName

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
        $success = 'Transaction Successful';
    } else {
        $error = $_GET['msg'];
    }
}

$accNo = $_SESSION['AccNo'];
$sql = "SELECT Balance FROM balance WHERE AccNo = '$accNo'";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
$balance = htmlspecialchars($data['Balance']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/logo1.png" type="image/x-icon">
    <title>Transfer Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/transfer.css">
    <link rel="stylesheet" href="css/all.min.css">
</head>

<body>
    <div id="loading-screen">
        <img src="../../assets/img/logo1.png" alt="Loading...">
        <div id="loading-text">Loading, please wait...</div>
    </div>
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
                <h3>Transfer Funds</h3>
                <ul class="navbar-nav-ul">
                    <li class="nav-item avatar-n">
                        <div class="avatar-nav" style="background-image: url('<?php echo htmlspecialchars($pp); ?>');"></div>
                    </li>
                </ul>
            </div>
            </br>
            <!-- Main Content -->
            <div class="index-content container-main">
                <div class="overview-row d-flex">
                    <div class="revenue">
                        <div class="revenue-container">
                            <div class="revenue-header d-flex justify-between">
                                <h6 class="revenue-header-text">Transfer Funds</h6>
                            </div>
                            <div class="user-setting-body project-body">
                                <form action="../../scripts/bal_transfer.php" method="POST">
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col d-flex flex-direction-column">
                                            <label class="form-label" for="receiver_accNo"><strong>Account Number</strong></label>
                                            <input class="form-control-prof" type="text" id="receiver_accNo" name="receiver_accNo">
                                        </div>
                                    </div>
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col d-flex flex-direction-column">
                                            <label class="form-label" for="amount"><strong>Amount</strong></label>
                                            <input class="form-control-prof" type="number" id="amount" name="amount">
                                        </div>
                                    </div>
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col d-flex flex-direction-column">
                                            <label class="form-label" for="remarks"><strong>Remarks</strong></label>
                                            <input class="form-control-prof" type="text" id="remarks" name="remarks">
                                        </div>
                                    </div>
                                    <small id="error-code" class="error-font"><?php echo htmlspecialchars($error) ?></small>
                                    <div class="form-row">
                                        <div class="form-row-button text-center">
                                            <button class="button-profile" name="submit" id="submit" type="submit">Transfer</button>
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
        // Hide loading screen after page load
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loading-screen');
            loadingScreen.style.display = 'none';
        });

        <?php if ($success) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo htmlspecialchars($success); ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php } ?>

        // Toggle sidebar on mobile
        document.querySelector('.navbar-top::before')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Show loading screen before navigation
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    e.preventDefault();
                    const loadingScreen = document.getElementById('loading-screen');
                    loadingScreen.style.display = 'flex';
                    setTimeout(() => {
                        window.location.href = href;
                    }, 500); // Delay to show loading screen
                }
            });
        });
    </script>
</body>

</html>