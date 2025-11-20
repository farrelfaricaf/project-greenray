<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $va_capacity = $_POST['va_capacity'];
    $tariff_per_kwh = $_POST['tariff_per_kwh'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $stmt = $koneksi->prepare("INSERT INTO power_tariffs (va_capacity, tariff_per_kwh, is_active) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $va_capacity, $tariff_per_kwh, $is_active);
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
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>