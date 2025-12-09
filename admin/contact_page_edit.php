<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";


$stmt = $koneksi->prepare("SELECT * FROM contact_settings WHERE id = 1");
$stmt->execute();
$contact = $stmt->get_result()->fetch_assoc();

if (!$contact) {
    
    $koneksi->query("
        INSERT INTO contact_settings (id, header_title, header_subtitle, address, phone, whatsapp, email, office_hours, map_embed)
        VALUES (
            1,
            'Contact Us',
            'Feel free to reach out to our team for any questions about solar solutions.',
            '',
            '',
            '',
            '',
            '',
            ''
        )
    ");
    $stmt = $koneksi->prepare("SELECT * FROM contact_settings WHERE id = 1");
    $stmt->execute();
    $contact = $stmt->get_result()->fetch_assoc();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header_title = $_POST['header_title'] ?? '';
    $header_subtitle = $_POST['header_subtitle'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $email = $_POST['email'] ?? '';
    $office_hours = $_POST['office_hours'] ?? '';
    $map_embed = $_POST['map_embed'] ?? '';

    $stmtUpd = $koneksi->prepare("
        UPDATE contact_settings
        SET header_title = ?,
            header_subtitle = ?,
            address = ?,
            phone = ?,
            whatsapp = ?,
            email = ?,
            office_hours = ?,
            map_embed = ?
        WHERE id = 1
    ");
    $stmtUpd->bind_param(
        "ssssssss",
        $header_title,
        $header_subtitle,
        $address,
        $phone,
        $whatsapp,
        $email,
        $office_hours,
        $map_embed
    );

    if ($stmtUpd->execute()) {
        $alert_message = '<div class="alert alert-success mt-3">Contact settings updated successfully.</div>';

        
        $stmt = $koneksi->prepare("SELECT * FROM contact_settings WHERE id = 1");
        $stmt->execute();
        $contact = $stmt->get_result()->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger mt-3">Error: ' . $stmtUpd->error . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Page Settings - GreenRay Admin</title>
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
                    <h1 class="mt-4">Contact Page Settings</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Contact Page</li>
                    </ol>

                    <?php echo $alert_message; ?>

                    <form method="POST">
                        <div class="card mb-4">
                            <div class="card-header">
                                Header Content
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Header Title</label>
                                    <input type="text" name="header_title" class="form-control"
                                        value="<?php echo htmlspecialchars($contact['header_title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Header Subtitle</label>
                                    <textarea name="header_subtitle" class="form-control" rows="2"><?php
                                    echo htmlspecialchars($contact['header_subtitle']); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                Contact Information
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Office Address</label>
                                    <textarea name="address" class="form-control" rows="3"><?php
                                    echo htmlspecialchars($contact['address']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="<?php echo htmlspecialchars($contact['phone']); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">WhatsApp Link (wa.me)</label>
                                        <input type="text" name="whatsapp" class="form-control"
                                            value="<?php echo htmlspecialchars($contact['whatsapp']); ?>">
                                        <div class="form-text">
                                            Example: https://wa.me/62812xxxxxxx
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?php echo htmlspecialchars($contact['email']); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Office Hours</label>
                                    <input type="text" name="office_hours" class="form-control"
                                        value="<?php echo htmlspecialchars($contact['office_hours']); ?>"
                                        placeholder="Mon–Fri, 09.00–17.00">
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                Map Embed
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Google Maps Embed Code</label>
                                    <textarea name="map_embed" class="form-control" rows="4"><?php
                                    echo htmlspecialchars($contact['map_embed']); ?></textarea>
                                    <div class="form-text">
                                        Paste the &lt;iframe&gt; embed code from Google Maps (optional).
                                    </div>
                                </div>

                                <?php if (!empty($contact['map_embed'])): ?>
                                    <label class="form-label">Preview</label>
                                    <div class="ratio ratio-16x9 mb-3">
                                        <?php echo $contact['map_embed']; ?>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="../html/contact-us.php" target="_blank" class="btn btn-outline-secondary">
                                    View Public Page
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </main>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>