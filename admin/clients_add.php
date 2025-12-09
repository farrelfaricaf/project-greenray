<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $logo_path_db = ""; 

    
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {

        $target_dir = "../uploads/clients/"; 
        $file_name = uniqid() . '-' . basename($_FILES["logo_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        $check = getimagesize($_FILES["logo_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["logo_file"]["tmp_name"], $target_file)) {
                $logo_path_db = "uploads/clients/" . $file_name; 
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file yang di-upload.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File yang di-upload bukan gambar.</div>';
        }
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Logo klien wajib di-upload.</div>';
    }
    

    
    $name = $_POST['name'];

    
    if (empty($alert_message)) {

        
        $stmt = $koneksi->prepare("INSERT INTO clients (name, logo_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $logo_path_db);

        
        if ($stmt->execute()) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Klien baru berhasil ditambahkan.
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
    <title>Tambah Klien - GreenRay Admin</title>
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

                    <h1 class="mt-4">Tambah Klien Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="clients.php">Data Klien</a></li>
                        <li class="breadcrumb-item active">Tambah Klien</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir Klien Baru
                        </div>
                        <div class="card-body">
                            <form action="clients_add.php" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <label class="small mb-1" for="name">Nama Klien</label>
                                    <input class="form-control" id="name" name="name" type="text"
                                        placeholder="Cth: PT Sejahtera" required>
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1" for="logo_file">Upload Logo Klien</label>
                                    <input class="form-control" id="logo_file" name="logo_file" type="file" required>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Klien</button>
                                <a href="clients.php" class="btn btn-secondary">Batal</a>
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
</body>

</html>