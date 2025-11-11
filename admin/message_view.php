<?php
// 1. Hubungkan ke database
include '../koneksi.php';

$alert_message = "";
$message_id = null;
$message_data = []; // Array untuk menyimpan data pesan

// 2. Ambil ID dari URL (GET Request)
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // 3. Ambil data pesan dari database
    $stmt_select = $koneksi->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt_select->bind_param("i", $message_id); // 'i' untuk integer
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $message_data = $result->fetch_assoc();

        // 4. (FITUR PENTING) Jika pesan belum dibaca (is_read = 0), update jadi 1
        if ($message_data['is_read'] == 0) {
            $stmt_update = $koneksi->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
            $stmt_update->bind_param("i", $message_id);
            $stmt_update->execute();
            $stmt_update->close();
        }

    } else {
        $alert_message = '<div class="alert alert-danger">Error: Pesan tidak ditemukan!</div>';
    }
    $stmt_select->close();
} else {
    $alert_message = '<div class="alert alert-danger">Error: ID Pesan tidak valid.</div>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Lihat Pesan #<?php echo $message_id; ?> - GreenRay Admin</title>
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
                        <a class="nav-link" href="consultations.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                            Konsultasi
                        </a>
                        <a class="nav-link active" href="contact_messages.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                            Pesan Kontak
                        </a>

                        <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                        <a class="nav-link" href="users.php">... Users</a>
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

                    <h1 class="mt-4">Lihat Pesan Masuk</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="contact_messages.php">Pesan Masuk</a></li>
                        <li class="breadcrumb-item active">Lihat Detail #<?php echo $message_id; ?></li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <?php if (!empty($message_data)): ?>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-envelope-open-text me-1"></i>
                                Detail Pesan (ID: <?php echo $message_id; ?>)
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>Tanggal:</strong>
                                    <?php echo date('d M Y, H:i', strtotime($message_data['created_at'])); ?>
                                </p>
                                <p>
                                    <strong>Dari:</strong>
                                    <?php echo htmlspecialchars($message_data['full_name']); ?>
                                </p>
                                <p>
                                    <strong>Email:</strong>
                                    <a
                                        href="mailto:<?php echo htmlspecialchars($message_data['email']); ?>"><?php echo htmlspecialchars($message_data['email']); ?></a>
                                </p>
                                <p>
                                    <strong>Subjek:</strong>
                                    <?php echo htmlspecialchars($message_data['subject']); ?>
                                </p>

                                <hr>

                                <p><strong>Isi Pesan:</strong></p>
                                <div class="p-3 bg-light rounded border">
                                    <?php echo nl2br(htmlspecialchars($message_data['message'])); ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="contact_messages.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                                <a href="message_delete.php?id=<?php echo $message_id; ?>" class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus pesan ini secara permanen?');">
                                    <i class="fas fa-trash me-1"></i> Hapus Pesan
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <p>Data tidak ditemukan atau ID tidak valid. Silakan kembali ke daftar.</p>
                        <a href="contact_messages.php" class="btn btn-secondary">Kembali ke Daftar</a>
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