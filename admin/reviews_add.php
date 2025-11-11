<?php

include '../koneksi.php';

$alert_message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $image_path_db = ""; 

    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "../uploads/reviews/"; 
        $file_name = uniqid() . '-' . basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        $check = getimagesize($_FILES["image_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                
                $image_path_db = "uploads/reviews/" . $file_name; 
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File bukan gambar.</div>';
        }
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Foto customer wajib di-upload.</div>';
    }
    

    
    $customer_name = $_POST['customer_name'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    
    if ($alert_message == "") {
        
        $stmt = $koneksi->prepare("INSERT INTO reviews (customer_name, review_text, rating, image_url, is_visible) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssisi",
            $customer_name,
            $review_text,
            $rating,
            $image_path_db,
            $is_visible
        );

        
        if ($stmt->execute()) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Review baru berhasil ditambahkan.
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
    <title>Tambah Review - GreenRay Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">...</nav>

    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link active" href="reviews.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                            Reviews
                        </a>
                        <a class="nav-link" href="faqs.php">... FAQ</a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">...</div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">

                    <h1 class="mt-4">Tambah Review Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="reviews.php">Data Reviews</a></li>
                        <li class="breadcrumb-item active">Tambah Review</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir Review Baru
                        </div>
                        <div class="card-body">
                            <form action="reviews_add.php" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <label class="small mb-1" for="customer_name">Nama Customer</label>
                                    <input class="form-control" id="customer_name" name="customer_name" type="text"
                                        placeholder="Cth: Budi Santoso" required>
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="review_text">Isi Review</label>
                                    <textarea class="form-control" id="review_text" name="review_text" rows="4"
                                        placeholder="Tulis isi review di sini..."></textarea>
                                </div>

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="rating">Rating (Angka 1-5)</label>
                                        <input class="form-control" id="rating" name="rating" type="number" min="1"
                                            max="5" placeholder="Cth: 5" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="image_file">Foto Customer</label>
                                        <input class="form-control" id="image_file" name="image_file" type="file"
                                            required>
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" id="is_visible" name="is_visible" type="checkbox"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_visible">
                                        Tampilkan di Website? (Visible)
                                    </label>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Review</button>
                                <a href="reviews.php" class="btn btn-secondary">Batal</a>
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