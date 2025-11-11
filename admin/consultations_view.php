<?php

include '../koneksi.php';

$alert_message = "";
$consultation_id = null;
$consultation = []; 


if (isset($_GET['id'])) {
    $consultation_id = $_GET['id'];

    
    $stmt = $koneksi->prepare("SELECT * FROM consultation_requests WHERE id = ?");
    $stmt->bind_param("i", $consultation_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $consultation = $result->fetch_assoc();

        
        
        
        

    } else {
        $alert_message = '<div class="alert alert-danger">Error: Data Konsultasi tidak ditemukan!</div>';
    }
    $stmt->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Konsultasi tidak valid.</div>';
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Lihat Konsultasi #<?php echo $consultation_id; ?> - GreenRay Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
    <style>
        /* Style kustom untuk halaman view */
        .dl-horizontal dt {
            float: left;
            width: 200px;
            /* Lebar label */
            clear: left;
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 700;
        }

        .dl-horizontal dd {
            margin-left: 220px;
            /* Jarak setelah label */
            margin-bottom: 0.5rem;
        }
    </style>
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

                        <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                        <a class="nav-link" href="projects.php">... Proyek</a>
                        <a class="nav-link" href="products.php">... Produk</a>
                        <div class="sb-sidenav-menu-heading">Interaksi User</div>
                        <a class="nav-link active" href="consultations.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                            Konsultasi
                        </a>
                        <a class="nav-link" href="contact_messages.php">... Pesan Kontak</a>

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

                    <h1 class="mt-4">Detail Konsultasi (Lead)</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="consultations.php">Data Konsultasi</a></li>
                        <li class="breadcrumb-item active">Lihat Detail #<?php echo $consultation_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($consultation)): ?>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-user me-1"></i>
                                        Data Klien
                                    </div>
                                    <div class="card-body">
                                        <dl class="dl-horizontal">
                                            <dt>ID User (jika login)</dt>
                                            <dd><?php echo $consultation['user_id'] ? $consultation['user_id'] : '<i>(Tamu)</i>'; ?>
                                            </dd>

                                            <dt>Nama Lengkap</dt>
                                            <dd><?php echo htmlspecialchars($consultation['full_name']); ?></dd>

                                            <dt>Email</dt>
                                            <dd><?php echo htmlspecialchars($consultation['email']); ?></dd>

                                            <dt>Telepon</dt>
                                            <dd><?php echo htmlspecialchars($consultation['phone']); ?></dd>

                                            <dt>Alamat</dt>
                                            <dd><?php echo nl2br(htmlspecialchars($consultation['address'])); ?></dd>

                                            <dt>Kelurahan</dt>
                                            <dd><?php echo htmlspecialchars($consultation['kelurahan']); ?></dd>

                                            <dt>Kecamatan</dt>
                                            <dd><?php echo htmlspecialchars($consultation['kecamatan']); ?></dd>

                                            <dt>Kode Pos</dt>
                                            <dd><?php echo htmlspecialchars($consultation['postal_code']); ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-keyboard me-1"></i>
                                        Input Kalkulator
                                    </div>
                                    <div class="card-body">
                                        <dl class="dl-horizontal">
                                            <dt>Tagihan Bulanan</dt>
                                            <dd>Rp
                                                <?php echo number_format($consultation['calc_monthly_bill'], 0, ',', '.'); ?>
                                            </dd>

                                            <dt>Daya VA</dt>
                                            <dd><?php echo htmlspecialchars($consultation['calc_va_capacity']); ?></dd>

                                            <dt>Lokasi</dt>
                                            <dd><?php echo htmlspecialchars($consultation['calc_location']); ?></dd>

                                            <dt>Tipe Properti</dt>
                                            <dd><?php echo htmlspecialchars($consultation['calc_property_type']); ?></dd>

                                            <dt>Timeline Instalasi</dt>
                                            <dd><?php echo htmlspecialchars($consultation['calc_installation_timeline']); ?>
                                            </dd>

                                            <dt>Hambatan Atap</dt>
                                            <dd><?php echo htmlspecialchars($consultation['calc_roof_constraints']); ?></dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Hasil Kalkulasi
                                    </div>
                                    <div class="card-body">
                                        <dl class="dl-horizontal">
                                            <dt>Estimasi Hemat</dt>
                                            <dd><strong>Rp
                                                    <?php echo number_format($consultation['result_monthly_savings'], 0, ',', '.'); ?>
                                                    / bulan</strong></dd>

                                            <dt>Sistem (kWp)</dt>
                                            <dd><strong><?php echo htmlspecialchars($consultation['result_system_capacity_kwp']); ?>
                                                    kWp</strong></dd>

                                            <dt>Estimasi Investasi</dt>
                                            <dd><strong>Rp
                                                    <?php echo number_format($consultation['result_investment_estimate'], 0, ',', '.'); ?></strong>
                                            </dd>

                                            <dt>ROI (Tahun)</dt>
                                            <dd><strong><?php echo htmlspecialchars($consultation['result_roi_years']); ?>
                                                    tahun</strong></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="consultations.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                        <a href="consultations_edit.php?id=<?php echo $consultation_id; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Data Ini
                        </a>

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