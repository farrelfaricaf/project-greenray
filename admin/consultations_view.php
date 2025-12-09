<?php
include 'auth_check.php';
include '../koneksi.php';

$alert_message = "";
$consultation_id = null;
$consultation = [];
$replies_history = [];

$admin_name = $_SESSION['admin_name'] ?? 'Admin';




if (isset($_POST['send_reply'])) {
    $consultation_id = $_POST['consultation_id'];
    $reply_to_email = $_POST['reply_to_email'];
    $user_name = $_POST['user_name'];
    $reply_message = $_POST['reply_message'];
    $original_subject = "Hasil Konsultasi Kalkulator GreenRay";

    if (empty($reply_message)) {
        $alert_message = '<div class="alert alert-danger">Error: Isi balasan tidak boleh kosong.</div>';
    } else {


        if (sendEmail($reply_to_email, $original_subject, nl2br(htmlspecialchars($reply_message)), $reply_message)) {



            $stmt_save_reply = $koneksi->prepare("INSERT INTO admin_replies (reference_id, reply_type, admin_name, reply_body) VALUES (?, 'consultation', ?, ?)");
            $stmt_save_reply->bind_param("iss", $consultation_id, $admin_name, $reply_message);
            $stmt_save_reply->execute();
            $stmt_save_reply->close();

            $alert_message = '<div class="alert alert-success">Balasan berhasil terkirim ke ' . htmlspecialchars($reply_to_email) . '.</div>';

        } else {
            $alert_message = '<div class="alert alert-danger">Error: Gagal mengirim email balasan. Cek konfigurasi SMTP kamu.</div>';
        }
    }
}





if (isset($_GET['id'])) {
    $consultation_id = $_GET['id'];


    $stmt = $koneksi->prepare("SELECT * FROM consultation_requests WHERE id = ?");
    $stmt->bind_param("i", $consultation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $consultation = $result->fetch_assoc();


        $stmt_replies = $koneksi->prepare("SELECT * FROM admin_replies WHERE reference_id = ? AND reply_type = 'consultation' ORDER BY sent_at DESC");
        $stmt_replies->bind_param("i", $consultation_id);
        $stmt_replies->execute();
        $result_replies = $stmt_replies->get_result();
        while ($row = $result_replies->fetch_assoc()) {
            $replies_history[] = $row;
        }
        $stmt_replies->close();

    } else {
        $alert_message = '<div class="alert alert-danger">Error: Data Konsultasi tidak ditemukan!</div>';
    }
    $stmt->close();
} else {

    if ($consultation_id == null && isset($_POST['consultation_id'])) {
        $consultation_id = $_POST['consultation_id'];
    } else {
        $alert_message = '<div class="alert alert-danger">Error: ID Konsultasi tidak valid.</div>';
    }
}


if (!empty($alert_message) && $consultation_id && empty($consultation)) {
    $stmt = $koneksi->prepare("SELECT * FROM consultation_requests WHERE id = ?");
    $stmt->bind_param("i", $consultation_id);
    $stmt->execute();
    $consultation = $stmt->get_result()->fetch_assoc();


    $stmt_replies = $koneksi->prepare("SELECT * FROM admin_replies WHERE reference_id = ? AND reply_type = 'consultation' ORDER BY sent_at DESC");
    $stmt_replies->bind_param("i", $consultation_id);
    $stmt_replies->execute();
    $result_replies = $stmt_replies->get_result();
    while ($row = $result_replies->fetch_assoc()) {
        $replies_history[] = $row;
    }
    $stmt_replies->close();
}


$default_reply_message = "";
if (!empty($consultation)) {
    $user_name = htmlspecialchars($consultation['full_name']);
    $system_kwp = htmlspecialchars($consultation['result_system_capacity_kwp']);
    $savings_rp = number_format($consultation['result_monthly_savings'], 0, ',', '.');

    $default_reply_message = "Halo $user_name,\n\nTerima kasih atas permintaan konsultasi Anda melalui website GreenRay.\n\nBerdasarkan perhitungan, estimasi sistem yang ideal untuk Anda adalah $system_kwp kWp dengan potensi penghematan Rp $savings_rp / bulan.\n\nTim kami ingin menjadwalkan survey lokasi virtual (via video call/foto) atau survey langsung untuk memvalidasi data dan memberikan penawaran final.\n\nApakah Anda ada waktu luang di minggu ini?\n\nSalam,\n$admin_name\nTim GreenRay";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Lihat Konsultasi #<?php echo $consultation_id; ?> - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../img/favicon.png?v=1.1" sizes="180x180">
    <style>
        .dl-horizontal dt {
            float: left;
            width: 200px;
            clear: left;
            text-align: right;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 700;
        }

        .dl-horizontal dd {
            margin-left: 220px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="sb-nav-fixed">

    <?php include 'includes/navbar.php'; ?>

    <div id="layoutSidenav">

        <?php include 'includes/sidebar.php'; ?>

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
                                    <div class="card-header"><i class="fas fa-user me-1"></i> Data Klien</div>
                                    <div class="card-body">
                                        <dl class="dl-horizontal">
                                            <dt>ID User (jika login)</dt>
                                            <dd><?php echo $consultation['user_id'] ? $consultation['user_id'] : '<i>(Tamu)</i>'; ?>
                                            </dd>
                                            <dt>Nama Lengkap</dt>
                                            <dd><?php echo htmlspecialchars($consultation['full_name']); ?></dd>
                                            <dt>Email</dt>
                                            <dd><a
                                                    href="mailto:<?php echo htmlspecialchars($consultation['email']); ?>"><?php echo htmlspecialchars($consultation['email']); ?></a>
                                            </dd>
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
                                    <div class="card-header"><i class="fas fa-keyboard me-1"></i> Input Kalkulator</div>
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
                                    <div class="card-header bg-primary text-white"><i class="fas fa-chart-line me-1"></i>
                                        Hasil Kalkulasi</div>
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

                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-reply me-1"></i>
                                Balas Konsultasi Ini
                            </div>
                            <div class="card-body">
                                <form action="consultations_view.php?id=<?php echo $consultation_id; ?>" method="POST">
                                    <input type="hidden" name="consultation_id" value="<?php echo $consultation_id; ?>">
                                    <input type="hidden" name="reply_to_email"
                                        value="<?php echo htmlspecialchars($consultation['email']); ?>">
                                    <input type="hidden" name="user_name"
                                        value="<?php echo htmlspecialchars($consultation['full_name']); ?>">

                                    <div class="mb-3">
                                        <label for="reply_message" class="form-label">Isi Balasan (Template sudah disiapkan,
                                            silakan diedit jika perlu):</label>
                                        <textarea class="form-control" id="reply_message" name="reply_message" rows="12"
                                            required><?php echo $default_reply_message; ?></textarea>
                                    </div>
                                    <button type="submit" name="send_reply" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> Kirim Balasan ke Email Klien
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-history me-1"></i>
                                Riwayat Balasan
                            </div>
                            <div class="card-body">
                                <?php if (empty($replies_history)): ?>
                                    <p class="text-muted text-center">Belum ada balasan untuk konsultasi ini.</p>
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

                        <a href="consultations.php" class="btn btn-secondary mb-4">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>

                    <?php else: ?>
                        <p>Data tidak ditemukan atau ID tidak valid. Silakan kembali ke daftar.</p>
                        <a href="consultations.php" class="btn btn-secondary">Kembali ke Daftar</a>
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