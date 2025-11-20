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
    <title>Pesan Masuk - GreenRay Admin</title>
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
                    <h1 class="mt-4">Pesan Kontak Masuk</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pesan Masuk</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-envelope me-1"></i>
                            Daftar Semua Pesan (dari tabel `contact_messages`)
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">ID</th>
                                        <th style="width: 15%;">Tanggal</th>
                                        <th style="width: 20%;">Nama Pengirim</th>
                                        <th style="width: 20%;">Email</th>
                                        <th style="width: 25%;">Subjek</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    $query_messages = "SELECT id, created_at, full_name, email, subject, is_read 
                                    FROM contact_messages 
                                    ORDER BY id DESC";
                                    $result_messages = $koneksi->query($query_messages);

                                    if ($result_messages && $result_messages->num_rows > 0) {
                                        
                                        while ($row = $result_messages->fetch_assoc()) {

                                            
                                            $tanggal = date('d M Y, H:i', strtotime($row['created_at']));

                                            
                                            if ($row['is_read'] == 0) {
                                                $status = '<span class="badge bg-primary">Baru</span>';
                                                $row_style = 'font-weight: bold;'; 
                                            } else {
                                                $status = '<span class="badge bg-secondary">Dibaca</span>';
                                                $row_style = ''; 
                                            }

                                            echo '<tr style="' . $row_style . '">';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td>' . $tanggal . '</td>';
                                            echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
                                            echo '<td>' . $status . '</td>';
                                            echo '<td>
                                                <a href="message_view.php?id=' . $row['id'] . '" class="btn btn-info btn-sm">Lihat</a>
                                                <a href="message_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus pesan ini?\');">Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">Tidak ada pesan masuk.</td></tr>';
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