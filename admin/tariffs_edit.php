<?php
include '../koneksi.php';
include 'auth_check.php';

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
                    <h1 class="mt-4">Edit Tarif</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting Kalkulator</li>
                        <li class="breadcrumb-item"><a href="tariffs.php">Manajemen Tarif</a></li>
                        <li class="breadcrumb-item active">Edit Tarif #<?php echo $tariff_id; ?></li>
                    </ol>
                    <?php echo $alert_message; ?>
                    <?php if (!empty($tariff)): ?>
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
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>