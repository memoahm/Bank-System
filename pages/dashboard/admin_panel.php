<?php
session_start();
if (!isset($_SESSION['AccNo'])) {
    header('Location: ../login.php?msg=Please login to continue');
    exit;
}

// Check if user is admin
require('../../configs/db.php');
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
mysqli_stmt_close($stmt);
if (!$user || $user['Role'] !== 'admin') {
    header('Location: index.php?msg=Access denied');
    exit;
}

require('pp_check.php'); // PP Check
require('../../scripts/get_userinfo.php'); // $fName, $pp

// Handle messages
$error = '';
$success = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success') {
        $success = 'Operation Successful';
    } else {
        $error = $_GET['msg'];
    }
}

// Fetch all users for client management
$sql = "SELECT AccNo, Name, Address, Email, Role FROM userinfo WHERE Role != 'admin'";
$users_result = mysqli_query($conn, $sql);
if (!$users_result) {
    $error = 'Failed to fetch users: ' . mysqli_error($conn);
}

// Fetch all support messages
$sql = "SELECT id, AccNo, Message, DateTime FROM support_messages ORDER BY DateTime DESC";
$messages_result = mysqli_query($conn, $sql);
if (!$messages_result) {
    $error = 'Failed to fetch support messages: ' . mysqli_error($conn);
}

// Fetch all transactions
$sql = "SELECT Sender, Receiver, Amount, Remarks, DateTime, SenBalance, RecBalance, notified FROM transactions ORDER BY DateTime DESC";
$transactions_result = mysqli_query($conn, $sql);
if (!$transactions_result) {
    $error = 'Failed to fetch transactions: ' . mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assets/img/logo1.png" type="image/x-icon">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/admin_panel.css">
    <link rel="stylesheet" href="css/all.min.css">
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
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_panel.php' ? 'active' : ''; ?>" href="admin_panel.php">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Panel</span>
                        </a>
                    </li>
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
                <h3>Admin Panel</h3>
                <ul class="navbar-nav-ul">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell fa-fw"></i></a>
                    </li>
                    <li class="nav-item avatar-n">
                        <div class="avatar-nav" style="background-image: url('<?php echo htmlspecialchars($pp); ?>');"></div>
                    </li>
                </ul>
            </div>
            <br>

            <!-- Main Content -->
            <div class="index-content container-main">
                <!-- Create New Account -->
                <div class="admin-container">
                    <h6 class="admin-header">Create New Account</h6>
                    <form action="../../scripts/create_account.php" method="POST">
                        <div class="form-row d-flex justify-between">
                            <div class="form-row-col d-flex flex-direction-column">
                                <label class="form-label" for="fname"><strong>Name</strong></label>
                                <input class="form-control-prof" type="text" id="fname" name="fname" required>
                            </div>
                        </div>
                        <div class="form-row d-flex justify-between">
                            <div class="form-row-col d-flex flex-direction-column">
                                <label class="form-label" for="email"><strong>Email</strong></label>
                                <input class="form-control-prof" type="email" id="email" name="email" required>
                            </div>
                            <div class="form-row-col d-flex flex-direction-column">
                                <label class="form-label" for="password"><strong>Password</strong></label>
                                <input class="form-control-prof" type="password" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="form-row d-flex justify-between">
                            <div class="form-row-col d-flex flex-direction-column">
                                <label class="form-label" for="address"><strong>Address</strong></label>
                                <input class="form-control-prof" type="text" id="address" name="address" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <button class="button-profile" type="submit">Create Account</button>
                        </div>
                    </form>
                </div>
                <!-- Client Management -->
                <div class="admin-container">
                    <h6 class="admin-header">Client Management</h6>
                    <div class="table-itself">
                        <table>
                            <thead>
                                <tr>
                                    <th>Account Number</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($users_result)) { ?>
                                    <tr data-accno="<?php echo htmlspecialchars($row['AccNo']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['Name']); ?>"
                                        data-email="<?php echo htmlspecialchars($row['Email']); ?>"
                                        data-address="<?php echo htmlspecialchars($row['Address'] ?? ''); ?>"
                                        data-role="<?php echo htmlspecialchars($row['Role']); ?>">
                                        <td><?php echo htmlspecialchars($row['AccNo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Address'] ?? 'غير متوفر'); ?></td>
                                        <td><?php echo htmlspecialchars($row['Role']); ?></td>
                                        <td>
                                            <button class="button-profile" onclick="editClient('<?php echo $row['AccNo']; ?>', this)">Edit</button>
                                            <button class="button-profile button-danger" onclick="deleteClient('<?php echo $row['AccNo']; ?>')">Delete</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Support Messages -->
                <div class="admin-container">
                    <h6 class="admin-header">Support Messages</h6>
                    <div class="table-itself">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Account Number</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($messages_result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['AccNo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Message']); ?></td>
                                        <td><?php echo htmlspecialchars($row['DateTime']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="admin-container">
                    <h6 class="admin-header">Transactions</h6>
                    <div class="table-itself">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th>Sender Balance</th>
                                    <th>Receiver Balance</th>
                                    <th>Notified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($transactions_result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['Sender']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Receiver']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Amount']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Remarks'] ?? 'غير متوفر'); ?></td>
                                        <td><?php echo htmlspecialchars($row['DateTime']); ?></td>
                                        <td><?php echo htmlspecialchars($row['SenBalance']); ?></td>
                                        <td><?php echo htmlspecialchars($row['RecBalance']); ?></td>
                                        <td><?php echo htmlspecialchars($row['notified'] ? 'Yes' : 'No'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
// Toggle sidebar on mobile
document.querySelector('.navbar-top::before')?.addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('active');
});

// Show success/error messages
<?php if ($success) { ?>
    Swal.fire({
        icon: 'success',
        title: 'Successful operation',
        text: '<?php echo htmlspecialchars($success); ?>',
        position: 'center',
        showConfirmButton: true,
        confirmButtonColor: '#009578',
        timer: 5000
    });
<?php } ?>
<?php if ($error) { ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo htmlspecialchars($error); ?>',
        position: 'center',
        showConfirmButton: true,
        confirmButtonColor: '#009578',
        timer: 5000
    });
<?php } ?>

// Edit client
function editClient(accNo, button) {
    const row = button.closest('tr');
    const name = row.dataset.name;
    const email = row.dataset.email;
    const address = row.dataset.address || '';
    const role = row.dataset.role || 'user';

    Swal.fire({
        title: 'Edit Client',
        html: `
            <input id="edit-fname" class="swal2-input" placeholder="First Name" value="${name}">
            <input id="edit-email" class="swal2-input" placeholder="Email" value="${email}">
            <input id="edit-address" class="swal2-input" placeholder="Address" value="${address}">
            <select id="edit-role" class="swal2-select">
                <option value="user" ${role === 'user' ? 'selected' : ''}>User</option>
                <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
            </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save',
        confirmButtonColor: '#009578',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        preConfirm: () => {
            const fname = document.getElementById('edit-fname').value.trim();
            const email = document.getElementById('edit-email').value.trim();
            const address = document.getElementById('edit-address').value.trim();
            const role = document.getElementById('edit-role').value;
            if (!fname || !email || !address || !role) {
                Swal.showValidationMessage('All fields are required');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.showValidationMessage('Invalid email format');
                return false;
            }
            return {
                fname,
                email,
                address,
                role
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = {
                accNo: accNo,
                fname: result.value.fname,
                email: result.value.email,
                address: result.value.address,
                role: result.value.role
            };
            console.log('Sending data to edit_client.php:', data);

            fetch('../../scripts/edit_client.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successful operation',
                        text: 'Client updated successfully',
                        position: 'center',
                        confirmButtonColor: '#009578',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to update client',
                        position: 'center',
                        confirmButtonColor: '#009578',
                        timer: 5000
                    });
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the client: ' + error.message,
                    position: 'center',
                    confirmButtonColor: '#009578',
                    timer: 5000
                });
            });
        }
    });
}

// Delete client
function deleteClient(accNo) {
    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'This will permanently delete the client account!',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#009578'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../../scripts/delete_client.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    accNo: accNo
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successful operation',
                        text: 'Client deleted successfully',
                        position: 'center',
                        confirmButtonColor: '#009578',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete client',
                        position: 'center',
                        confirmButtonColor: '#009578',
                        timer: 5000
                    });
                }
            }).catch(error => {
                console.error('Fetch error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the client: ' + error.message,
                    position: 'center',
                    confirmButtonColor: '#009578',
                    timer: 5000
                });
            });
        }
    });
}
</script>
        </body>
        </html>