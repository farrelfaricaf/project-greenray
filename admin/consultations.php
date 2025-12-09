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
    <title>Manajemen Konsultasi - GreenRay Admin</title>
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
                    <h1 class="mt-4">Manajemen Konsultasi (Leads)</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Konsultasi</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-calculator me-1"></i>
                            Daftar Semua Konsultasi (dari tabel `consultation_requests`)
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th>Lokasi</th>
                                        <th>Hasil Sistem (kWp)</th>
                                        <th>Estimasi Penghematan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $query_consults = "SELECT id, created_at, full_name, email, phone, calc_location, 
                                            result_system_capacity_kwp, result_monthly_savings 
                                    FROM consultation_requests 
                                    ORDER BY id DESC";
                                    $result_consults = $koneksi->query($query_consults);

                                    if ($result_consults && $result_consults->num_rows > 0) {

                                        while ($row = $result_consults->fetch_assoc()) {

                                            $tanggal = date('d M Y, H:i', strtotime($row['created_at']));

                                            $penghematan = "Rp " . number_format($row['result_monthly_savings'], 0, ',', '.');

                                            echo '<tr>';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td>' . $tanggal . '</td>';
                                            echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['calc_location']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['result_system_capacity_kwp']) . ' kWp</td>';
                                            echo '<td>' . htmlspecialchars($penghematan) . '</td>';

                                            echo '<td>
                                                <a href="consultations_view.php?id=' . $row['id'] . '" class="btn btn-info btn-sm">Lihat</a>
                                                <a href="consultations_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="consultations_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus data konsultasi ini?\');">Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="9" class="text-center">Tidak ada data konsultasi.</td></tr>';
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