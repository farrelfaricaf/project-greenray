<?php
include '../koneksi.php';
include 'auth_check.php';

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: products.php");
    exit;
}


$stmt_prod = $koneksi->prepare("SELECT name FROM products WHERE id = ?");
$stmt_prod->bind_param("i", $product_id);
$stmt_prod->execute();
$product = $stmt_prod->get_result()->fetch_assoc();

if (!$product) {
    die("Produk tidak ditemukan.");
}

$alert_message = "";


if (isset($_POST['upload_images'])) {
    $target_dir = "../uploads/products/gallery/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $count_success = 0;
    $total_files = count($_FILES['gallery_files']['name']);

    for ($i = 0; $i < $total_files; $i++) {
        if ($_FILES['gallery_files']['error'][$i] == 0) {
            $file_extension = strtolower(pathinfo($_FILES['gallery_files']['name'][$i], PATHINFO_EXTENSION));
            $new_name = uniqid() . '-gallery-' . $i . '.' . $file_extension;
            $target_file = $target_dir . $new_name;

            if (move_uploaded_file($_FILES['gallery_files']['tmp_name'][$i], $target_file)) {
                $db_path = "uploads/products/gallery/" . $new_name;

                $stmt_ins = $koneksi->prepare("INSERT INTO product_gallery_images (product_id, image_url) VALUES (?, ?)");
                $stmt_ins->bind_param("is", $product_id, $db_path);
                $stmt_ins->execute();
                $count_success++;
            }
        }
    }

    if ($count_success > 0) {
        $alert_message = '<div class="alert alert-success">' . $count_success . ' gambar berhasil ditambahkan.</div>';
    } else {
        $alert_message = '<div class="alert alert-warning">Tidak ada gambar yang diupload.</div>';
    }
}


if (isset($_GET['delete_img'])) {
    $img_id = $_GET['delete_img'];

    
    $stmt_get = $koneksi->prepare("SELECT image_url FROM product_gallery_images WHERE id = ? AND product_id = ?");
    $stmt_get->bind_param("ii", $img_id, $product_id);
    $stmt_get->execute();
    $img_data = $stmt_get->get_result()->fetch_assoc();

    if ($img_data) {
        if (file_exists("../" . $img_data['image_url'])) {
            unlink("../" . $img_data['image_url']);
        }

        $stmt_del = $koneksi->prepare("DELETE FROM product_gallery_images WHERE id = ?");
        $stmt_del->bind_param("i", $img_id);
        $stmt_del->execute();

        $alert_message = '<div class="alert alert-success">Gambar berhasil dihapus.</div>';
    }
}


$gallery_images = [];
$stmt_gal = $koneksi->prepare("SELECT * FROM product_gallery_images WHERE product_id = ? ORDER BY id DESC");
$stmt_gal->bind_param("i", $product_id);
$stmt_gal->execute();
$result_gal = $stmt_gal->get_result();
while ($row = $result_gal->fetch_assoc()) {
    $gallery_images[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Galeri Produk: <?php echo htmlspecialchars($product['name']); ?></title>
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
                    <h1 class="mt-4">Kelola Galeri Produk</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">Upload Gambar Baru (Bisa Pilih Banyak)</div>
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="input-group">
                                    <input type="file" name="gallery_files[]" class="form-control" multiple required>
                                    <button class="btn btn-primary" type="submit" name="upload_images">Upload</button>
                                </div>
                                <small class="text-muted">Tekan CTRL saat memilih file untuk upload banyak
                                    sekaligus.</small>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (empty($gallery_images)): ?>
                            <div class="col-12">
                                <p class="text-center text-muted">Belum ada gambar galeri.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($gallery_images as $img): ?>
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100">
                                        <img src="../<?php echo $img['image_url']; ?>" class="card-img-top"
                                            style="height: 150px; object-fit: cover;">
                                        <div class="card-body text-center">
                                            <a href="product_gallery.php?id=<?php echo $product_id; ?>&delete_img=<?php echo $img['id']; ?>"
                                                class="btn btn-sm btn-danger" onclick="return confirm('Hapus gambar ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="mb-5">
                        <a href="products.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
                    </div>

                </div>
            </main>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>