<?php
// 1. Hubungkan ke database
// (Path '../' artinya 'naik satu folder' untuk menemukan koneksi.php)
include '../koneksi.php';

$alert_message = ""; // Variabel untuk menyimpan pesan notifikasi

// 2. Logika untuk memproses form saat disubmit (ketika tombol "Simpan" ditekan)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua 17 data dari form
    $slug = $_POST['slug'];
    $title = $_POST['title'];
    $subtitle_goal = $_POST['subtitle_goal'];
    $category = $_POST['category'];
    $location_text = $_POST['location_text'];
    $hero_image_url = $_POST['hero_image_url'];

    $stat_capacity = $_POST['stat_capacity'];
    $stat_co2_reduction = $_POST['stat_co2_reduction'];
    $stat_timeline = $_POST['stat_timeline'];
    $stat_investment = $_POST['stat_investment'];

    $overview_result = $_POST['overview_result'];
    $overview_details = $_POST['overview_details'];
    $overview_generation = $_POST['overview_generation'];

    $challenges_html = $_POST['challenges_html'];
    $solutions_html = $_POST['solutions_html'];
    $impact_html = $_POST['impact_html'];

    $tech_specs_json = $_POST['tech_specs_json']; // Ambil data JSON sebagai string

    // 3. Buat query INSERT (Gunakan Prepared Statements agar aman dari SQL Injection)
    $stmt = $koneksi->prepare("INSERT INTO projects 
        (slug, title, subtitle_goal, category, location_text, hero_image_url, 
        stat_capacity, stat_co2_reduction, stat_timeline, stat_investment, 
        overview_result, overview_details, overview_generation, 
        challenges_html, solutions_html, impact_html, tech_specs_json) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // 's' berarti string. Kita punya 17 kolom string/text/json
    $stmt->bind_param(
        "sssssssssssssssss",
        $slug,
        $title,
        $subtitle_goal,
        $category,
        $location_text,
        $hero_image_url,
        $stat_capacity,
        $stat_co2_reduction,
        $stat_timeline,
        $stat_investment,
        $overview_result,
        $overview_details,
        $overview_generation,
        $challenges_html,
        $solutions_html,
        $impact_html,
        $tech_specs_json
    );

    // 4. Eksekusi query
    if ($stmt->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Proyek baru berhasil ditambahkan.
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
    <meta name="description" content="Admin GreenRay" />
    <meta name="author" content="Farrel" />

    <title>Tambah Proyek - GreenRay Admin</title>

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
                        <a class="nav-link active" href="projects.php">
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

                    <h1 class="mt-4">Tambah Proyek Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="projects.php">Data Proyek</a></li>
                        <li class="breadcrumb-item active">Tambah Proyek</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir Proyek Baru
                        </div>
                        <div class="card-body">
                            <form action="project_add.php" method="POST">

                                <h5 class="mt-3 text-dark">Info Dasar</h5>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="title">Judul Proyek</label>
                                        <input class="form-control" id="title" name="title" type="text"
                                            placeholder="Cth: Residential Project – Surabaya Home" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="slug">Slug (untuk URL)</label>
                                        <input class="form-control" id="slug" name="slug" type="text"
                                            placeholder="cth: residential-project-surabaya" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="subtitle_goal">Subjudul / Goal</label>
                                    <input class="form-control" id="subtitle_goal" name="subtitle_goal" type="text"
                                        placeholder="Cth: Reduce household electricity bills" required>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="category">Kategori</label>
                                        <input class="form-control" id="category" name="category" type="text"
                                            placeholder="Cth: Residential" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="location_text">Lokasi Teks</label>
                                        <input class="form-control" id="location_text" name="location_text" type="text"
                                            placeholder="Cth: Surabaya, East Java" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="hero_image_url">URL Gambar Utama (Hero)</label>
                                    <input class="form-control" id="hero_image_url" name="hero_image_url" type="text"
                                        placeholder="Cth: ../img/rumah.jpg" required>
                                </div>

                                <h5 class="mt-4 text-dark">Statistik (4 Kartu)</h5>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="stat_capacity">Kapasitas</label>
                                        <input class="form-control" id="stat_capacity" name="stat_capacity" type="text"
                                            placeholder="Cth: 5 kWp">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="stat_co2_reduction">Reduksi CO2</label>
                                        <input class="form-control" id="stat_co2_reduction" name="stat_co2_reduction"
                                            type="text" placeholder="Cth: 3.6 tons/year">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="stat_timeline">Timeline</label>
                                        <input class="form-control" id="stat_timeline" name="stat_timeline" type="text"
                                            placeholder="Cth: 2 months">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="stat_investment">Investasi</label>
                                        <input class="form-control" id="stat_investment" name="stat_investment"
                                            type="text" placeholder="Cth: $8,500">
                                    </div>
                                </div>

                                <h5 class="mt-4 text-dark">Project Overview</h5>
                                <div class="mb-3">
                                    <label class="small mb-1" for="overview_result">Result</label>
                                    <input class="form-control" id="overview_result" name="overview_result" type="text"
                                        placeholder="Cth: Successfully reduced bills by 60%">
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="overview_details">Details</label>
                                    <input class="form-control" id="overview_details" name="overview_details"
                                        type="text" placeholder="Cth: Grid system with module monitoring unit">
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="overview_generation">Energy Generation</label>
                                    <textarea class="form-control" id="overview_generation" name="overview_generation"
                                        rows="3"
                                        placeholder="Cth: Generates approximately 4,500 kWh annually..."></textarea>
                                </div>

                                <h5 class="mt-4 text-dark">Detail Lainnya (HTML/JSON)</h5>
                                <div class="mb-3">
                                    <label class="small mb-1" for="challenges_html">Challenges (HTML List)</label>
                                    <textarea class="form-control" id="challenges_html" name="challenges_html" rows="4"
                                        placeholder="Tulis sebagai <ul><li>...</li></ul>"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="solutions_html">Solutions (HTML List)</label>
                                    <textarea class="form-control" id="solutions_html" name="solutions_html" rows="4"
                                        placeholder="Tulis sebagai <ul><li>...</li></ul>"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="impact_html">Impact (HTML List)</label>
                                    <textarea class="form-control" id="impact_html" name="impact_html" rows="4"
                                        placeholder="Tulis sebagai <ul><li>...</li></ul>"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="tech_specs_json">Technical Specs (JSON)</label>
                                    <textarea class="form-control" id="tech_specs_json" name="tech_specs_json" rows="4"
                                        placeholder='Cth: [{"label": "Panel Type", "value": "Monocrystalline 400W"}, {"label": "Panel Count", "value": "8 panels"}]'></textarea>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Proyek</button>
                                <a href="projects.php" class="btn btn-secondary">Batal</a>
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