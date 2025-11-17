<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$user_id = null;
$user_email = "";
$new_password_plain = "";
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt_select = $koneksi->prepare("SELECT email FROM users WHERE id = ?");
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($result->num_rows > 0) {
        $user_email = $result->fetch_assoc()['email'];
        $new_password_plain = bin2hex(random_bytes(4)); 
        $hashed_password = password_hash($new_password_plain, PASSWORD_BCRYPT);
        $stmt_update = $koneksi->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_password, $user_id);
        if ($stmt_update->execute()) {
            $alert_message = '<div class="alert alert-success" role="alert">
                                <strong>Sukses!</strong> Password untuk <strong>' . htmlspecialchars($user_email) . '</strong> berhasil di-reset.
                              </div>';
        } else {
            $alert_message = '<div class="alert alert-danger">Error: Gagal me-reset password. ' . $stmt_update->error . '</div>';
        }
        $stmt_update->close();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: User tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID User tidak valid.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Reset Password - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link" href="index.php">... Dashboard</a>
                        <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link active" href="users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Admin
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Reset Password User</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="users.php">Data Users</a></li>
                        <li class="breadcrumb-item active">Reset Password</li>
                    </ol>
                    <?php echo $alert_message; ?>
                    <?php if (!empty($new_password_plain)): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <i class="fas fa-key me-1"></i>
                                Password Baru Telah Dibuat
                            </div>
                            <div class="card-body text-center">
                                <p>Password baru untuk user <strong><?php echo htmlspecialchars($user_email); ?></strong>
                                    adalah:</p>
                                <h3 class="mt-3 mb-3">
                                    <code
                                        style="background-color: #eee; padding: 10px; border-radius: 5px;"><?php echo $new_password_plain; ?></code>
                                </h3>
                                <p class="text-danger">
                                    <strong>PENTING:</strong> Segera berikan password ini kepada user.
                                    <br>Password ini tidak akan ditampilkan lagi setelah kamu meninggalkan halaman ini.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar User
                    </a>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; GreenRay 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>