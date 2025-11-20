<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$admin_id = null;
$admin_data = [];

// 1. AMBIL DATA ADMIN (GET)
if (isset($_GET['id'])) {
    $admin_id = $_GET['id'];

    // Query KHUSUS untuk mengambil data dengan role='admin'
    $stmt_select = $koneksi->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ? AND role = 'admin'");
    $stmt_select->bind_param("i", $admin_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Data Administrator tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Admin tidak valid.</div>';
}

// 2. UPDATE DATA ADMIN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['admin_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Role dipaksa tetap 'admin'
    $role = 'admin';

    $stmt_update = $koneksi->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt_update->bind_param("ssssi", $first_name, $last_name, $email, $role, $admin_id);

    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Data Administrator berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';

        // Refresh data
        $stmt_refresh = $koneksi->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt_refresh->bind_param("i", $admin_id);
        $stmt_refresh->execute();
        $admin_data = $stmt_refresh->get_result()->fetch_assoc();
        $stmt_refresh->close();

    } else {
        if ($koneksi->errno == 1062) {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Email tersebut sudah digunakan.
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

// Fallback data kosong
if (empty($admin_data)) {
    $admin_data = array_fill_keys(['first_name', 'last_name', 'email', 'role'], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Administrator - GreenRay Admin</title>
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

                    <h1 class="mt-4">Edit Administrator</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="admins.php">Data Admin</a></li>
                        <li class="breadcrumb-item active">Edit Admin #<?php echo $admin_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($admin_id) && !empty($admin_data['email'])): ?>
                        <div class="card mb-4 border-danger">
                            <div class="card-header bg-danger text-white">
                                <i class="fas fa-user-shield me-1"></i>
                                Formulir Edit Administrator (ID: <?php echo $admin_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="admin_edit.php?id=<?php echo $admin_id; ?>" method="POST">
                                    <input type="hidden" name="admin_id" value="<?php echo $admin_id; ?>">

                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="first_name">Nama Depan</label>
                                            <input class="form-control" id="first_name" name="first_name" type="text"
                                                value="<?php echo htmlspecialchars($admin_data['first_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="last_name">Nama Belakang</label>
                                            <input class="form-control" id="last_name" name="last_name" type="text"
                                                value="<?php echo htmlspecialchars($admin_data['last_name']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1" for="email">Email</label>
                                        <input class="form-control" id="email" name="email" type="email"
                                            value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="role">Role / Peran</label>
                                            <input class="form-control" type="text" value="Administrator" readonly disabled>
                                            <input type="hidden" name="role" value="admin">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1">Password</label>
                                            <input class="form-control" type="text" value="********" disabled readonly>
                                            <small class="form-text text-muted">Gunakan fitur Reset Password jika
                                                lupa.</small>
                                        </div>
                                    </div>

                                    <button class="btn btn-danger" type="submit">Update Administrator</button>
                                    <a href="admins.php" class="btn btn-secondary">Kembali</a>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
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