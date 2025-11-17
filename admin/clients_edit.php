<?php
// 1. Hubungkan ke database
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$client_id = null;
$client = [];

// 2. Ambil ID Klien dari URL (GET Request)
if (isset($_GET['id'])) {
    $client_id = $_GET['id'];

    // 3. Ambil data lama dari database
    $stmt_select = $koneksi->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt_select->bind_param("i", $client_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Klien tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Klien tidak valid.</div>';
}

// 4. Logika untuk memproses form saat disubmit (POST Request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil path gambar lama dari hidden input
    $current_logo_path = $_POST['current_logo_path'];
    $logo_path_db = $current_logo_path; // Default-nya adalah gambar lama

    // --- AWAL LOGIKA UPLOAD FILE BARU (JIKA ADA) ---
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0 && $_FILES['logo_file']['size'] > 0) {

        $target_dir = "../uploads/clients/";
        $file_name = uniqid() . '-' . basename($_FILES["logo_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["logo_file"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["logo_file"]["tmp_name"], $target_file)) {
                // Jika berhasil, set path BARU untuk database
                $logo_path_db = "uploads/clients/" . $file_name;

                // Hapus file gambar LAMA dari server
                if (!empty($current_logo_path) && file_exists("../" . $current_logo_path)) {
                    unlink("../" . $current_logo_path);
                }
            } else {
                $alert_message = '<div class="alert alert-danger">Error: Gagal memindahkan file baru.</div>';
            }
        } else {
            $alert_message = '<div class="alert alert-danger">Error: File baru bukan gambar.</div>';
        }
    }
    // --- AKHIR LOGIKA UPLOAD FILE ---

    // Ambil data form lainnya
    $client_id = $_POST['client_id'];
    $name = $_POST['name'];

    // Hanya jalankan query UPDATE jika alert masih kosong
    if (empty($alert_message)) {

        // 5. Buat query UPDATE
        $stmt_update = $koneksi->prepare("UPDATE clients SET name = ?, logo_url = ? WHERE id = ?");
        $stmt_update->bind_param("ssi", $name, $logo_path_db, $client_id);

        // 6. Eksekusi query
        if ($stmt_update->execute()) {
            $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Sukses!</strong> Klien berhasil diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';

            // Ambil lagi data terbaru untuk ditampilkan di form
            $stmt_select = $koneksi->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt_select->bind_param("i", $client_id);
            $stmt_select->execute();
            $client = $stmt_select->get_result()->fetch_assoc();
            $stmt_select->close();

        } else {
            $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>';
        }
        $stmt_update->close();
    }
}

// Jika data $client kosong (karena error atau ID tidak ada), isi dengan string kosong
if (empty($client)) {
    $client = array_fill_keys(['name', 'logo_url'], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Klien #<?php echo $client_id; ?> - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
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
                        <a class="nav-link active" href="clients.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Klien
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">...</div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">

                    <h1 class="mt-4">Edit Klien</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="clients.php">Data Klien</a></li>
                        <li class="breadcrumb-item active">Edit Klien #<?php echo $client_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($client)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit Klien (ID: <?php echo $client_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="clients_edit.php?id=<?php echo $client_id; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                                    <input type="hidden" name="current_logo_path"
                                        value="<?php echo htmlspecialchars($client['logo_url']); ?>">

                                    <div class="mb-3">
                                        <label class="small mb-1" for="name">Nama Klien</label>
                                        <input class="form-control" id="name" name="name" type="text"
                                            value="<?php echo htmlspecialchars($client['name']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1">Logo Saat Ini:</label><br>
                                        <?php if (!empty($client['logo_url'])): ?>
                                            <img src="../<?php echo htmlspecialchars($client['logo_url']); ?>"
                                                alt="<?php echo htmlspecialchars($client['name']); ?>"
                                                style="height: 50px; border: 1px solid #ddd; background: #fff; padding: 5px;">
                                        <?php else: ?>
                                            <small class="text-muted">Belum ada logo.</small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label class="small mb-1" for="logo_file">Upload Logo Baru (Opsional)</label>
                                        <input class="form-control" id="logo_file" name="logo_file" type="file">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti logo.</small>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Update Klien</button>
                                    <a href="clients.php" class="btn btn-secondary">Kembali ke Daftar</a>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
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