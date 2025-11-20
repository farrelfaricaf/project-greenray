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
    <title>Manajemen Lokasi - GreenRay Admin</title>
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
                    <h1 class="mt-4">Manajemen Lokasi (Kalkulator)</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting Kalkulator</li>
                        <li class="breadcrumb-item active">Manajemen Lokasi</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Daftar Semua Lokasi (dari tabel `locations`)
                                </span>
                                <a href="locations_add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Lokasi
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 30%;">Nama Kota</th>
                                        <th style="width: 20%;">Faktor Irradiance</th>
                                        <th style="width: 20%;">Status</th>
                                        <th style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    $query_locations = "SELECT id, city_name, irradiance_factor, is_active FROM locations ORDER BY id ASC";
                                    $result_locations = $koneksi->query($query_locations);

                                    if ($result_locations && $result_locations->num_rows > 0) {
                                        
                                        while ($row = $result_locations->fetch_assoc()) {

                                            
                                            $status = $row['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';

                                            echo '<tr>';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td>' . htmlspecialchars($row['city_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['irradiance_factor']) . '</td>';
                                            echo '<td>' . $status . '</td>';
                                            echo '<td>
                                                <a href="locations_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="locations_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus lokasi ini?\');">Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">Tidak ada data lokasi.</td></tr>';
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