<?php
// 1. Hubungkan ke database
include '../koneksi.php';

$alert_message = ""; // Variabel untuk menyimpan pesan notifikasi

// 2. Logika untuk memproses form saat disubmit (ketika tombol "Simpan" ditekan)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $slug = $_POST['slug'];
    $name = $_POST['name'];
    $subtitle = $_POST['subtitle'];
    $image_url = $_POST['image_url'];
    $description = $_POST['description'];
    $summary_feature_1 = $_POST['summary_feature_1'];
    $summary_feature_2 = $_POST['summary_feature_2'];
    $key_features_json = $_POST['key_features_json'];
    $specifications_json = $_POST['specifications_json'];

    // 3. Buat query INSERT (Gunakan Prepared Statements agar aman)
    $stmt = $koneksi->prepare("INSERT INTO products 
        (slug, name, subtitle, image_url, description, 
        summary_feature_1, summary_feature_2, key_features_json, specifications_json) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // 's' berarti string. Kita punya 9 kolom.
    $stmt->bind_param(
        "sssssssss",
        $slug,
        $name,
        $subtitle,
        $image_url,
        $description,
        $summary_feature_1,
        $summary_feature_2,
        $key_features_json,
        $specifications_json
    );

    // 4. Eksekusi query
    if ($stmt->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Produk baru berhasil ditambahkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal menyimpan: ' . $stmt->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah Produk - GreenRay Admin</title>
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
                        <a class="nav-link active" href="products.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-solar-panel"></i></div>
                            Produk
                        </a>
                        <a class="nav-link" href="clients.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Klien
                        </a>
                        <a class="nav-link" href="reviews.php">
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

                    <h1 class="mt-4">Tambah Produk Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Data Produk</a></li>
                        <li class="breadcrumb-item active">Tambah Produk</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir Produk Baru
                        </div>
                        <div class="card-body">
                            <form action="product_add.php" method="POST">

                                <h5 class="mt-3 text-dark">Info Utama</h5>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="name">Nama Produk</label>
                                        <input class="form-control" id="name" name="name" type="text"
                                            placeholder="Cth: The SunDial Starter" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="subtitle">Subjudul</label>
                                        <input class="form-control" id="subtitle" name="subtitle" type="text"
                                            placeholder="Cth: (2-3 kWp)">
                                    </div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="slug">Slug (untuk URL)</label>
                                        <input class="form-control" id="slug" name="slug" type="text"
                                            placeholder="cth: the-sundial-starter" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="image_url">URL Gambar</label>
                                        <input class="form-control" id="image_url" name="image_url" type="text"
                                            placeholder="Cth: ../img/catalog-product1.png" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="description">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                        placeholder="Deskripsi singkat produk..."></textarea>
                                </div>

                                <h5 class="mt-4 text-dark">Ringkasan (untuk Halaman Home)</h5>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="summary_feature_1">Fitur Ringkas 1</label>
                                        <input class="form-control" id="summary_feature_1" name="summary_feature_1"
                                            type="text" placeholder="Cth: Small Investment, Big Savings">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="summary_feature_2">Fitur Ringkas 2</label>
                                        <input class="form-control" id="summary_feature_2" name="summary_feature_2"
                                            type="text" placeholder="Cth: Quick Installation">
                                    </div>
                                </div>

                                <h5 class="mt-4 text-dark">Detail (JSON)</h5>
                                <div class="mb-3">
                                    <label class="small mb-1" for="key_features_json">Key Features (JSON)</label>
                                    <textarea class="form-control" id="key_features_json" name="key_features_json"
                                        rows="5"
                                        placeholder='[{"title": "Judul Fitur 1", "description": "Deskripsi fitur 1."}, {"title": "Judul Fitur 2", "description": "Deskripsi fitur 2."}]'></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="specifications_json">Spesifikasi (JSON)</label>
                                    <textarea class="form-control" id="specifications_json" name="specifications_json"
                                        rows="5"
                                        placeholder='[{"icon_class": "icon-apa", "title": "Judul Spek 1", "subtitle": "Subjudul spek 1."}, {"icon_class": "icon-lain", "title": "Judul Spek 2", "subtitle": "Subjudul spek 2."}]'></textarea>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Produk</button>
                                <a href="products.php" class="btn btn-secondary">Batal</a>
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