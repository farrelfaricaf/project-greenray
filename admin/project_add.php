<?php

include '../koneksi.php';

$alert_message = "";

// ======================================================
// FUNGSI HELPER BARU
// ======================================================
/**
 * Mengubah teks dengan format baris baru menjadi daftar HTML.
 * @param string $plain_text Teks dari textarea (cth: "Poin 1\nPoin 2")
 * @return string Teks dalam format HTML (cth: "<ul><li>Poin 1</li><li>Poin 2</li></ul>")
 */
function convertNewlinesToHtmlList($plain_text)
{
    // 1. Ganti semua jenis newline (Windows \r\n, Mac \r) menjadi \n
    $normalized_text = str_replace(["\r\n", "\r"], "\n", $plain_text);

    // 2. Pecah teks berdasarkan \n
    $lines = explode("\n", $normalized_text);

    // 3. Hapus baris kosong dan spasi
    $lines = array_filter(array_map('trim', $lines));

    if (empty($lines)) {
        return '';
    }

    // 4. Bungkus dengan <li>
    $li_items = array_map(function ($line) {
        return '<li class="mb-2">' . htmlspecialchars($line) . '</li>'; // mb-2 untuk spasi
    }, $lines);

    // 5. Gabungkan <li> saja
    return implode('', $li_items);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $hero_image_path_db = "";

    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] == 0) {
        // ... (Logika upload file kamu tetap sama) ...
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

    // ======================================================
    // PERUBAHAN DI SINI: Konversi teks biasa ke HTML
    // ======================================================
    $challenges_html = convertNewlinesToHtmlList($_POST['challenges_html']);
    $solutions_html = convertNewlinesToHtmlList($_POST['solutions_html']);
    $impact_html = convertNewlinesToHtmlList($_POST['impact_html']);
    // ======================================================

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
            $impact_html, // Variabel yang sudah dikonversi
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
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>

<body class="sb-nav-fixed">
    <?php include 'includes/navbar.php'; ?>

    <div id="layoutSidenav">
        <?php include 'includes/sidebar.php'; ?>

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
                                    <label class="small mb-1" for="challenges_html">Challenges</label>
                                    <textarea class="form-control" id="challenges_html" name="challenges_html" rows="4"
                                        placeholder="Tulis per baris. Setiap baris baru akan menjadi satu poin list."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="solutions_html">Solutions</label>
                                    <textarea class="form-control" id="solutions_html" name="solutions_html" rows="4"
                                        placeholder="Tulis per baris. Setiap baris baru akan menjadi satu poin list."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="impact_html">Impact</label>
                                    <textarea class="form-control" id="impact_html" name="impact_html" rows="4"
                                        placeholder="Tulis per baris. Setiap baris baru akan menjadi satu poin list."></textarea>
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
                                    <button class="btn btn-primary" type="submit">Simpan Proyek</button>
                                    <a href="projects.php" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- Script Slugify (dari sebelumnya) ---
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

            // --- Script untuk Dynamic Tech Specs (BARU) ---
            const container = document.getElementById('tech-specs-container');
            const addBtn = document.getElementById('add-spec-btn');
            const hiddenInput = document.getElementById('tech_specs_json');
            const form = document.getElementById('project-form');

            // Fungsi untuk membuat baris input baru
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

                // Tambahkan event listener untuk tombol hapus
                row.querySelector('.remove-spec-btn').addEventListener('click', function () {
                    row.remove();
                });

                container.appendChild(row);
            }

            // Event listener untuk tombol "Tambah Spesifikasi"
            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    createSpecRow(); // Tambah baris kosong
                });
            }

            // Event listener untuk form submit
            if (form) {
                form.addEventListener('submit', function (e) {
                    const specs = [];
                    const rows = container.querySelectorAll('.dynamic-spec-row');

                    rows.forEach(row => {
                        const label = row.querySelector('.spec-label').value.trim();
                        const value = row.querySelector('.spec-value').value.trim();

                        if (label && value) { // Hanya simpan jika keduanya diisi
                            specs.push({
                                label: label,
                                value: value
                            });
                        }
                    });

                    // Ubah array objek menjadi string JSON dan masukkan ke input tersembunyi
                    hiddenInput.value = JSON.stringify(specs);
                });
            }
        });
    </script>
</body>

</html>