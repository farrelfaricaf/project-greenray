<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$user_id = null;
$user = [];


if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    
    $stmt_select = $koneksi->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
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

    
    $role = isset($_POST['role']) ? $_POST['role'] : ($user['role'] ?? 'user');

    $stmt_update = $koneksi->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?");
    $stmt_update->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);

    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Data user berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';

        
        $stmt_refresh = $koneksi->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE id = ?");
        $stmt_refresh->bind_param("i", $user_id);
        $stmt_refresh->execute();
        $user = $stmt_refresh->get_result()->fetch_assoc();
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

if (empty($user)) {
    $user = array_fill_keys(['first_name', 'last_name', 'email', 'role'], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User #<?php echo $user_id; ?> - GreenRay Admin</title>
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

                    <h1 class="mt-4">Edit User</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="users.php">Data Users</a></li>
                        <li class="breadcrumb-item active">Edit User #<?php echo $user_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($user_id) && !empty($user['email'])): ?>
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
                                        <label class="small mb-1" for="role">Role / Peran</label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>
                                                User Biasa (Klien)</option>
                                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>
                                                Administrator</option>
                                        </select>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Update User</button>
                                    <a href="users.php" class="btn btn-secondary">Kembali ke Daftar</a>
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