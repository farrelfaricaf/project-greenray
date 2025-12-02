<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

// Ambil Data Home
$stmt = $koneksi->query("SELECT * FROM page_home WHERE id = 1");
// Gunakan ?? [] agar jika data kosong tidak error
$data = $stmt->fetch_assoc() ?? [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil Input (Gunakan ?? '' agar aman jika input tidak terkirim)
    $about_1_title = $_POST['about_1_title'] ?? '';
    $about_1_desc = $_POST['about_1_desc'] ?? '';
    $about_2_title = $_POST['about_2_title'] ?? '';
    $about_2_desc = $_POST['about_2_desc'] ?? '';

    $catalog_title = $_POST['catalog_title'] ?? '';
    $catalog_desc = $_POST['catalog_desc'] ?? '';

    $portfolio_title = $_POST['portfolio_title'] ?? '';
    $portfolio_desc = $_POST['portfolio_desc'] ?? '';

    $review_title = $_POST['review_title'] ?? '';
    $review_desc = $_POST['review_desc'] ?? '';
    $faq_title = $_POST['faq_title'] ?? '';
    $faq_desc = $_POST['faq_desc'] ?? '';

    $home_product_limit = $_POST['home_product_limit'] ?? 6;
    $home_project_limit = $_POST['home_project_limit'] ?? 6;

    // Fungsi Upload
    function uploadAndCompress($fileInputName, $targetDir, $currentImage)
    {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] != 0)
            return $currentImage;
        $source = $_FILES[$fileInputName]['tmp_name'];
        $ext = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));
        $validExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $validExt))
            return $currentImage;
        if (!file_exists($targetDir))
            mkdir($targetDir, 0777, true);
        $fileName = uniqid() . '-' . time() . '.' . $ext;
        $destination = $targetDir . $fileName;
        if (move_uploaded_file($source, $destination)) {
            if (!empty($currentImage) && file_exists("../" . $currentImage))
                unlink("../" . $currentImage);
            return str_replace('../', '', $destination);
        }
        return $currentImage;
    }

    // Proses Gambar
    // Gunakan ?? '' pada $data[...] untuk mencegah error jika kolom gambar belum ada
    $hero_image = uploadAndCompress('hero_image', '../uploads/home/', $data['hero_image'] ?? '');
    $about_1_image = uploadAndCompress('about_1_image', '../uploads/home/', $data['about_1_image'] ?? '');
    $about_2_image = uploadAndCompress('about_2_image', '../uploads/home/', $data['about_2_image'] ?? '');

    // Update Database
    $sql = "UPDATE page_home SET 
            hero_image=?, 
            about_1_title=?, about_1_desc=?, about_1_image=?,
            about_2_title=?, about_2_desc=?, about_2_image=?,
            catalog_title=?, catalog_desc=?,
            portfolio_title=?, portfolio_desc=?,
            review_title=?, review_desc=?,
            faq_title=?, faq_desc=?,
            home_product_limit=?, home_project_limit=?
            WHERE id=1";

    $stmt_upd = $koneksi->prepare($sql);

    $stmt_upd->bind_param(
        "sssssssssssssssii",
        $hero_image,
        $about_1_title,
        $about_1_desc,
        $about_1_image,
        $about_2_title,
        $about_2_desc,
        $about_2_image,
        $catalog_title,
        $catalog_desc,
        $portfolio_title,
        $portfolio_desc,
        $review_title,
        $review_desc,
        $faq_title,
        $faq_desc,
        $home_product_limit,
        $home_project_limit
    );

    if ($stmt_upd->execute()) {
        $alert_message = '<div class="alert alert-success border-0 shadow-sm"><i class="fas fa-check-circle me-2"></i> Halaman Home berhasil diupdate!</div>';
        $stmt = $koneksi->query("SELECT * FROM page_home WHERE id = 1");
        $data = $stmt->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger border-0 shadow-sm"><i class="fas fa-exclamation-circle me-2"></i> Gagal: ' . $koneksi->error . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Home Page</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
    <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 700;
            padding: 1.25rem 1.5rem;
            color: #444;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-hero {
            border-left: 5px solid #198754;
        }

        .header-about {
            border-left: 5px solid #0dcaf0;
        }

        .header-section {
            border-left: 5px solid #ffc107;
        }

        .header-settings {
            border-left: 5px solid #6c757d;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
        }

        .img-preview {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 10px;
            background: #f9f9f9;
        }
    </style>
</head>

<body class="sb-nav-fixed bg-light">
    <?php include 'includes/navbar.php'; ?>
    <div id="layoutSidenav">
        <?php include 'includes/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 pb-5">
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                        <h1 class="h3 text-gray-800">Edit Halaman Home</h1>
                        <a href="../html/home.php" target="_blank"
                            class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Website
                        </a>
                    </div>

                    <?php echo $alert_message; ?>

                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="card">
                            <div class="card-header header-hero text-success">
                                <i class="fas fa-image fa-lg"></i> Banner Utama
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <label class="form-label d-block text-start">Preview Gambar</label>
                                        <?php $img = !empty($data['hero_image']) ? '../' . $data['hero_image'] : 'https://via.placeholder.com/400x200?text=No+Image'; ?>
                                        <img src="<?php echo $img; ?>" class="img-preview">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Ganti Gambar Banner</label>
                                        <input type="file" name="hero_image" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header header-about text-info">
                                <i class="fas fa-info-circle fa-lg"></i> Section About Us
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Bagian 1 (Gambar Kiri)
                                        </h6>
                                        <div class="mb-3"><label class="form-label">Judul</label><input type="text"
                                                name="about_1_title" class="form-control"
                                                value="<?php echo htmlspecialchars($data['about_1_title'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3"><label class="form-label">Deskripsi</label><textarea
                                                name="about_1_desc" class="form-control"
                                                rows="4"><?php echo htmlspecialchars($data['about_1_desc'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3"><label class="form-label">Ganti Gambar</label><input
                                                type="file" name="about_1_image" class="form-control"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-uppercase text-muted small mb-3">Bagian 2 (Gambar Kanan)
                                        </h6>
                                        <div class="mb-3"><label class="form-label">Judul</label><input type="text"
                                                name="about_2_title" class="form-control"
                                                value="<?php echo htmlspecialchars($data['about_2_title'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3"><label class="form-label">Deskripsi</label><textarea
                                                name="about_2_desc" class="form-control"
                                                rows="4"><?php echo htmlspecialchars($data['about_2_desc'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3"><label class="form-label">Ganti Gambar</label><input
                                                type="file" name="about_2_image" class="form-control"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header header-section text-warning">
                                <i class="fas fa-heading fa-lg"></i> Judul & Deskripsi Section
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border">
                                            <h6 class="fw-bold text-primary">Catalog (Produk)</h6>
                                            <div class="mb-2"><label class="form-label small">Judul</label><input
                                                    type="text" name="catalog_title" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['catalog_title'] ?? ''); ?>">
                                            </div>
                                            <div><label class="form-label small">Deskripsi</label><textarea
                                                    name="catalog_desc" class="form-control"
                                                    rows="2"><?php echo htmlspecialchars($data['catalog_desc'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border">
                                            <h6 class="fw-bold text-success">Portfolio (Proyek)</h6>
                                            <div class="mb-2"><label class="form-label small">Judul</label><input
                                                    type="text" name="portfolio_title" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['portfolio_title'] ?? ''); ?>">
                                            </div>
                                            <div><label class="form-label small">Deskripsi</label><textarea
                                                    name="portfolio_desc" class="form-control"
                                                    rows="2"><?php echo htmlspecialchars($data['portfolio_desc'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border">
                                            <h6 class="fw-bold text-info">Reviews</h6>
                                            <div class="mb-2"><label class="form-label small">Judul</label><input
                                                    type="text" name="review_title" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['review_title'] ?? ''); ?>">
                                            </div>
                                            <div><label class="form-label small">Deskripsi</label><textarea
                                                    name="review_desc" class="form-control"
                                                    rows="2"><?php echo htmlspecialchars($data['review_desc'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded border">
                                            <h6 class="fw-bold text-secondary">FAQ</h6>
                                            <div class="mb-2"><label class="form-label small">Judul</label><input
                                                    type="text" name="faq_title" class="form-control"
                                                    value="<?php echo htmlspecialchars($data['faq_title'] ?? ''); ?>">
                                            </div>
                                            <div><label class="form-label small">Deskripsi</label><textarea
                                                    name="faq_desc" class="form-control"
                                                    rows="2"><?php echo htmlspecialchars($data['faq_desc'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header header-settings text-secondary">
                                <i class="fas fa-sliders-h fa-lg"></i> Pengaturan Limit Item
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6 border-end">
                                        <label class="form-label">Maksimal Produk Tampil</label>
                                        <input type="number" name="home_product_limit" class="form-control"
                                            value="<?php echo $data['home_product_limit'] ?? 6; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Maksimal Proyek Tampil</label>
                                        <input type="number" name="home_project_limit" class="form-control"
                                            value="<?php echo $data['home_project_limit'] ?? 6; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold">SIMPAN PERUBAHAN</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>