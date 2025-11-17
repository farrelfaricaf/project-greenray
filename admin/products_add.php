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
        $check = getimagesize($_FILES["hero_image_file"]["tmp_name"]);

        if ($check !== false) {
            if (move_uploaded_file($_FILES["hero_image_file"]["tmp_name"], $target_file)) {
                $hero_image_path_db = "uploads/projects/" . $file_name;
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File bukan gambar.</div>';
        }
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Gambar utama wajib di-upload.</div>';
    }
    

    
    $slug = $koneksi->real_escape_string($_POST['slug']);
    $title = $koneksi->real_escape_string($_POST['title']);
    $subtitle_goal = $koneksi->real_escape_string($_POST['subtitle_goal']);
    $category = $koneksi->real_escape_string($_POST['category']);
    $location_text = $koneksi->real_escape_string($_POST['location_text']);
    $hero_image_path_db = $koneksi->real_escape_string($hero_image_path_db); 

    $stat_capacity = $koneksi->real_escape_string($_POST['stat_capacity']);
    $stat_co2_reduction = $koneksi->real_escape_string($_POST['stat_co2_reduction']);
    $stat_timeline = $koneksi->real_escape_string($_POST['stat_timeline']);
    $stat_investment = $koneksi->real_escape_string($_POST['stat_investment']);

    $overview_result = $koneksi->real_escape_string($_POST['overview_result']);
    $overview_details = $koneksi->real_escape_string($_POST['overview_details']);
    $overview_generation = $koneksi->real_escape_string($_POST['overview_generation']);

    $challenges_html = $koneksi->real_escape_string($_POST['challenges_html']);
    $solutions_html = $koneksi->real_escape_string($_POST['solutions_html']);
    $impact_html = $koneksi->real_escape_string($_POST['impact_html']);
    $tech_specs_json = $koneksi->real_escape_string($_POST['tech_specs_json']);

    
    if (empty($alert_message)) {
        $sql_insert = "INSERT INTO projects 
            (slug, title, subtitle_goal, category, location_text, hero_image_url, 
            stat_capacity, stat_co2_reduction, stat_timeline, stat_investment, 
            overview_result, overview_details, overview_generation, 
            challenges_html, solutions_html, impact_html, tech_specs_json) 
            VALUES 
            ('$slug', '$title', '$subtitle_goal', '$category', '$location_text', '$hero_image_path_db',
            '$stat_capacity', '$stat_co2_reduction', '$stat_timeline', '$stat_investment',
            '$overview_result', '$overview_details', '$overview_generation',
            '$challenges_html', '$solutions_html', '$impact_html', '$tech_specs_json')";

        
        if ($koneksi->query($sql_insert) === TRUE) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Proyek baru berhasil ditambahkan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        } else {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal menyimpan ke database: ' . $koneksi->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        }
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
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
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
                                <h5 class="mt-4 text-dark">Project Overview</h5>
                                <h5 class="mt-4 text-dark">Detail Lainnya (HTML/JSON)</h5>
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