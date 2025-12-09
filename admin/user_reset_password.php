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
    <?php include 'includes/navbar.php'; ?>

    <div id="layoutSidenav">

        <?php include 'includes/sidebar.php'; ?>

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

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>