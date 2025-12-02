<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

// AMBIL DATA SAAT INI
$stmt = $koneksi->query("SELECT * FROM page_portfolio WHERE id = 1");
$data = $stmt->fetch_assoc();

// JIKA TOMBOL SIMPAN DITEKAN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $header_title = $_POST['header_title'];
    $header_desc = $_POST['header_desc'];
    $projects_title = $_POST['projects_title']; // <-- DATA BARU

    // Fungsi Upload & Kompresi
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

        $fileName = uniqid() . '-portf-' . time() . '.' . $ext;
        $destination = $targetDir . $fileName;

        if (move_uploaded_file($source, $destination)) {
            if (!empty($currentImage) && file_exists("../" . $currentImage) && strpos($currentImage, 'default') === false) {
                unlink("../" . $currentImage);
            }
            return str_replace('../', '', $destination);
        }
        return $currentImage;
    }

    // Proses Upload Gambar Header
    $header_image = uploadAndCompress('header_image', '../uploads/portfolio/', $data['header_image']);

    // Update Database (Tambahkan projects_title)
    $stmt_upd = $koneksi->prepare("UPDATE page_portfolio SET header_title=?, header_desc=?, header_image=?, projects_title=? WHERE id=1");
    $stmt_upd->bind_param("ssss", $header_title, $header_desc, $header_image, $projects_title);

    if ($stmt_upd->execute()) {
        $alert_message = '<div class="alert alert-success border-0 shadow-sm"><i class="fas fa-check-circle me-2"></i> Halaman Portfolio berhasil diperbarui!</div>';
        $stmt = $koneksi->query("SELECT * FROM page_portfolio WHERE id = 1");
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
    <title>Edit Halaman Portfolio</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
    <style>
        /* Style Minimalis (Sama dengan Home Edit) */
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

        .header-banner {
            border-left: 5px solid #0d6efd;
            /* Warna Biru */
        }

        .img-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 10px;
            background: #f9f9f9;
        }

        .form-label {
            font-weight: 600;
            color: #555;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
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
                        <h1 class="h3 text-gray-800">Edit Halaman Portfolio</h1>
                        <a href="../html/portofolio.php" target="_blank"
                            class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Website
                        </a>
                    </div>

                    <?php echo $alert_message; ?>

                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="card">
                            <div class="card-header header-banner text-primary">
                                <i class="fas fa-images fa-lg"></i> Header & Deskripsi Utama
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Halaman</label>
                                            <input type="text" name="header_title" class="form-control"
                                                value="<?php echo htmlspecialchars($data['header_title']); ?>"
                                                placeholder="Contoh: Our Projects">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi Singkat</label>
                                            <textarea name="header_desc" class="form-control" rows="4"
                                                placeholder="Jelaskan tentang proyek-proyek Anda..."><?php echo htmlspecialchars($data['header_desc']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label">Gambar Header / Banner</label>
                                        <?php
                                        $img = !empty($data['header_image']) ? '../' . $data['header_image'] : 'https://via.placeholder.com/600x300?text=No+Image';
                                        ?>
                                        <img src="<?php echo $img; ?>" class="img-preview">
                                        <input type="file" name="header_image" class="form-control mt-2">
                                        <small class="text-muted">Format: JPG, PNG, WEBP. Max lebar disarankan:
                                            1230px.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header header-section text-success">
                                <i class="fas fa-heading fa-lg"></i> Judul Section Proyek
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Judul (Di atas Grid Proyek)</label>
                                    <input type="text" name="projects_title" class="form-control"
                                        value="<?php echo htmlspecialchars($data['projects_title'] ?? 'Solar Panel Installation Projects'); ?>">
                                    <small class="text-muted">Teks ini muncul di tengah halaman, di atas daftar
                                        proyek.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow fw-bold py-3">
                                <i class="fas fa-save me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>

                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>