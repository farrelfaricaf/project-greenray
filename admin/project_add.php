<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $hero_image_path_db = ""; 

    
    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] == 0) {

        $target_dir = "../uploads/projects/"; 

        
        $file_name = uniqid() . '-' . basename($_FILES["hero_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        $check = getimagesize($_FILES["hero_image_file"]["tmp_name"]);
        if ($check !== false) {
            
            if (move_uploaded_file($_FILES["hero_image_file"]["tmp_name"], $target_file)) {
                
                
                $hero_image_path_db = "uploads/projects/" . $file_name;
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file yang di-upload.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File yang di-upload bukan gambar.</div>';
        }
    } else {
        
        $alert_message = '<div class="alert alert-danger">Error: Gambar utama (hero image) wajib di-upload.</div>';
    }
    

    
    $slug = $_POST['slug'];
    $title = $_POST['title'];
    $subtitle_goal = $_POST['subtitle_goal'];
    $category = $_POST['category'];
    $location_text = $_POST['location_text'];
    

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

    $tech_specs_json = $_POST['tech_specs_json'];

    
    if (empty($alert_message)) {

        
        $stmt = $koneksi->prepare("INSERT INTO projects 
            (slug, title, subtitle_goal, category, location_text, hero_image_url, 
            stat_capacity, stat_co2_reduction, stat_timeline, stat_investment, 
            overview_result, overview_details, overview_generation, 
            challenges_html, solutions_html, impact_html, tech_specs_json) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssssssssssssss",
            $slug,
            $title,
            $subtitle_goal,
            $category,
            $location_text,
            $hero_image_path_db, 
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

        
        if ($stmt->execute()) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Proyek baru berhasil ditambahkan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        } else {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal menyimpan ke database: ' . $stmt->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah Proyek - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">...</nav>

    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link active" href="projects.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Proyek
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">...</div>
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

                            <form action="project_add.php" method="POST" enctype="multipart/form-data">

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
                                    <label class="small mb-1" for="hero_image_file">Upload Gambar Utama (Hero)</label>
                                    <input class="form-control" id="hero_image_file" name="hero_image_file" type="file"
                                        required>
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