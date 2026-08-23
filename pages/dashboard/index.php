<?php
session_start();
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../login.php?msg=Please login to continue');
    exit;
}

require('../../configs/db.php');
require('pp_check.php'); // PP Check
require('../../scripts/get_balance.php'); // $balance
require('../../scripts/get_interest.php'); // $interest
require('../../scripts/get_userinfo.php'); // $name, $fName
require('../../scripts/get_analytics.php'); // $totalDebit, $totalCredit
require('../../scripts/get_transactions.php'); // $trns
require('../../scripts/notifications.php'); // Notifications

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
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        addNotification('success', 'Transaction Successful');
    } else {
        addNotification('error', $_GET['msg']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/logo1.png" type="image/x-icon">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/all.min.css">
</head>

<body>
    <div id="loading-screen">
        <img src="../../assets/img/logo1.png" alt="Loading...">
        <div id="loading-text">Loading, please wait...</div>
    </div>
    <div id="wrapper">
        <!-- Navbar Side -->
        <nav class="navbar-side sidebar">
            <div class="nav-container">
                <a class="navbar-brand" href="./index.php">
                    <div class="sidebar-brand-icon rotate-n-15">
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
                <h3>Welcome, <span class="Name"><?php echo htmlspecialchars($fName); ?></span></h3>
                <ul class="navbar-nav-ul">
                    <li class="nav-item notification-bell">
                        <a class="nav-link" href="#" id="notification-toggle"><i class="fas fa-bell fa-fw"></i></a>
                        <?php if (getUnreadNotificationsCount() > 0) { ?>
                            <span class="notification-badge"><?php echo getUnreadNotificationsCount(); ?></span>
                        <?php } ?>
                        <div class="notification-dropdown" id="notification-dropdown">
                            <?php foreach ($_SESSION['notifications'] as $notification) { ?>
                                <div class="notification-item <?php echo $notification['type']; ?> <?php echo $notification['read'] ? '' : 'unread'; ?>">
                                    <div><?php echo htmlspecialchars($notification['message']); ?></div>
                                    <div class="timestamp"><?php echo $notification['timestamp']; ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </li>
                    <li class="nav-item avatar-n">
                        <div class="avatar-nav" style="background-image: url('<?php echo htmlspecialchars($pp); ?>');"></div>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="index-content container-main">
                <div class="dashboard-header d-flex justify-between">
                    <h3></h3>
                </div>
                <!-- Info Cards -->
                <div class="income-inf-row row">
                    <div class="col-income">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-text">
                                    <div class="card-span"><span style="color: rgb(78, 115, 223);">Balance</span></div>
                                    <div class="card-price"><span>EG. <?php echo $balance ?></span></div>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-income">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-text">
                                    <div class="card-span"><span style="color: rgb(246, 194, 62);">Interest</span></div>
                                    <div class="card-price"><span>EG. <?php echo $interest ?></span></div>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-percent fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-income">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-text">
                                    <div class="card-span"><span style="color: rgb(28, 200, 138);">Income</span></div>
                                    <div class="card-price"><span>EG. <?php echo $totalCredit ?></span></div>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-income">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-text">
                                    <div class="card-span"><span style="color: red;">Expense</span></div>
                                    <div class="card-price"><span>EG. <?php echo $totalDebit ?></span></div>
                                </div>
                                <div class="card-icon">
                                    <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- First Row -->
                <div class="overview-row row d-flex">
                    <!-- Recent Transactions -->
                    <div class="earnings">
                        <div class="earning-container row2-bgEdit">
                            <div class="earning-header d-flex justify-between">
                                <h6 class="earning-header-text">Recent Transactions</h6>
                            </div>
                            <div class="earning-body">
                                <div class="table-itself margin-column-form">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Transaction Type</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Remarks</th>
                                                <th>Transaction Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 0;
                                            foreach ($trns as $trn) {
                                                if ($count >= 7) break;
                                                $date = date("d-m-Y", strtotime($trn['DateTime']));
                                                $sender = $trn['Sender'];
                                                $receiver = $trn['Receiver'];
                                                $amount = $trn['Amount'];
                                                $remarks = $trn['Remarks'];
                                                if ($trn['Sender'] == $accNo) {
                                                    echo "<tr>
                                                        <td>Debit</td>
                                                        <td>Transfer to $receiver</td>
                                                        <td>EG. $amount</td>
                                                        <td>$remarks</td>
                                                        <td>$date</td>
                                                    </tr>";
                                                } else {
                                                    echo "<tr>
                                                        <td>Credit</td>
                                                        <td>Transfer from $receiver</td>
                                                        <td>EG. $amount</td>
                                                        <td>$remarks</td>
                                                        <td>$date</td>
                                                    </tr>";
                                                }
                                                $count++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Transfer Form -->
                    </br>
                    <div class="revenue">
                        <div class="revenue-container row2-bgEdit">
                            <div class="revenue-header d-flex justify-between">
                                <h6 class="revenue-header-text">Transfer Funds</h6>
                            </div>
                            <div class="user-setting-body project-body">
                                <form action="../../scripts/bal_transfer.php" method="POST">
                                    <div class="form-row d-flex justify-between">
                                        <div class="form-row-col d-flex flex-direction-column">
                                            <label class="form-label" for="receiver_accNo"><strong>Account
                                                    Number</strong></label>
                                            <input class="form-control-prof" type="text" id="receiver_accNo"
                                                name="receiver_accNo">
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
                                    <small id="error-code" class="error-font"><?php echo isset($error) ? htmlspecialchars($error) : ''; ?></small>
                                    <div class="form-row">
                                        <div class="form-row-button text-center">
                                            <button class="button-profile" name="submit" id="submit"
                                                type="submit">Transfer</button>
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

        // Toggle sidebar on mobile
        document.querySelector('.navbar-top::before')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Toggle notification dropdown
        const notificationToggle = document.getElementById('notification-toggle');
        const notificationDropdown = document.getElementById('notification-dropdown');
        notificationToggle.addEventListener('click', (e) => {
            e.preventDefault();
            notificationDropdown.classList.toggle('active');
            if (notificationDropdown.classList.contains('active')) {
                // Mark notifications as read via AJAX
                fetch('../../scripts/mark_notifications_read.php', {
                    method: 'POST'
                }).then(() => {
                    document.querySelector('.notification-badge')?.remove();
                });
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationToggle.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
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
        // Show error message if Access denied
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'Access denied') { ?>
            Swal.fire({
                icon: 'error',
                title: 'error',
                text: 'Access denied',
                position: 'center',
                showConfirmButton: true,
                confirmButtonColor: '#dc3545',
                timer: 5000
            });
        <?php } ?>
    </script>
</body>

</html>