<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

// Cek jika ada parameter role dari URL
$selected_role = isset($_GET['role']) ? $_GET['role'] : 'user';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password_plain = $_POST['password'];
    $role = $_POST['role'];

    // Validasi Email
    $cek_email = $koneksi->query("SELECT id FROM users WHERE email = '$email'");
    if ($cek_email->num_rows > 0) {
        $alert_message = '<div class="alert alert-danger">Error: Email sudah terdaftar.</div>';
    } else {
        // Hash Password
        $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

        // Query INSERT (SESUAI STRUKTUR DB KAMU)
        // Kolom: first_name, last_name, email, password, role
        $stmt = $koneksi->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hash, $role);

        if ($stmt->execute()) {
            $alert_message = '<div class="alert alert-success">
                                <strong>Sukses!</strong> Admin baru berhasil ditambahkan.
                              </div>';
        } else {
            $alert_message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tambah User</title>
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
                    <h1 class="mt-4">Tambah Admin</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="admins.php">Admin</a></li>
                        <li class="breadcrumb-item active">Tambah Baru</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            Info Dasar
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="first_name">Nama Depan</label>
                                        <input class="form-control" id="first_name" name="first_name" type="text"
                                            placeholder="Cth: John" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="last_name">Nama Belakang</label>
                                        <input class="form-control" id="last_name" name="last_name" type="text"
                                            placeholder="Cth: Doe" required />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="email">Email</label>
                                    <input class="form-control" id="email" name="email" type="email"
                                        placeholder="name@example.com" required />
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="password">Password</label>
                                        <input class="form-control" id="password" name="password" type="password"
                                            placeholder="Password Baru" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="role">Role / Peran</label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="admin" <?php echo ($selected_role == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                        </select>
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Pengguna</button>
                                <a href="admins.php" class="btn btn-secondary">Kembali ke Daftar</a>
                            </form>
                        </div>
                    </div>
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