<?php
include '../koneksi.php';
$alert_message = "";
$review_id = null;
$review = [];
if (isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $stmt_select = $koneksi->prepare("SELECT * FROM reviews WHERE id = ?");
    $stmt_select->bind_param("i", $review_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($result->num_rows > 0) {
        $review = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Review tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Review tidak valid.</div>';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_id = $_POST['review_id'];
    $customer_name = $_POST['customer_name'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $image_url = $_POST['image_url'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    $stmt_update = $koneksi->prepare("UPDATE reviews SET 
        customer_name = ?, review_text = ?, rating = ?, image_url = ?, is_visible = ? 
        WHERE id = ?");
    $stmt_update->bind_param(
        "ssisii",
        $customer_name,
        $review_text,
        $rating,
        $image_url,
        $is_visible,
        $review_id
    );
    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Review berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
        $stmt_select = $koneksi->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt_select->bind_param("i", $review_id);
        $stmt_select->execute();
        $review = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();
    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }
    $stmt_update->close();
}
if (empty($review)) {
    $review = array_fill_keys(['customer_name', 'review_text', 'rating', 'image_url', 'is_visible'], '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Review #<?php echo $review_id; ?> - GreenRay Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
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
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                        <a class="nav-link" href="projects.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Proyek
                        </a>
                        <a class="nav-link" href="products.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-solar-panel"></i></div>
                            Produk
                        </a>
                        <a class="nav-link" href="clients.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Klien
                        </a>
                        <a class="nav-link active" href="reviews.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                            Reviews
                        </a>
                        <a class="nav-link" href="faqs.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-question-circle"></i></div>
                            FAQ
                        </a>
                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <a class="nav-link" href="consultations.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                            Konsultasi
                        </a>
                        <a class="nav-link" href="contact_messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Pesan Kontak
                        </a>

                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseKalkulator" aria-expanded="false"
                            aria-controls="collapseKalkulator">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Setting Kalkulator
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseKalkulator" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="locations.php">Manajemen Lokasi</a>
                                <a class="nav-link" href="tariffs.php">Manajemen Tarif</a>
                            </nav>
                        </div>
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
                    <h1 class="mt-4">Edit Review</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="reviews.php">Data Reviews</a></li>
                        <li class="breadcrumb-item active">Edit Review #<?php echo $review_id; ?></li>
                    </ol>
                    <?php echo $alert_message; ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i>
                            Formulir Edit Review (ID: <?php echo $review_id; ?>)
                        </div>
                        <div class="card-body">
                            <form action="reviews_edit.php?id=<?php echo $review_id; ?>" method="POST">
                                <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">
                                <div class="mb-3">
                                    <label class="small mb-1" for="customer_name">Nama Customer</label>
                                    <input class="form-control" id="customer_name" name="customer_name" type="text"
                                        value="<?php echo htmlspecialchars($review['customer_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="review_text">Isi Review</label>
                                    <textarea class="form-control" id="review_text" name="review_text"
                                        rows="4"><?php echo htmlspecialchars($review['review_text']); ?></textarea>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="rating">Rating (Angka 1-5)</label>
                                        <input class="form-control" id="rating" name="rating" type="number" min="1"
                                            max="5" value="<?php echo htmlspecialchars($review['rating']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="image_url">URL Foto Customer</label>
                                        <input class="form-control" id="image_url" name="image_url" type="text"
                                            value="<?php echo htmlspecialchars($review['image_url']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <?php $is_checked = $review['is_visible'] ? 'checked' : ''; ?>
                                    <input class="form-check-input" id="is_visible" name="is_visible" type="checkbox"
                                        value="1" <?php echo $is_checked; ?>>
                                    <label class="form-check-label" for="is_visible">
                                        Tampilkan di Website? (Visible)
                                    </label>
                                </div>
                                <button class="btn btn-primary" type="submit">Update Review</button>
                                <a href="reviews.php" class="btn btn-secondary">Kembali ke Daftar</a>
                            </form>
                        </div>
                    </div>
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