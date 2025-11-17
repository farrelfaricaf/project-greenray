<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 
$user_id = null;
$user = []; 
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt_select = $koneksi->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
    $stmt_select->bind_param("i", $user_id); 
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: User tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID User tidak valid.</div>';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $stmt_update = $koneksi->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
    $stmt_update->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Data user berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
        $stmt_select = $koneksi->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
        $stmt_select->bind_param("i", $user_id);
        $stmt_select->execute();
        $user = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();
    } else {
        if ($koneksi->errno == 1062) { 
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Email tersebut sudah digunakan oleh user lain.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
        } else {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        }
    }
    $stmt_update->close();
}
if (empty($user)) {
    $user = array_fill_keys(['first_name', 'last_name', 'email'], '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit User #<?php echo $user_id; ?> - GreenRay Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
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

                    <h1 class="mt-4">Edit User</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="users.php">Data Users</a></li>
                        <li class="breadcrumb-item active">Edit User #<?php echo $user_id; ?></li>
                    </ol>
                    <?php echo $alert_message; ?>
                    <?php if (!empty($user_id) && !empty($user['email'])): // Hanya tampilkan form jika data user valid ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit User (ID: <?php echo $user_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="user_edit.php?id=<?php echo $user_id; ?>" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="first_name">Nama Depan</label>
                                            <input class="form-control" id="first_name" name="first_name" type="text"
                                                value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="last_name">Nama Belakang</label>
                                            <input class="form-control" id="last_name" name="last_name" type="text"
                                                value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="email">Email</label>
                                        <input class="form-control" id="email" name="email" type="email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1">Password</label>
                                        <input class="form-control" type="text" value="********" disabled readonly>
                                        <small class="form-text text-muted">Untuk mengubah password, gunakan tombol "Reset
                                            Password" di halaman daftar user.</small>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Update User</button>
                                    <a href="users.php" class="btn btn-secondary">Kembali ke Daftar</a>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
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