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
    <title>Manajemen FAQ - GreenRay Admin</title>
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
                    <h1 class="mt-4">Manajemen FAQ</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data FAQ</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-question-circle me-1"></i>
                                    Daftar Semua FAQ (dari tabel `faqs`)
                                </span>
                                <a href="faqs_add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah FAQ
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Urutan</th>
                                        <th style="width: 30%;">Pertanyaan</th>
                                        <th style="width: 45%;">Jawaban (Singkat)</th>
                                        <th style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    
                                    $query_faqs = "SELECT id, question, answer, order_index FROM faqs ORDER BY order_index ASC";
                                    $result_faqs = $koneksi->query($query_faqs);

                                    if ($result_faqs && $result_faqs->num_rows > 0) {
                                        
                                        while ($row = $result_faqs->fetch_assoc()) {

                                            
                                            $answer_snippet = strlen($row['answer']) > 100 ? substr(strip_tags($row['answer']), 0, 100) . '...' : strip_tags($row['answer']);

                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($row['order_index']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['question']) . '</td>';
                                            echo '<td>' . htmlspecialchars($answer_snippet) . '</td>';
                                            echo '<td>
                                                <a href="faqs_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="faqs_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus FAQ ini?\');">Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">Tidak ada data FAQ.</td></tr>';
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