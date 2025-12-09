<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$project_id = null;
$project = [];


function convertNewlinesToHtmlList($plain_text)
{
    $normalized_text = str_replace(["\r\n", "\r"], "\n", $plain_text);
    $lines = explode("\n", $normalized_text);
    $lines = array_filter(array_map('trim', $lines));
    if (empty($lines)) {
        return '';
    }
    $li_items = array_map(function ($line) {
        return '<li class="mb-2">' . htmlspecialchars($line) . '</li>';
    }, $lines);
    return implode('', $li_items);
}
function convertHtmlListToNewlines($html)
{
    if (empty(trim($html))) {
        return '';
    }
    $text = html_entity_decode($html);
    $text = preg_replace('/<\/li>\s*/i', "\n", $text);
    $text = strip_tags($text);
    return trim($text);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_hero_image_path = $_POST['current_hero_image'];
    $hero_image_path_db = $current_hero_image_path;

    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] == 0 && $_FILES['hero_image_file']['size'] > 0) {
        
        $target_dir = "../uploads/projects/";
        $file_name = uniqid() . '-' . basename($_FILES["hero_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $check = getimagesize($_FILES["hero_image_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["hero_image_file"]["tmp_name"], $target_file)) {
                $hero_image_path_db = "uploads/projects/" . $file_name;
                if (!empty($current_hero_image_path) && file_exists("../" . $current_hero_image_path)) {
                    unlink("../" . $current_hero_image_path);
                }
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File bukan gambar.</div>';
        }
    }

    $project_id = $_POST['project_id'];
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
    $challenges_html = convertNewlinesToHtmlList($_POST['challenges_html']);
    $solutions_html = convertNewlinesToHtmlList($_POST['solutions_html']);
    $impact_html = convertNewlinesToHtmlList($_POST['impact_html']);

    
    $tech_specs_json = $_POST['tech_specs_json'];

    if (empty($alert_message)) {
        $stmt_update = $koneksi->prepare("UPDATE projects SET 
            slug = ?, title = ?, subtitle_goal = ?, category = ?, location_text = ?, hero_image_url = ?, 
            stat_capacity = ?, stat_co2_reduction = ?, stat_timeline = ?, stat_investment = ?, 
            overview_result = ?, overview_details = ?, overview_generation = ?, 
            challenges_html = ?, solutions_html = ?, impact_html = ?, tech_specs_json = ? 
            WHERE id = ?");
        $stmt_update->bind_param(
            "sssssssssssssssssi",
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
            $tech_specs_json, 
            $project_id
        );
        if ($stmt_update->execute()) {
            $alert_message = '<div class="alert alert-success">Proyek berhasil diperbarui.</div>';
        } else {
            $alert_message = '<div class="alert alert-danger">Error: ' . $stmt_update->error . '</div>';
        }
        $stmt_update->close();
    }
}


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    if (isset($_GET['id'])) {
        $project_id = $_GET['id'];
        $stmt_select = $koneksi->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt_select->bind_param("i", $project_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($result->num_rows > 0) {
            $project = $result->fetch_assoc();
        } else {
            $alert_message = '<div class="alert alert-danger">Error: Proyek tidak ditemukan!</div>';
            $project_id = null;
        }
        $stmt_select->close();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: ID Proyek tidak valid.</div>';
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && $project_id && empty($project)) {
    $stmt_select = $koneksi->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt_select->bind_param("i", $project_id);
    $stmt_select->execute();
    $project = $stmt_select->get_result()->fetch_assoc();
    $stmt_select->close();
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
    <?php include 'includes/navbar.php'; ?>

    <div id="layoutSidenav"> 
        <?php include 'includes/sidebar.php'; ?>

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

                    <?php if ($project_id && !empty($project)): ?>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-edit me-1"></i> Formulir Edit Proyek</div>
                            <div class="card-body">
                                <form action="project_edit.php?id=<?php echo $project_id; ?>" method="POST"
                                    enctype="multipart/form-data" id="project-form">
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
                                                alt="Gambar Hero" style="height: 100px; border-radius: 5px;">
                                        <?php else: ?><small class="text-muted">Belum ada gambar.</small><?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="hero_image_file">Upload Gambar Utama Baru
                                            (Opsional)</label>
                                        <input class="form-control" id="hero_image_file" name="hero_image_file" type="file">
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

                                    <h5 class="mt-4 text-dark">Detail Lainnya</h5>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="challenges_html">Challenges</label>
                                        <textarea class="form-control" id="challenges_html" name="challenges_html"
                                            rows="4"><?php echo convertHtmlListToNewlines($project['challenges_html']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="solutions_html">Solutions</label>
                                        <textarea class="form-control" id="solutions_html" name="solutions_html"
                                            rows="4"><?php echo convertHtmlListToNewlines($project['solutions_html']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="impact_html">Impact</label>
                                        <textarea class="form-control" id="impact_html" name="impact_html"
                                            rows="4"><?php echo convertHtmlListToNewlines($project['impact_html']); ?></textarea>
                                    </div>

                                    <h5 class="mt-4 text-dark">Technical Specifications</h5>
                                    <div id="tech-specs-container">
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-success mt-2" id="add-spec-btn">
                                            <i class="fas fa-plus me-1"></i> Tambah Spesifikasi
                                        </button>
                                    </div>
                                    <input type="hidden" id="tech_specs_json" name="tech_specs_json">

                                    <div class="mt-4 border-top pt-3">
                                        <button class="btn btn-primary" type="submit">Update Proyek</button>
                                        <a href="projects.php" class="btn btn-secondary">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Tidak dapat memuat data proyek.</div>
                        <a href="projects.php" class="btn btn-secondary">Kembali ke Daftar</a>
                    <?php endif; ?>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <script>
        
        const existingSpecs = <?php echo !empty($project['tech_specs_json']) ? $project['tech_specs_json'] : '[]'; ?>;

        document.addEventListener('DOMContentLoaded', function () {

            
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            if (titleInput && slugInput) {
                function slugify(text) {
                    return text.toString().toLowerCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^\w\-]+/g, '')
                        .replace(/\-\-+/g, '-')
                        .replace(/^-+/, '')
                        .replace(/-+$/, '');
                }
                titleInput.addEventListener('input', function () {
                    slugInput.value = slugify(titleInput.value);
                });
            }

            
            const container = document.getElementById('tech-specs-container');
            const addBtn = document.getElementById('add-spec-btn');
            const hiddenInput = document.getElementById('tech_specs_json');
            const form = document.getElementById('project-form');

            function createSpecRow(label = '', value = '') {
                const row = document.createElement('div');
                row.className = 'row gx-2 mb-2 dynamic-spec-row';
                row.innerHTML = `
                    <div class="col-md-5">
                        <label class="small mb-1">Label</label>
                        <input class="form-control spec-label" type="text" placeholder="Cth: Tipe Panel" value="${label}">
                    </div>
                    <div class="col-md-5">
                        <label class="small mb-1">Value</label>
                        <input class="form-control spec-value" type="text" placeholder="Cth: Monocrystalline 400W" value="${value}">
                    </div>
                    <div class="col-md-2">
                        <label class="small mb-1 d-block">&nbsp;</label>
                        <button type="button" class="btn btn-danger w-100 remove-spec-btn">Hapus</button>
                    </div>
                `;

                row.querySelector('.remove-spec-btn').addEventListener('click', function () {
                    row.remove();
                });
                container.appendChild(row);
            }

            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    createSpecRow();
                });
            }

            
            if (existingSpecs && Array.isArray(existingSpecs)) {
                existingSpecs.forEach(spec => {
                    
                    if (spec.label && spec.value) {
                        createSpecRow(spec.label, spec.value);
                    }
                });
            }

            
            if (form) {
                form.addEventListener('submit', function (e) {
                    const specs = [];
                    const rows = container.querySelectorAll('.dynamic-spec-row');
                    rows.forEach(row => {
                        const label = row.querySelector('.spec-label').value.trim();
                        const value = row.querySelector('.spec-value').value.trim();
                        if (label && value) {
                            specs.push({
                                label: label,
                                value: value
                            });
                        }
                    });
                    hiddenInput.value = JSON.stringify(specs);
                });
            }
        });
    </script>
</body>

</html>