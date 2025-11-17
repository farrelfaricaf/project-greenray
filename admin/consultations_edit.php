<?php

include '../koneksi.php';
include 'auth_check.php';

$alert_message = ""; 
$consultation_id = null;
$consultation = []; 


if (isset($_GET['id'])) {
    $consultation_id = $_GET['id'];

    
    $stmt_select = $koneksi->prepare("SELECT * FROM consultation_requests WHERE id = ?");
    $stmt_select->bind_param("i", $consultation_id); 
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $consultation = $result->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Error: Data Konsultasi tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    
    $alert_message = '<div class="alert alert-danger">Error: ID Konsultasi tidak valid.</div>';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $consultation_id = $_POST['consultation_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $kelurahan = $_POST['kelurahan'];
    $kecamatan = $_POST['kecamatan'];
    $postal_code = $_POST['postal_code'];
    $calc_monthly_bill = $_POST['calc_monthly_bill'];
    $calc_va_capacity = $_POST['calc_va_capacity'];
    $calc_location = $_POST['calc_location'];
    $calc_property_type = $_POST['calc_property_type'];
    $calc_installation_timeline = $_POST['calc_installation_timeline'];
    $calc_roof_constraints = $_POST['calc_roof_constraints'];
    $result_monthly_savings = $_POST['result_monthly_savings'];
    $result_system_capacity_kwp = $_POST['result_system_capacity_kwp'];
    $result_investment_estimate = $_POST['result_investment_estimate'];
    $result_roi_years = $_POST['result_roi_years'];

    
    $stmt_update = $koneksi->prepare("UPDATE consultation_requests SET 
        full_name = ?, email = ?, phone = ?, address = ?, kelurahan = ?, kecamatan = ?, postal_code = ?, 
        calc_monthly_bill = ?, calc_va_capacity = ?, calc_location = ?, calc_property_type = ?, 
        calc_installation_timeline = ?, calc_roof_constraints = ?, 
        result_monthly_savings = ?, result_system_capacity_kwp = ?, result_investment_estimate = ?, result_roi_years = ? 
        WHERE id = ?");

    
    $stmt_update->bind_param(
        "sssssssisssssiiddi",
        $full_name,
        $email,
        $phone,
        $address,
        $kelurahan,
        $kecamatan,
        $postal_code,
        $calc_monthly_bill,
        $calc_va_capacity,
        $calc_location,
        $calc_property_type,
        $calc_installation_timeline,
        $calc_roof_constraints,
        $result_monthly_savings,
        $result_system_capacity_kwp,
        $result_investment_estimate,
        $result_roi_years,
        $consultation_id 
    );

    
    if ($stmt_update->execute()) {
        $alert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> Data konsultasi berhasil diperbarui.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';

        
        $stmt_select = $koneksi->prepare("SELECT * FROM consultation_requests WHERE id = ?");
        $stmt_select->bind_param("i", $consultation_id);
        $stmt_select->execute();
        $consultation = $stmt_select->get_result()->fetch_assoc();
        $stmt_select->close();

    } else {
        $alert_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Gagal memperbarui: ' . $stmt_update->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }

    $stmt_update->close();
}


if (empty($consultation)) {
    $consultation = array_fill_keys([
        'full_name',
        'email',
        'phone',
        'address',
        'kelurahan',
        'kecamatan',
        'postal_code',
        'calc_monthly_bill',
        'calc_va_capacity',
        'calc_location',
        'calc_property_type',
        'calc_installation_timeline',
        'calc_roof_constraints',
        'result_monthly_savings',
        'result_system_capacity_kwp',
        'result_investment_estimate',
        'result_roi_years'
    ], '');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Konsultasi #<?php echo $consultation_id; ?> - GreenRay Admin</title>
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
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
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
                        <a class="nav-link" href="clients.php">
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
                        <a class="nav-link active" href="consultations.php">
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

                    <h1 class="mt-4">Edit Data Konsultasi</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="consultations.php">Data Konsultasi</a></li>
                        <li class="breadcrumb-item active">Edit Data #<?php echo $consultation_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($consultation)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-edit me-1"></i>
                                Formulir Edit Konsultasi (ID: <?php echo $consultation_id; ?>)
                            </div>
                            <div class="card-body">

                                <form action="consultations_edit.php?id=<?php echo $consultation_id; ?>" method="POST">
                                    <input type="hidden" name="consultation_id" value="<?php echo $consultation_id; ?>">

                                    <h5 class="mt-3 text-dark">Data Diri Klien</h5>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="full_name">Nama Lengkap</label>
                                            <input class="form-control" id="full_name" name="full_name" type="text"
                                                value="<?php echo htmlspecialchars($consultation['full_name']); ?>"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="email">Email</label>
                                            <input class="form-control" id="email" name="email" type="email"
                                                value="<?php echo htmlspecialchars($consultation['email']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="phone">Nomor Telepon</label>
                                        <input class="form-control" id="phone" name="phone" type="text"
                                            value="<?php echo htmlspecialchars($consultation['phone']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small mb-1" for="address">Alamat Lengkap</label>
                                        <textarea class="form-control" id="address" name="address"
                                            rows="2"><?php echo htmlspecialchars($consultation['address']); ?></textarea>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-4">
                                            <label class="small mb-1" for="kelurahan">Kelurahan</label>
                                            <input class="form-control" id="kelurahan" name="kelurahan" type="text"
                                                value="<?php echo htmlspecialchars($consultation['kelurahan']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small mb-1" for="kecamatan">Kecamatan</label>
                                            <input class="form-control" id="kecamatan" name="kecamatan" type="text"
                                                value="<?php echo htmlspecialchars($consultation['kecamatan']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small mb-1" for="postal_code">Kode Pos</label>
                                            <input class="form-control" id="postal_code" name="postal_code" type="text"
                                                value="<?php echo htmlspecialchars($consultation['postal_code']); ?>">
                                        </div>
                                    </div>

                                    <hr class="mt-4 mb-3" />
                                    <h5 class="mt-3 text-dark">Data Input Kalkulator</h5>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_monthly_bill">Tagihan Bulanan (Rp)</label>
                                            <input class="form-control" id="calc_monthly_bill" name="calc_monthly_bill"
                                                type="number"
                                                value="<?php echo htmlspecialchars($consultation['calc_monthly_bill']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_va_capacity">Daya VA</label>
                                            <input class="form-control" id="calc_va_capacity" name="calc_va_capacity"
                                                type="text"
                                                value="<?php echo htmlspecialchars($consultation['calc_va_capacity']); ?>">
                                        </div>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_location">Lokasi</label>
                                            <input class="form-control" id="calc_location" name="calc_location" type="text"
                                                value="<?php echo htmlspecialchars($consultation['calc_location']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_property_type">Tipe Properti</label>
                                            <input class="form-control" id="calc_property_type" name="calc_property_type"
                                                type="text"
                                                value="<?php echo htmlspecialchars($consultation['calc_property_type']); ?>">
                                        </div>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_installation_timeline">Timeline
                                                Instalasi</label>
                                            <input class="form-control" id="calc_installation_timeline"
                                                name="calc_installation_timeline" type="text"
                                                value="<?php echo htmlspecialchars($consultation['calc_installation_timeline']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="calc_roof_constraints">Hambatan Atap</label>
                                            <input class="form-control" id="calc_roof_constraints"
                                                name="calc_roof_constraints" type="text"
                                                value="<?php echo htmlspecialchars($consultation['calc_roof_constraints']); ?>">
                                        </div>
                                    </div>

                                    <hr class="mt-4 mb-3" />
                                    <h5 class="mt-3 text-dark">Data Hasil Kalkulasi</h5>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="result_monthly_savings">Estimasi Hemat
                                                (Rp)</label>
                                            <input class="form-control" id="result_monthly_savings"
                                                name="result_monthly_savings" type="number"
                                                value="<?php echo htmlspecialchars($consultation['result_monthly_savings']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="result_system_capacity_kwp">Sistem (kWp)</label>
                                            <input class="form-control" id="result_system_capacity_kwp"
                                                name="result_system_capacity_kwp" type="text"
                                                value="<?php echo htmlspecialchars($consultation['result_system_capacity_kwp']); ?>">
                                        </div>
                                    </div>
                                    <div class="row gx-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="result_investment_estimate">Estimasi Investasi
                                                (Rp)</label>
                                            <input class="form-control" id="result_investment_estimate"
                                                name="result_investment_estimate" type="number"
                                                value="<?php echo htmlspecialchars($consultation['result_investment_estimate']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="result_roi_years">ROI (Tahun)</label>
                                            <input class="form-control" id="result_roi_years" name="result_roi_years"
                                                type="text"
                                                value="<?php echo htmlspecialchars($consultation['result_roi_years']); ?>">
                                        </div>
                                    </div>

                                    <button class="btn btn-primary" type="submit">Update Data</button>
                                    <a href="consultations.php" class="btn btn-secondary">Kembali ke Daftar</a>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Data tidak ditemukan atau ID tidak valid. Silakan kembali ke daftar.</p>
                        <a href="consultations.php" class="btn btn-secondary">Kembali ke Daftar</a>
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