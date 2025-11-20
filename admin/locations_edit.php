<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$location_id = null;
$location = [];


if (isset($_GET['id'])) {
    $location_id = $_GET['id'];


    $stmt_select = $koneksi->prepare("SELECT * FROM locations WHERE id = ?");
    $stmt_select->bind_param("i", $location_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $location = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Lokasi tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Lokasi tidak valid.</div>';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $location_id = $_POST['location_id'];
    $city_name = $_POST['city_name'];
    $irradiance_factor = $_POST['irradiance_factor'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;


    $stmt_update = $koneksi->prepare("UPDATE locations SET city_name = ?, irradiance_factor = ?, is_active = ? WHERE id = ?");


    $stmt_update->bind_param("sdii", $city_name, $irradiance_factor, $is_active, $location_id);


    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Lokasi berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';


        $stmt_select = $koneksi->prepare("SELECT * FROM locations WHERE id = ?");
        $stmt_select->bind_param("i", $location_id);
        $stmt_select->execute();
        $location = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();

    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt_update->close();
}


if (empty($location)) {
    $location = array_fill_keys(['city_name', 'irradiance_factor', 'is_active'], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Lokasi #<?php echo $location_id; ?> - GreenRay Admin</title>
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

                    <h1 class="mt-4">Edit Lokasi</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Setting Kalkulator</li>
                        <li class="breadcrumb-item"><a href="locations.php">Manajemen Lokasi</a></li>
                        <li class="breadcrumb-item active">Edit Lokasi #<?php echo $location_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($location)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit Lokasi (ID: <?php echo $location_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="locations_edit.php?id=<?php echo $location_id; ?>" method="POST">
                                    <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">

                                    <div class="mb-3">
                                        <label class="small mb-1" for="city_name">Nama Kota</label>
                                        <input class="form-control" id="city_name" name="city_name" type="text"
                                            value="<?php echo htmlspecialchars($location['city_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="irradiance_factor">Faktor Irradiance</label>
                                        <input class="form-control" id="irradiance_factor" name="irradiance_factor"
                                            type="text"
                                            value="<?php echo htmlspecialchars($location['irradiance_factor']); ?>"
                                            required>
                                    </div>
                                    <div class="form-check mb-3">
                                        <?php $is_checked = $location['is_active'] ? 'checked' : ''; ?>
                                        <input class="form-check-input" id="is_active" name="is_active" type="checkbox"
                                            value="1" <?php echo $is_checked; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif? (Tampilkan di dropdown kalkulator)
                                        </label>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Update Lokasi</button>
                                    <a href="locations.php" class="btn btn-secondary">Kembali ke Daftar</a>
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