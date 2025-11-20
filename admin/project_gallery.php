<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$project_id = null;
$project_title = "";


if (isset($_GET['id'])) {
    $project_id = $_GET['id'];


    $stmt_title = $koneksi->prepare("SELECT title FROM projects WHERE id = ?");
    $stmt_title->bind_param("i", $project_id);
    $stmt_title->execute();
    $result_title = $stmt_title->get_result();
    if ($row_title = $result_title->fetch_assoc()) {
        $project_title = $row_title['title'];
    } else {
        die("Error: Proyek tidak ditemukan.");
    }
    $stmt_title->close();

} else {
    die("Error: ID Proyek tidak valid.");
}


if (isset($_GET['delete_image_id'])) {
    $image_id_to_delete = $_GET['delete_image_id'];


    $stmt_select = $koneksi->prepare("SELECT image_url FROM project_gallery_images WHERE id = ? AND project_id = ?");
    $stmt_select->bind_param("ii", $image_id_to_delete, $project_id);
    $stmt_select->execute();
    $result_img = $stmt_select->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        $image_path = "../" . $row_img['image_url'];
        if (file_exists($image_path) && !empty($row_img['image_url'])) {
            unlink($image_path);
        }


        $stmt_delete = $koneksi->prepare("DELETE FROM project_gallery_images WHERE id = ?");
        $stmt_delete->bind_param("i", $image_id_to_delete);
        $stmt_delete->execute();
        $stmt_delete->close();

        $alert_message = '<div class="alert alert-success">Gambar berhasil dihapus.</div>';
    }
    $stmt_select->close();

    header("Location: project_gallery.php?id=" . $project_id);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['gallery_image_file'])) {

    $image_path_db = "";


    if (isset($_FILES['gallery_image_file']) && $_FILES['gallery_image_file']['error'] == 0) {

        $target_dir = "../uploads/projects/";
        $file_name = uniqid() . '-gallery-' . basename($_FILES["gallery_image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["gallery_image_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["gallery_image_file"]["tmp_name"], $target_file)) {
                $image_path_db = "uploads/projects/" . $file_name;


                $stmt_insert = $koneksi->prepare("INSERT INTO project_gallery_images (project_id, image_url, alt_text) VALUES (?, ?, ?)");
                $alt_text = "Galeri " . $project_title;
                $stmt_insert->bind_param("iss", $project_id, $image_path_db, $alt_text);

                if ($stmt_insert->execute()) {
                    $alert_message = '<div class="alert alert-success">Gambar baru berhasil di-upload.</div>';
                } else {
                    $alert_message = '<div class="alert alert-danger">Error: Gagal menyimpan path ke DB.</div>';
                }
                $stmt_insert->close();

            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File bukan gambar.</div>';
        }
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Tidak ada file yang dipilih atau terjadi error upload.</div>';
    }
}


$gallery_images = [];
$stmt_gallery_list = $koneksi->prepare("SELECT * FROM project_gallery_images WHERE project_id = ? ORDER BY id DESC");
$stmt_gallery_list->bind_param("i", $project_id);
$stmt_gallery_list->execute();
$result_gallery_list = $stmt_gallery_list->get_result();
while ($row = $result_gallery_list->fetch_assoc()) {
    $gallery_images[] = $row;
}
$stmt_gallery_list->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Galeri Proyek - GreenRay Admin</title>
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

                    <h1 class="mt-4">Manajemen Galeri Proyek</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="projects.php">Data Proyek</a></li>
                        <li class="breadcrumb-item active">Galeri untuk: <?php echo htmlspecialchars($project_title); ?>
                        </li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-upload me-1"></i>
                            Upload Foto Baru
                        </div>
                        <div class="card-body">
                            <form action="project_gallery.php?id=<?php echo $project_id; ?>" method="POST"
                                enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="small mb-1" for="gallery_image_file">Pilih Gambar</label>
                                    <input class="form-control" id="gallery_image_file" name="gallery_image_file"
                                        type="file" required>
                                    <small class="text-muted">Upload satu per satu gambar untuk galeri proyek
                                        ini.</small>
                                </div>
                                <button class="btn btn-primary" type="submit">Upload Gambar</button>
                                <a href="projects.php" class="btn btn-secondary">Kembali ke Daftar Proyek</a>
                            </form>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-images me-1"></i>
                            Galeri Saat Ini (<?php echo count($gallery_images); ?> foto)
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (empty($gallery_images)): ?>
                                    <div class="col-12 text-center">Belum ada gambar di galeri ini.</div>
                                <?php else: ?>
                                    <?php foreach ($gallery_images as $image): ?>
                                        <div class="col-md-3 mb-3 text-center">
                                            <img src="../<?php echo htmlspecialchars($image['image_url']); ?>"
                                                alt="<?php echo htmlspecialchars($image['alt_text']); ?>"
                                                class="img-thumbnail mb-2"
                                                style="height: 150px; width: 100%; object-fit: cover;">
                                            <br>
                                            <a href="project_gallery.php?id=<?php echo $project_id; ?>&delete_image_id=<?php echo $image['id']; ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus gambar ini?');">
                                                Hapus
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
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
</body>

</html>