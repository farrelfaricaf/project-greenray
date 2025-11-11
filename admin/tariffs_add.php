<?php
// 1. Hubungkan ke database
include '../koneksi.php';

$alert_message = ""; // Variabel untuk menyimpan pesan notifikasi

// 2. Logika untuk memproses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $va_capacity = $_POST['va_capacity'];
    $tariff_per_kwh = $_POST['tariff_per_kwh'];
    // Cek apakah is_active dikirim (jika checkbox dicentang, nilainya '1', jika tidak, '0')
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // 3. Buat query INSERT
    $stmt = $koneksi->prepare("INSERT INTO power_tariffs (va_capacity, tariff_per_kwh, is_active) VALUES (?, ?, ?)");

    // 'sdi' = string, double, integer
    $stmt->bind_param("sdi", $va_capacity, $tariff_per_kwh, $is_active);

    // 4. Eksekusi query
    if ($stmt->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Tarif baru berhasil ditambahkan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal menyimpan: ' . $stmt->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tambah Tarif - GreenRay Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
</head>

<body class="sb-nav-fixed">

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">GreenRay Admin</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
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
                        <a class="nav-link" href="projects.php">... Proyek</a>
                        <a class="nav-link" href="products.php">... Produk</a>
                        <a class="nav-link" href="clients.php">... Klien</a>
                        <a class="nav-link" href="reviews.php">... Reviews</a>
                        <a class="nav-link" href="faqs.php">... FAQ</a>

                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <a class="nav-link" href="consultations.php">... Konsultasi</a>
                        <a class="nav-link" href="contact_messages.php">... Pesan Kontak</a>

                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#collapseKalkulator"
                            aria-expanded="true" aria-controls="collapseKalkulator">
                            <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                            Setting Kalkulator
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse show" id="collapseKalkulator" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="locations.php">Manajemen Lokasi</a>
                                <a class="nav-link active" href="tariffs.php">Manajemen Tarif</a>
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

                    <h1 class="mt-4">Tambah Tarif Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting Kalkulator</li>
                        <li class="breadcrumb-item"><a href="tariffs.php">Manajemen Tarif</a></li>
                        <li class="breadcrumb-item active">Tambah Tarif</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Formulir Tarif Baru
                        </div>
                        <div class="card-body">
                            <form action="tariff_add.php" method="POST">

                                <div class="mb-3">
                                    <label class="small mb-1" for="va_capacity">Kapasitas VA</label>
                                    <input class="form-control" id="va_capacity" name="va_capacity" type="text"
                                        placeholder="Cth: 1300 VA" required>
                                </div>
                                <div class="mb-3">
                                    <label class="small mb-1" for="tariff_per_kwh">Tarif per kWh (Rp)</label>
                                    <input class="form-control" id="tariff_per_kwh" name="tariff_per_kwh" type="text"
                                        placeholder="Cth: 1444.70 (gunakan titik untuk desimal)" required>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" id="is_active" name="is_active" type="checkbox"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        Aktif? (Tampilkan di dropdown kalkulator)
                                    </label>
                                </div>

                                <button class="btn btn-primary" type="submit">Simpan Tarif</button>
                                <a href="tariffs.php" class="btn btn-secondary">Batal</a>
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