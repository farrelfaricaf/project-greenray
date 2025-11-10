<?php
// 1. Hubungkan ke database
include '../koneksi.php';

$alert_message = ""; // Variabel untuk menyimpan pesan notifikasi
$client_id = null;
$client = []; // Array untuk menyimpan data klien yang akan diedit

// 2. Ambil ID Klien dari URL (GET Request)
if (isset($_GET['id'])) {
    $client_id = $_GET['id'];

    // 3. Ambil data klien yang ada dari database
    $stmt_select = $koneksi->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt_select->bind_param("i", $client_id); // 'i' untuk integer
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

    // Ambil semua data dari form (termasuk ID dari hidden input)
    $client_id = $_POST['client_id'];
    $name = $_POST['name'];
    $logo_url = $_POST['logo_url'];

    // 5. Buat query UPDATE
    $stmt_update = $koneksi->prepare("UPDATE clients SET name = ?, logo_url = ? WHERE id = ?");

    // 's' untuk string, 'i' untuk integer (ID di akhir)
    $stmt_update->bind_param("ssi", $name, $logo_url, $client_id);

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
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>

        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                        <a class="nav-link" href="projects.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Proyek
                        </a>
                        <a class="nav-link" href="products.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-solar-panel"></i></div>
                            Produk
                        </a>
                        <a class="nav-link active" href="clients.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                            Klien
                        </a>
                        <a class="nav-link" href="reviews.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                            Reviews
                        </a>
                        <a class="nav-link" href="faqs.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-question-circle"></i></div>
                            FAQ
                        </a>

                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <a class="nav-link" href="consultations.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                            Konsultasi
                        </a>
                        <a class="nav-link" href="contact_messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Pesan Kontak
                        </a>

                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#collapseKalkulator" aria-expanded="false"
                            aria-controls="collapseKalkulator">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Setting Kalkulator
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseKalkulator" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="locations.php">Manajemen Lokasi</a>
                                <a class="nav-link" href="tariffs.php">Manajemen Tarif</a>
                            </nav>
                        </div>

                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Admin
                </div>
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

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i>
                            Formulir Edit Klien (ID: <?php echo $client_id; ?>)
                        </div>
                        <div class="card-body">

                            <form action="client_edit.php?id=<?php echo $client_id; ?>" method="POST">
                                <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">

                                <div class="mb-3">
                                    <label class="small mb-1" for="name">Nama Klien</label>
                                    <input class="form-control" id="name" name="name" type="text"
                                        value="<?php echo htmlspecialchars($client['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="logo_url">URL Logo</label>
                                    <input class="form-control" id="logo_url" name="logo_url" type="text"
                                        value="<?php echo htmlspecialchars($client['logo_url']); ?>" required>
                                </div>

                                <button class="btn btn-primary" type="submit">Update Klien</button>
                                <a href="clients.php" class="btn btn-secondary">Kembali ke Daftar</a>
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