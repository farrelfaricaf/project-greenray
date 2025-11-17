<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$project_id = null;
$project = [];


if (isset($_GET['id'])) {
    
    $project_id = (int) $_GET['id']; 

    
    $sql_select = "SELECT * FROM projects WHERE id = $project_id";
    $result = $koneksi->query($sql_select);

    if ($result && $result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Proyek tidak ditemukan!</div>';
    }
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Proyek tidak valid.</div>';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $project_id = (int) $_POST['project_id'];
    
    $slug = $koneksi->real_escape_string($_POST['slug']);
    $title = $koneksi->real_escape_string($_POST['title']);
    $subtitle_goal = $koneksi->real_escape_string($_POST['subtitle_goal']);
    $category = $koneksi->real_escape_string($_POST['category']);
    $location_text = $koneksi->real_escape_string($_POST['location_text']);

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

    $current_hero_image = $_POST['current_hero_image']; 
    $hero_image_path_db = $current_hero_image; 

    
    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] == 0 && $_FILES['hero_image_file']['size'] > 0) {
        $target_dir = "../uploads/projects/";
        $file_name = uniqid() . '-' . basename($_FILES["hero_image_file"]["name"]);
        $target_file = $target_dir . $file_name;

        $check = getimagesize($_FILES["hero_image_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["hero_image_file"]["tmp_name"], $target_file)) {
                $hero_image_path_db = $koneksi->real_escape_string("uploads/projects/" . $file_name);
                
                if (!empty($current_hero_image) && file_exists("../" . $current_hero_image)) {
                    unlink("../" . $current_hero_image);
                }
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file baru.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File baru bukan gambar.</div>';
        }
    }
    

    
    if (empty($alert_message)) {
        $sql_update = "UPDATE projects SET 
            slug = '$slug', title = '$title', subtitle_goal = '$subtitle_goal', 
            category = '$category', location_text = '$location_text', hero_image_url = '$hero_image_path_db', 
            stat_capacity = '$stat_capacity', stat_co2_reduction = '$stat_co2_reduction', 
            stat_timeline = '$stat_timeline', stat_investment = '$stat_investment', 
            overview_result = '$overview_result', overview_details = '$overview_details', 
            overview_generation = '$overview_generation', challenges_html = '$challenges_html', 
            solutions_html = '$solutions_html', impact_html = '$impact_html', 
            tech_specs_json = '$tech_specs_json' 
            WHERE id = $project_id";

        
        if ($koneksi->query($sql_update) === TRUE) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Proyek berhasil diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';

            
            $result = $koneksi->query("SELECT * FROM projects WHERE id = $project_id");
            $project = $result->fetch_assoc();

        } else {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal memperbarui: ' . $koneksi->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        }
    }
}


if (empty($project)) {
    $project = array_fill_keys([
        'slug',
        'title',
        'subtitle_goal',
        'category',
        'location_text',
        'hero_image_url',
        'stat_capacity',
        'stat_co2_reduction',
        'stat_timeline',
        'stat_investment',
        'overview_result',
        'overview_details',
        'overview_generation',
        'challenges_html',
        'solutions_html',
        'impact_html',
        'tech_specs_json'
    ], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Proyek #<?php echo $project_id; ?> - GreenRay Admin</title>
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

                    <h1 class="mt-4">Edit Proyek</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="projects.php">Data Proyek</a></li>
                        <li class="breadcrumb-item active">Edit Proyek #<?php echo $project_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($project_id) && !empty($project)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit Proyek (ID: <?php echo $project_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="project_edit.php?id=<?php echo $project_id; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                    <input type="hidden" name="current_hero_image"
                                        value="<?php echo htmlspecialchars($project['hero_image_url']); ?>">

                                    <h5 class="mt-3 text-dark">Info Dasar</h5>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="title">Judul Proyek</label>
                                            <input class="form-control" id="title" name="title" type="text"
                                                value="<?php echo htmlspecialchars($project['title']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="slug">Slug (untuk URL)</label>
                                            <input class="form-control" id="slug" name="slug" type="text"
                                                value="<?php echo htmlspecialchars($project['slug']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="subtitle_goal">Subjudul / Goal</label>
                                        <input class="form-control" id="subtitle_goal" name="subtitle_goal" type="text"
                                            value="<?php echo htmlspecialchars($project['subtitle_goal']); ?>" required>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="category">Kategori</label>
                                            <input class="form-control" id="category" name="category" type="text"
                                                value="<?php echo htmlspecialchars($project['category']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="location_text">Lokasi Teks</label>
                                            <input class="form-control" id="location_text" name="location_text" type="text"
                                                value="<?php echo htmlspecialchars($project['location_text']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1">Gambar Saat Ini:</label><br>
                                        <?php if (!empty($project['hero_image_url'])): ?>
                                            <img src="../<?php echo htmlspecialchars($project['hero_image_url']); ?>"
                                                alt="Gambar Hero Saat Ini"
                                                style="height: 100px; border-radius: 5px; border: 1px solid #ddd;">
                                        <?php else: ?>
                                            <small class="text-muted">Belum ada gambar.</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="hero_image_file">Upload Gambar Utama Baru
                                            (Opsional)</label>
                                        <input class="form-control" id="hero_image_file" name="hero_image_file" type="file">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                                    </div>

                                    <h5 class="mt-4 text-dark">Statistik (4 Kartu)</h5>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-3">
                                            <label class="small mb-1" for="stat_capacity">Kapasitas</label>
                                            <input class="form-control" id="stat_capacity" name="stat_capacity" type="text"
                                                value="<?php echo htmlspecialchars($project['stat_capacity']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small mb-1" for="stat_co2_reduction">Reduksi CO2</label>
                                            <input class="form-control" id="stat_co2_reduction" name="stat_co2_reduction"
                                                type="text"
                                                value="<?php echo htmlspecialchars($project['stat_co2_reduction']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small mb-1" for="stat_timeline">Timeline</label>
                                            <input class="form-control" id="stat_timeline" name="stat_timeline" type="text"
                                                value="<?php echo htmlspecialchars($project['stat_timeline']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small mb-1" for="stat_investment">Investasi</label>
                                            <input class="form-control" id="stat_investment" name="stat_investment"
                                                type="text"
                                                value="<?php echo htmlspecialchars($project['stat_investment']); ?>">
                                        </div>
                                    </div>

                                    <h5 class="mt-4 text-dark">Project Overview</h5>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="overview_result">Result</label>
                                        <input class="form-control" id="overview_result" name="overview_result" type="text"
                                            value="<?php echo htmlspecialchars($project['overview_result']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="overview_details">Details</label>
                                        <input class="form-control" id="overview_details" name="overview_details"
                                            type="text"
                                            value="<?php echo htmlspecialchars($project['overview_details']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="overview_generation">Energy Generation</label>
                                        <textarea class="form-control" id="overview_generation" name="overview_generation"
                                            rows="3"><?php echo htmlspecialchars($project['overview_generation']); ?></textarea>
                                    </div>

                                    <h5 class="mt-4 text-dark">Detail Lainnya (HTML/JSON)</h5>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="challenges_html">Challenges (HTML List)</label>
                                        <textarea class="form-control" id="challenges_html" name="challenges_html"
                                            rows="4"><?php echo htmlspecialchars($project['challenges_html']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="solutions_html">Solutions (HTML List)</label>
                                        <textarea class="form-control" id="solutions_html" name="solutions_html"
                                            rows="4"><?php echo htmlspecialchars($project['solutions_html']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="impact_html">Impact (HTML List)</label>
                                        <textarea class="form-control" id="impact_html" name="impact_html"
                                            rows="4"><?php echo htmlspecialchars($project['impact_html']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="tech_specs_json">Technical Specs (JSON)</label>
                                        <textarea class="form-control" id="tech_specs_json" name="tech_specs_json"
                                            rows="4"><?php echo htmlspecialchars($project['tech_specs_json']); ?></textarea>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Update Proyek</button>
                                    <a href="projects.php" class="btn btn-secondary">Kembali ke Daftar</a>
                                </form>
                            </div>
                        </div>
                    <?php endif;?> 
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