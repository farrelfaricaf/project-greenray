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
    <title>Manajemen Reviews - GreenRay Admin</title>
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
                    <h1 class="mt-4">Manajemen Reviews</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Reviews</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-star me-1"></i>
                                    Daftar Semua Reviews (dari tabel `reviews`)
                                </span>
                                <a href="reviews_add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Tambah Review
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Foto</th>
                                        <th>Nama Customer</th>
                                        <th>Rating</th>
                                        <th>Isi Review (Singkat)</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    $query_reviews = "SELECT id, customer_name, rating, review_text, image_url, is_visible FROM reviews ORDER BY id ASC";
                                    $result_reviews = $koneksi->query($query_reviews);

                                    if ($result_reviews && $result_reviews->num_rows > 0) {
                                        
                                        while ($row = $result_reviews->fetch_assoc()) {

                                            
                                            $status = $row['is_visible'] ? '<span class="badge bg-success">Visible</span>' : '<span class="badge bg-danger">Hidden</span>';

                                            
                                            $review_snippet = strlen($row['review_text']) > 50 ? substr($row['review_text'], 0, 50) . '...' : $row['review_text'];
                                   
                                            $image_path = '../' . ltrim(str_replace('../', '', $row['image_url']), '/');
                                            echo '<tr>';
                                            echo '<td>' . $row['id'] . '</td>';
                                            echo '<td><img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['customer_name']) . '" width="50" height="50" style="border-radius: 50%; object-fit: cover;"></td>';
                                            echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['rating']) . ' ★</td>';
                                            echo '<td>' . htmlspecialchars($review_snippet) . '</td>';
                                            echo '<td>' . $status . '</td>';
                                            echo '<td>
                                                <a href="reviews_edit.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</a>
                                                <a href="reviews_delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus review ini?\');"><i class="fa-solid fa-xmark me-1"></i>Hapus</a>
                                            </td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">Tidak ada data review.</td></tr>';
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