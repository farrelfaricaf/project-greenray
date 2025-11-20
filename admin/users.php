<?php
include '../koneksi.php';
include 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manajemen Users - GreenRay Admin</title>
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
                    <h1 class="mt-4">Manajemen Users</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Users</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-users me-1"></i>
                            Daftar Semua User (dari tabel `users`)
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 20%;">Nama Depan</th>
                                        <th style="width: 20%;">Nama Belakang</th>
                                        <th style="width: 30%;">Email</th>
                                        <th style="width: 15%;">Tanggal Gabung</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_users = "SELECT id, first_name, last_name, email, created_at FROM users ORDER BY id DESC";
                                    $result_users = $koneksi->query($query_users);
                                    if ($result_users && $result_users->num_rows > 0) {
                                        while ($row = $result_users->fetch_assoc()) {
                                            $tanggal = date('d M Y', strtotime($row['created_at']));
                                            echo '<tr>';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                            echo '<td>' . $tanggal . '</td>';
                                            echo '<td>
                                                <a href="user_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm" title="Edit Nama/Email">Edit</a>
                                                <a href="user_reset_password.php?id=' . $row['id'] . '" class="btn btn-secondary btn-sm" title="Reset Password" onclick="return confirm(\'Yakin ingin me-reset password untuk ' . htmlspecialchars($row['email']) . '?\');">Reset Pass</a>
                                                <a href="user_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" title="Hapus User" onclick="return confirm(\'Yakin ingin menghapus user ini?\');">Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center">Tidak ada data user.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>