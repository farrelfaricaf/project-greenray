<?php
include 'auth_check.php';
include '../koneksi.php';

$alert_message = "";
$message_id = null;
$message_data = [];
$replies_history = [];

$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// LOGIKA UNTUK MENGIRIM BALASAN
if (isset($_POST['send_reply'])) {
    $message_id = $_POST['message_id'];
    $reply_to_email = $_POST['reply_to_email'];
    $original_subject = $_POST['original_subject'];
    $reply_message = $_POST['reply_message'];
    $original_message = $_POST['original_message'];

    if (empty($reply_message)) {
        $alert_message = '<div class="alert alert-danger">Error: Isi balasan tidak boleh kosong.</div>';
    } else {
        $new_subject = "Re: " . $original_subject;
        $html_body = "Halo,<br><br>" . nl2br(htmlspecialchars($reply_message)) . "<br><br><hr>";
        $html_body .= "<i>Pada " . date('d M Y') . ", Anda menulis:</i><br>";
        $html_body .= "<blockquote>" . nl2br(htmlspecialchars($original_message)) . "</blockquote>";
        $alt_body = "Halo,\n\n" . $reply_message . "\n\n--\nOn " . date('d M Y') . ", you wrote:\n" . $original_message;

        if (sendEmail($reply_to_email, $new_subject, $html_body, $alt_body)) {

            // === PERUBAHAN DI SINI ===
            // Kita tambahkan 'contact' sebagai reply_type
            $stmt_save_reply = $koneksi->prepare("INSERT INTO admin_replies (reference_id, reply_type, admin_name, reply_body) VALUES (?, 'contact', ?, ?)");
            $stmt_save_reply->bind_param("iss", $message_id, $admin_name, $reply_message);
            // =========================

            $stmt_save_reply->execute();
            $stmt_save_reply->close();
            $alert_message = '<div class="alert alert-success">Balasan berhasil terkirim ke ' . htmlspecialchars($reply_to_email) . '.</div>';
        } else {
            $alert_message = '<div class="alert alert-danger">Error: Gagal mengirim email balasan.</div>';
        }
    }
}

// LOGIKA UNTUK MENAMPILKAN PESAN
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    $stmt_select = $koneksi->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt_select->bind_param("i", $message_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows > 0) {
        $message_data = $result->fetch_assoc();
        if ($message_data['is_read'] == 0) {
            $koneksi->query("UPDATE contact_messages SET is_read = 1 WHERE id = $message_id");
        }

        // === PERUBAHAN DI SINI ===
        // Kita sesuaikan query SELECT
        $stmt_replies = $koneksi->prepare("SELECT * FROM admin_replies WHERE reference_id = ? AND reply_type = 'contact' ORDER BY sent_at DESC");
        // =========================

        $stmt_replies->bind_param("i", $message_id);
        $stmt_replies->execute();
        $result_replies = $stmt_replies->get_result();
        while ($row = $result_replies->fetch_assoc()) {
            $replies_history[] = $row;
        }
        $stmt_replies->close();

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
                        <a class="nav-link" href="index.php">... Dashboard</a>
                        <a class="nav-link" href="projects.php">... Proyek</a>
                        <a class="nav-link" href="consultations.php">... Konsultasi</a>
                        <a class="nav-link active" href="contact_messages.php">... Pesan Kontak</a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo htmlspecialchars($admin_name); ?>
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
                            <div class="card-header"><i class="fas fa-envelope-open-text me-1"></i> Detail Pesan (ID:
                                <?php echo $message_id; ?>)</div>
                            <div class="card-body">
                                <p><strong>Tanggal:</strong>
                                    <?php echo date('d M Y, H:i', strtotime($message_data['created_at'])); ?></p>
                                <p><strong>Dari:</strong> <?php echo htmlspecialchars($message_data['full_name']); ?></p>
                                <p><strong>Email:</strong> <a
                                        href="mailto:<?php echo htmlspecialchars($message_data['email']); ?>"><?php echo htmlspecialchars($message_data['email']); ?></a>
                                </p>
                                <p><strong>Subjek:</strong> <?php echo htmlspecialchars($message_data['subject']); ?></p>
                                <hr>
                                <p><strong>Isi Pesan:</strong></p>
                                <div class="p-3 bg-light rounded border">
                                    <?php echo nl2br(htmlspecialchars($message_data['message'])); ?>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="contact_messages.php" class="btn btn-secondary"><i
                                        class="fas fa-arrow-left me-1"></i> Kembali</a>
                                <a href="message_delete.php?id=<?php echo $message_id; ?>" class="btn btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus pesan ini?');"><i
                                        class="fas fa-trash me-1"></i> Hapus</a>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-success text-white"><i class="fas fa-reply me-1"></i> Balas Pesan Ini
                            </div>
                            <div class="card-body">
                                <form action="message_view.php?id=<?php echo $message_id; ?>" method="POST">
                                    <input type="hidden" name="message_id" value="<?php echo $message_id; ?>">
                                    <input type="hidden" name="reply_to_email"
                                        value="<?php echo htmlspecialchars($message_data['email']); ?>">
                                    <input type="hidden" name="original_subject"
                                        value="<?php echo htmlspecialchars($message_data['subject']); ?>">
                                    <input type="hidden" name="original_message"
                                        value="<?php echo htmlspecialchars($message_data['message']); ?>">
                                    <div class="mb-3">
                                        <label for="reply_message" class="form-label">Isi Balasan Anda:</label>
                                        <textarea class="form-control" id="reply_message" name="reply_message" rows="8"
                                            required></textarea>
                                    </div>
                                    <button type="submit" name="send_reply" class="btn btn-success"><i
                                            class="fas fa-paper-plane me-1"></i> Kirim Balasan</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-history me-1"></i> Riwayat Balasan</div>
                            <div class="card-body">
                                <?php if (empty($replies_history)): ?>
                                    <p class="text-muted text-center">Belum ada balasan untuk pesan ini.</p>
                                <?php else: ?>
                                    <?php foreach ($replies_history as $reply): ?>
                                        <div class="p-3 bg-light rounded border mb-3">
                                            <p class="mb-1">
                                                <strong>Oleh:</strong> <?php echo htmlspecialchars($reply['admin_name']); ?><br>
                                                <strong>Tanggal:</strong>
                                                <?php echo date('d M Y, H:i', strtotime($reply['sent_at'])); ?>
                                            </p>
                                            <hr class="my-2">
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['reply_body'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>Data tidak ditemukan...</p>
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