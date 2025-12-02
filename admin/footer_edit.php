<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

$link_options = [
    'index.php' => 'Halaman Utama (Home)',
    'html/calc.php' => 'Halaman Calculator',
    'html/katalog.php' => 'Halaman Catalog',
    'html/portofolio.php' => 'Halaman Portfolio',
    'html/contact-us.php' => 'Halaman Contact Us',
    'html/signin.php' => 'Halaman Login',
    'html/signup.php' => 'Halaman Register',
    '#' => 'Tidak Ada Link'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Simpan Settings
    $description = $_POST['description'];
    $copyright_text = $_POST['copyright_text'];
    $whatsapp_url = $_POST['whatsapp_url'];
    $email_url = $_POST['email_url'];

    $stmt_upd = $koneksi->prepare("UPDATE footer_settings SET description=?, copyright_text=?, whatsapp_url=?, email_url=? WHERE id=1");
    $stmt_upd->bind_param("ssss", $description, $copyright_text, $whatsapp_url, $email_url);
    $stmt_upd->execute();

    // Simpan Links (Maksimal 8)
    $koneksi->query("DELETE FROM footer_links WHERE section='quick_links'");

    if (isset($_POST['link_label'])) {
        $stmt_ins = $koneksi->prepare("INSERT INTO footer_links (label, url, section) VALUES (?, ?, 'quick_links')");

        // Batasi loop maksimal 8 kali
        $count = count($_POST['link_label']);
        $limit = min($count, 8); // Ambil angka terkecil antara jumlah input atau 8

        for ($i = 0; $i < $limit; $i++) {
            $label = $_POST['link_label'][$i];
            $url = $_POST['link_url'][$i];

            if (!empty($label)) {
                $stmt_ins->bind_param("ss", $label, $url);
                $stmt_ins->execute();
            }
        }
    }

    $alert_message = '<div class="alert alert-success">Footer berhasil diperbarui!</div>';
}

// AMBIL DATA
$data = $koneksi->query("SELECT * FROM footer_settings WHERE id = 1")->fetch_assoc();
$result_links = $koneksi->query("SELECT * FROM footer_links WHERE section='quick_links'");
$links = [];
while ($row = $result_links->fetch_assoc()) {
    $links[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Footer - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
    <style>
        .dynamic-row {
            background: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .remove-btn {
            margin-left: auto;
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
                    <h1 class="mt-4">Edit Footer</h1>
                    <?php echo $alert_message; ?>

                    <form action="" method="POST" class="mb-5">

                        <div class="card mb-4">
                            <div class="card-header fw-bold">Info Dasar</div>
                            <div class="card-body">
                                <div class="mb-3"><label>Deskripsi</label><textarea name="description"
                                        class="form-control"
                                        rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea></div>
                                <div class="mb-3"><label>Copyright</label><input type="text" name="copyright_text"
                                        class="form-control"
                                        value="<?php echo htmlspecialchars($data['copyright_text']); ?>"></div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                                <span>Quick Links (Max 8 Item)</span>
                                <button type="button" class="btn btn-sm btn-success" onclick="addLinkRow()"
                                    id="add-btn">
                                    <i class="fas fa-plus"></i> Tambah Link
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="links-container">
                                    <?php foreach ($links as $link): ?>
                                        <div class="dynamic-row">
                                            <div style="flex: 1;"><label class="small text-muted">Label</label><input
                                                    type="text" name="link_label[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($link['label']); ?>"></div>
                                            <div style="flex: 1;">
                                                <label class="small text-muted">URL</label>
                                                <select name="link_url[]" class="form-select">
                                                    <?php foreach ($link_options as $u => $l): ?>
                                                        <option value="<?php echo $u; ?>" <?php echo ($link['url'] == $u) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mt-4"><button type="button" class="btn btn-danger btn-sm"
                                                    onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="alert alert-warning small mt-2 mb-0" id="limit-alert"
                                    style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i> Batas maksimal 8 link tercapai. Jika
                                    lebih dari 4, link akan otomatis dibagi menjadi 2 kolom.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header fw-bold">Kontak</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6"><label>WhatsApp URL</label><input type="text"
                                            name="whatsapp_url" class="form-control"
                                            value="<?php echo htmlspecialchars($data['whatsapp_url']); ?>"></div>
                                    <div class="col-md-6"><label>Email URL</label><input type="text" name="email_url"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($data['email_url']); ?>"></div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Simpan Footer</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function checkLimit() {
            const count = document.querySelectorAll('#links-container .dynamic-row').length;
            const btn = document.getElementById('add-btn');
            const alertBox = document.getElementById('limit-alert');

            if (count >= 8) {
                btn.disabled = true;
                alertBox.style.display = 'block';
            } else {
                btn.disabled = false;
                alertBox.style.display = 'none';
            }
        }

        function removeRow(btn) {
            btn.parentElement.parentElement.remove();
            checkLimit();
        }

        function addLinkRow() {
            // Cek limit sebelum menambah
            if (document.querySelectorAll('#links-container .dynamic-row').length >= 8) return;

            const container = document.getElementById('links-container');
            const div = document.createElement('div');
            div.className = 'dynamic-row';

            let options = '';
            <?php foreach ($link_options as $u => $l): ?>
                options += `<option value="<?php echo $u; ?>"><?php echo $l; ?></option>`;
            <?php endforeach; ?>

            div.innerHTML = `
                <div style="flex: 1;"><label class="small text-muted">Label</label><input type="text" name="link_label[]" class="form-control" placeholder="Nama Menu"></div>
                <div style="flex: 1;"><label class="small text-muted">URL</label><select name="link_url[]" class="form-select">${options}</select></div>
                <div class="mt-4"><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
            `;
            container.appendChild(div);
            checkLimit();
        }

        // Cek saat load pertama kali
        document.addEventListener('DOMContentLoaded', checkLimit);
    </script>
</body>

</html>