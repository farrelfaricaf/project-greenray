<?php
include '../koneksi.php';
$alert_message = "";
$tariff_id = null;
$tariff = [];
if (isset($_GET['id'])) {
    $tariff_id = $_GET['id'];
    $stmt_select = $koneksi->prepare("SELECT * FROM power_tariffs WHERE id = ?");
    $stmt_select->bind_param("i", $tariff_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($result->num_rows > 0) {
        $tariff = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Tarif tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Tarif tidak valid.</div>';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tariff_id = $_POST['tariff_id'];
    $va_capacity = $_POST['va_capacity'];
    $tariff_per_kwh = $_POST['tariff_per_kwh'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $stmt_update = $koneksi->prepare("UPDATE power_tariffs SET va_capacity = ?, tariff_per_kwh = ?, is_active = ? WHERE id = ?");
    $stmt_update->bind_param("sdii", $va_capacity, $tariff_per_kwh, $is_active, $tariff_id);
    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Tarif berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
        $stmt_select = $koneksi->prepare("SELECT * FROM power_tariffs WHERE id = ?");
        $stmt_select->bind_param("i", $tariff_id);
        $stmt_select->execute();
        $tariff = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();
    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }
    $stmt_update->close();
}
if (empty($tariff)) {
    $tariff = array_fill_keys(['va_capacity', 'tariff_per_kwh', 'is_active'], '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Tarif #<?php echo $tariff_id; ?> - GreenRay Admin</title>
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
                        <a class="nav-link" href="index.php">... Dashboard</a>
                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">... Users</a>
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
                    <h1 class="mt-4">Edit Tarif</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting Kalkulator</li>
                        <li class="breadcrumb-item"><a href="tariffs.php">Manajemen Tarif</a></li>
                        <li class="breadcrumb-item active">Edit Tarif #<?php echo $tariff_id; ?></li>
                    </ol>
                    <?php echo $alert_message; ?>
                    <?php if (!empty($tariff)):  ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit Tarif (ID: <?php echo $tariff_id; ?>)
                            </div>
                            <div class="card-body">
                                <form action="tariffs_edit.php?id=<?php echo $tariff_id; ?>" method="POST">
                                    <input type="hidden" name="tariff_id" value="<?php echo $tariff_id; ?>">

                                    <div class="mb-3">
                                        <label class="small mb-1" for="va_capacity">Kapasitas VA</label>
                                        <input class="form-control" id="va_capacity" name="va_capacity" type="text"
                                            value="<?php echo htmlspecialchars($tariff['va_capacity']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="tariff_per_kwh">Tarif per kWh (Rp)</label>
                                        <input class="form-control" id="tariff_per_kwh" name="tariff_per_kwh" type="text"
                                            value="<?php echo htmlspecialchars($tariff['tariff_per_kwh']); ?>" required>
                                    </div>
                                    <div class="form-check mb-3">
                                        <?php $is_checked = $tariff['is_active'] ? 'checked' : ''; ?>
                                        <input class="form-check-input" id="is_active" name="is_active" type="checkbox"
                                            value="1" <?php echo $is_checked; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif? (Tampilkan di dropdown kalkulator)
                                        </label>
                                    </div>
                                    <button class="btn btn-primary" type="submit">Update Tarif</button>
                                    <a href="tariffs.php" class="btn btn-secondary">Kembali ke Daftar</a>
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