<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";

// AMBIL DATA SAAT INI
$stmt = $koneksi->query("SELECT * FROM landing_page WHERE id = 1");
$data = $stmt->fetch_assoc();

// DAFTAR OPSI LINK UNTUK DROPDOWN
$link_options = [
    'html/calc.php' => 'Halaman Calculator',
    'html/katalog.php' => 'Halaman Catalog',
    'html/portofolio.php' => 'Halaman Portfolio',
    'html/contact-us.php' => 'Halaman Contact Us',
    'html/signin.php' => 'Halaman Login',
    'html/signup.php' => 'Halaman Register',
    '#' => 'Tidak Ada Link'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- 1. HERO & STATS ---
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];

    $hero_button_primary = $_POST['hero_button_primary'];
    $hero_button_primary_url = $_POST['hero_button_primary_url']; // URL 1

    $hero_button_secondary = $_POST['hero_button_secondary'];
    $hero_button_secondary_url = $_POST['hero_button_secondary_url']; // URL 2

    $stat_1_value = $_POST['stat_1_value'];
    $stat_1_label = $_POST['stat_1_label'];
    $stat_2_value = $_POST['stat_2_value'];
    $stat_2_label = $_POST['stat_2_label'];
    $stat_3_value = $_POST['stat_3_value'];
    $stat_3_label = $_POST['stat_3_label'];
    $stat_4_value = $_POST['stat_4_value'];
    $stat_4_label = $_POST['stat_4_label'];

    // --- 2. WHY CHOOSE US (3 Cards) ---
    $why_1_title = $_POST['why_1_title'];
    $why_1_desc = $_POST['why_1_desc'];
    $why_1_back_title = $_POST['why_1_back_title'];
    $why_1_back_desc = $_POST['why_1_back_desc'];
    $why_2_title = $_POST['why_2_title'];
    $why_2_desc = $_POST['why_2_desc'];
    $why_2_back_title = $_POST['why_2_back_title'];
    $why_2_back_desc = $_POST['why_2_back_desc'];
    $why_3_title = $_POST['why_3_title'];
    $why_3_desc = $_POST['why_3_desc'];
    $why_3_back_title = $_POST['why_3_back_title'];
    $why_3_back_desc = $_POST['why_3_back_desc'];

    // --- 3. OUR SERVICES (3 Cards) ---
    $serv_1_title = $_POST['serv_1_title'];
    $serv_1_desc = $_POST['serv_1_desc'];
    $serv_1_back_title = $_POST['serv_1_back_title'] ?? '';
    $serv_1_back_desc = $_POST['serv_1_back_desc'];
    $serv_2_title = $_POST['serv_2_title'];
    $serv_2_desc = $_POST['serv_2_desc'];
    $serv_2_back_title = $_POST['serv_2_back_title'] ?? '';
    $serv_2_back_desc = $_POST['serv_2_back_desc'];
    $serv_3_title = $_POST['serv_3_title'];
    $serv_3_desc = $_POST['serv_3_desc'];
    $serv_3_back_title = $_POST['serv_3_back_title'] ?? '';
    $serv_3_back_desc = $_POST['serv_3_back_desc'];

    // --- 4. CTA ---
    $cta_title = $_POST['cta_title'];
    $cta_desc = $_POST['cta_desc'];

    // --- FUNGSI UPLOAD GAMBAR ---
    // --- FUNGSI UPLOAD + KOMPRESI OTOMATIS ---
    function uploadAndCompress($fileInputName, $targetDir, $currentImage)
    {
        // 1. Cek apakah ada file yang diupload & tidak error
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] != 0) {
            return $currentImage; // Kembalikan gambar lama jika tidak ada upload baru
        }

        $source = $_FILES[$fileInputName]['tmp_name'];
        $originalName = $_FILES[$fileInputName]['name'];
        $fileSize = $_FILES[$fileInputName]['size'];

        // Ambil ekstensi file
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $validExt = ['jpg', 'jpeg', 'png', 'webp'];

        // Cek ekstensi valid
        if (!in_array($ext, $validExt)) {
            return $currentImage;
        }

        // Buat folder jika belum ada
        if (!file_exists($targetDir))
            mkdir($targetDir, 0777, true);

        // Nama file baru (unik)
        $fileName = uniqid() . '-' . time() . '.' . $ext;
        $destination = $targetDir . $fileName;

        // 2. LOGIKA KOMPRESI & RESIZE
        // Dapatkan ukuran asli gambar
        list($width, $height) = getimagesize($source);

        // Set ukuran maksimal (misal lebar max 1200px agar tidak terlalu berat)
        $max_width = 1200;

        // Hitung rasio baru
        if ($width > $max_width) {
            $ratio = $max_width / $width;
            $new_width = $max_width;
            $new_height = $height * $ratio;
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        // Buat kanvas kosong untuk gambar baru
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Muat gambar sumber berdasarkan tipe
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $source_image = imagecreatefromjpeg($source);
                break;
            case 'png':
                $source_image = imagecreatefrompng($source);
                // Khusus PNG: Pertahankan transparansi
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                break;
            case 'webp':
                $source_image = imagecreatefromwebp($source);
                break;
        }

        // Resize gambar (copy dari sumber ke kanvas baru)
        imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Simpan ke folder tujuan dengan KOMPRESI
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($new_image, $destination, 75); // Kualitas 75% (Sangat cukup & ringan)
                break;
            case 'png':
                imagepng($new_image, $destination, 6); // Kompresi level 6 (0-9)
                break;
            case 'webp':
                imagewebp($new_image, $destination, 75); // Kualitas 75%
                break;
        }

        // Bersihkan memori
        imagedestroy($new_image);
        imagedestroy($source_image);

        // 3. HAPUS GAMBAR LAMA (Maintenance Storage)
        if (!empty($currentImage) && file_exists("../" . $currentImage)) {
            unlink("../" . $currentImage);
        }

        // Kembalikan path baru untuk disimpan ke database
        // (Sesuaikan string return dengan folder database kamu)
        // Karena $targetDir adalah '../uploads/landing/', kita ambil bagian 'uploads/...' nya saja
        return str_replace('../', '', $destination);
    }

    $hero_image = uploadAndCompress('hero_image', '../uploads/landing/', $data['hero_image']);
    $serv_1_image = uploadAndCompress('serv_1_image', '../uploads/landing/', $data['serv_1_image']);
    $serv_2_image = uploadAndCompress('serv_2_image', '../uploads/landing/', $data['serv_2_image']);
    $serv_3_image = uploadAndCompress('serv_3_image', '../uploads/landing/', $data['serv_3_image']);
    $cta_image = uploadAndCompress('cta_image', '../uploads/landing/', $data['cta_image']);

    // --- UPDATE DATABASE ---
    // Total ada 45 parameter (?) di sini
    $sql = "UPDATE landing_page SET 
        hero_title=?, hero_subtitle=?, 
        hero_button_primary=?, hero_button_primary_url=?, 
        hero_button_secondary=?, hero_button_secondary_url=?, 
        hero_image=?,
        stat_1_value=?, stat_1_label=?, stat_2_value=?, stat_2_label=?, 
        stat_3_value=?, stat_3_label=?, stat_4_value=?, stat_4_label=?,
        
        why_1_title=?, why_1_desc=?, why_1_back_title=?, why_1_back_desc=?,
        why_2_title=?, why_2_desc=?, why_2_back_title=?, why_2_back_desc=?,
        why_3_title=?, why_3_desc=?, why_3_back_title=?, why_3_back_desc=?,

        serv_1_title=?, serv_1_desc=?, serv_1_image=?, serv_1_back_title=?, serv_1_back_desc=?,
        serv_2_title=?, serv_2_desc=?, serv_2_image=?, serv_2_back_title=?, serv_2_back_desc=?,
        serv_3_title=?, serv_3_desc=?, serv_3_image=?, serv_3_back_title=?, serv_3_back_desc=?,
        
        cta_title=?, cta_desc=?, cta_image=?
        WHERE id=1";

    $stmt_upd = $koneksi->prepare($sql);

    // BIND PARAM (Harus ada 45 variabel di sini!)
    $stmt_upd->bind_param(
        str_repeat("s", 45),
        // 1. Hero (7 item)
        $hero_title,
        $hero_subtitle,
        $hero_button_primary,
        $hero_button_primary_url,
        $hero_button_secondary,
        $hero_button_secondary_url,
        $hero_image,

        // 2. Stats (8 item)
        $stat_1_value,
        $stat_1_label,
        $stat_2_value,
        $stat_2_label,
        $stat_3_value,
        $stat_3_label,
        $stat_4_value,
        $stat_4_label,

        // 3. Why Choose Us (12 item)
        $why_1_title,
        $why_1_desc,
        $why_1_back_title,
        $why_1_back_desc,
        $why_2_title,
        $why_2_desc,
        $why_2_back_title,
        $why_2_back_desc,
        $why_3_title,
        $why_3_desc,
        $why_3_back_title,
        $why_3_back_desc,

        // 4. Services (15 item)
        $serv_1_title,
        $serv_1_desc,
        $serv_1_image,
        $serv_1_back_title,
        $serv_1_back_desc,
        $serv_2_title,
        $serv_2_desc,
        $serv_2_image,
        $serv_2_back_title,
        $serv_2_back_desc,
        $serv_3_title,
        $serv_3_desc,
        $serv_3_image,
        $serv_3_back_title,
        $serv_3_back_desc,

        // 5. CTA (3 item)
        $cta_title,
        $cta_desc,
        $cta_image
    );

    if ($stmt_upd->execute()) {
        $alert_message = '<div class="alert alert-success">Landing Page Berhasil Diupdate!</div>';
        $stmt = $koneksi->query("SELECT * FROM landing_page WHERE id = 1");
        $data = $stmt->fetch_assoc();
    } else {
        $alert_message = '<div class="alert alert-danger">Gagal: ' . $stmt_upd->error . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Landing Page</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
</head>

<body class="sb-nav-fixed">
    <?php include 'includes/navbar.php'; ?>
    <div id="layoutSidenav">
        <?php include 'includes/sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Edit Landing Page</h1>
                    <?php echo $alert_message; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="mb-5">

                        <div class="card mb-4">
                            <div class="card-header fw-bold text-white bg-success">1. Hero Section</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold">Judul Utama</label>
                                        <textarea name="hero_title" class="form-control" rows="3"
                                            placeholder="Baris 1 (Hitam)&#10;Baris 2 (Hijau)"><?php echo htmlspecialchars($data['hero_title']); ?></textarea>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i>
                                            Tekan <b>Enter</b> untuk memisahkan baris. Baris kedua otomatis berwarna
                                            <span class="text-success fw-bold">Hijau</span>.
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Sub-Judul</label>
                                        <textarea name="hero_subtitle"
                                            class="form-control"><?php echo htmlspecialchars($data['hero_subtitle']); ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6"><label>Gambar Hero</label><input type="file" name="hero_image"
                                            class="form-control"></div>
                                    <div class="col-md-3">
                                        <label class="fw-bold text-success">Tombol 1 (Hijau)</label>
                                        <input type="text" name="hero_button_primary" class="form-control mb-1"
                                            placeholder="Teks Tombol"
                                            value="<?php echo htmlspecialchars($data['hero_button_primary']); ?>">
                                        <select name="hero_button_primary_url" class="form-select form-select-sm">
                                            <?php foreach ($link_options as $url => $label): ?>
                                                <option value="<?php echo $url; ?>" <?php echo ($data['hero_button_primary_url'] == $url) ? 'selected' : ''; ?>>
                                                    -> <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="fw-bold text-secondary">Tombol 2 (Putih)</label>
                                        <input type="text" name="hero_button_secondary" class="form-control mb-1"
                                            placeholder="Teks Tombol"
                                            value="<?php echo htmlspecialchars($data['hero_button_secondary']); ?>">
                                        <select name="hero_button_secondary_url" class="form-select form-select-sm">
                                            <?php foreach ($link_options as $url => $label): ?>
                                                <option value="<?php echo $url; ?>" <?php echo ($data['hero_button_secondary_url'] == $url) ? 'selected' : ''; ?>>
                                                    -> <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <h6>Statistik</h6>
                                <div class="row">
                                    <div class="col-md-3"><input type="text" name="stat_1_value" class="form-control"
                                            value="<?php echo $data['stat_1_value']; ?>"><input type="text"
                                            name="stat_1_label" class="form-control mt-1"
                                            value="<?php echo $data['stat_1_label']; ?>"></div>
                                    <div class="col-md-3"><input type="text" name="stat_2_value" class="form-control"
                                            value="<?php echo $data['stat_2_value']; ?>"><input type="text"
                                            name="stat_2_label" class="form-control mt-1"
                                            value="<?php echo $data['stat_2_label']; ?>"></div>
                                    <div class="col-md-3"><input type="text" name="stat_3_value" class="form-control"
                                            value="<?php echo $data['stat_3_value']; ?>"><input type="text"
                                            name="stat_3_label" class="form-control mt-1"
                                            value="<?php echo $data['stat_3_label']; ?>"></div>
                                    <div class="col-md-3"><input type="text" name="stat_4_value" class="form-control"
                                            value="<?php echo $data['stat_4_value']; ?>"><input type="text"
                                            name="stat_4_label" class="form-control mt-1"
                                            value="<?php echo $data['stat_4_label']; ?>"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header fw-bold text-white bg-success">2. Why Choose Us (Flip Cards)</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 border-end">
                                        <h6>Kartu 1</h6>
                                        <label>Judul Depan</label><input type="text" name="why_1_title"
                                            class="form-control mb-1" value="<?php echo $data['why_1_title']; ?>">
                                        <label>Desc Depan</label><input type="text" name="why_1_desc"
                                            class="form-control mb-1" value="<?php echo $data['why_1_desc']; ?>">
                                        <label>Judul Belakang</label><input type="text" name="why_1_back_title"
                                            class="form-control mb-1" value="<?php echo $data['why_1_back_title']; ?>">
                                        <label>Desc Belakang</label><textarea name="why_1_back_desc"
                                            class="form-control"
                                            rows="3"><?php echo $data['why_1_back_desc']; ?></textarea>
                                    </div>
                                    <div class="col-md-4 border-end">
                                        <h6>Kartu 2</h6>
                                        <label>Judul Depan</label><input type="text" name="why_2_title"
                                            class="form-control mb-1" value="<?php echo $data['why_2_title']; ?>">
                                        <label>Desc Depan</label><input type="text" name="why_2_desc"
                                            class="form-control mb-1" value="<?php echo $data['why_2_desc']; ?>">
                                        <label>Judul Belakang</label><input type="text" name="why_2_back_title"
                                            class="form-control mb-1" value="<?php echo $data['why_2_back_title']; ?>">
                                        <label>Desc Belakang</label><textarea name="why_2_back_desc"
                                            class="form-control"
                                            rows="3"><?php echo $data['why_2_back_desc']; ?></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Kartu 3</h6>
                                        <label>Judul Depan</label><input type="text" name="why_3_title"
                                            class="form-control mb-1" value="<?php echo $data['why_3_title']; ?>">
                                        <label>Desc Depan</label><input type="text" name="why_3_desc"
                                            class="form-control mb-1" value="<?php echo $data['why_3_desc']; ?>">
                                        <label>Judul Belakang</label><input type="text" name="why_3_back_title"
                                            class="form-control mb-1" value="<?php echo $data['why_3_back_title']; ?>">
                                        <label>Desc Belakang</label><textarea name="why_3_back_desc"
                                            class="form-control"
                                            rows="3"><?php echo $data['why_3_back_desc']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header fw-bold text-white bg-success">3. Our Services (Flip Cards)</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 border-end">
                                        <h6>Service 1</h6>
                                        <label class="fw-bold text-success mt-2">Judul</label>
                                        <input type="text" name="serv_1_title" class="form-control mb-1"
                                            value="<?php echo $data['serv_1_title']; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Depan</label>
                                        <input type="text" name="serv_1_desc" class="form-control mb-1"
                                            value="<?php echo $data['serv_1_desc']; ?>">

                                        <label class="fw-bold text-success mt-2">Judul Belakang</label>
                                        <input type="text" name="serv_1_back_title" class="form-control mb-1"
                                            value="<?php echo isset($data['serv_1_back_title']) ? $data['serv_1_back_title'] : ''; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Belakang</label>
                                        <textarea name="serv_1_back_desc" class="form-control mb-1"
                                            rows="3"><?php echo $data['serv_1_back_desc']; ?></textarea>
                                        <label class="fw-bold text-success mt-2">Tombol</label>
                                        <input type="text" name="serv_1_btn_text" class="form-control mb-1"
                                            placeholder="Teks"
                                            value="<?php echo htmlspecialchars($data['serv_1_btn_text']); ?>">
                                        <select name="serv_1_btn_url"
                                            class="form-select form-select-sm"><?php foreach ($link_options as $u => $l)
                                                echo "<option value='$u' " . ($data['serv_1_btn_url'] == $u ? 'selected' : '') . ">$l</option>"; ?></select>

                                        <label class="fw-bold text-success mt-2">Gambar</label>
                                        <input type="file" name="serv_1_image" class="form-control">
                                        <?php if ($data['serv_1_image'])
                                            echo '<img src="../' . $data['serv_1_image'] . '" width="50" class="mt-1 rounded">'; ?>
                                    </div>

                                    <div class="col-md-4 border-end">
                                        <h6>Service 2</h6>
                                        <label class="fw-bold text-success mt-2">Judul</label>
                                        <input type="text" name="serv_2_title" class="form-control mb-1"
                                            value="<?php echo $data['serv_2_title']; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Depan</label>
                                        <input type="text" name="serv_2_desc" class="form-control mb-1"
                                            value="<?php echo $data['serv_2_desc']; ?>">

                                        <label class="fw-bold text-success mt-2">Judul Belakang</label>
                                        <input type="text" name="serv_2_back_title" class="form-control mb-1"
                                            value="<?php echo isset($data['serv_2_back_title']) ? $data['serv_2_back_title'] : ''; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Belakang</label>
                                        <textarea name="serv_2_back_desc" class="form-control mb-1"
                                            rows="3"><?php echo $data['serv_2_back_desc']; ?></textarea>
                                        <label class="fw-bold text-success mt-2">Tombol</label>
                                        <input type="text" name="serv_2_btn_text" class="form-control mb-1"
                                            placeholder="Teks"
                                            value="<?php echo htmlspecialchars($data['serv_2_btn_text']); ?>">
                                        <select name="serv_2_btn_url"
                                            class="form-select form-select-sm"><?php foreach ($link_options as $u => $l)
                                                echo "<option value='$u' " . ($data['serv_2_btn_url'] == $u ? 'selected' : '') . ">$l</option>"; ?></select>

                                        <label class="fw-bold text-success mt-2">Gambar</label>
                                        <input type="file" name="serv_2_image" class="form-control">
                                        <?php if ($data['serv_2_image'])
                                            echo '<img src="../' . $data['serv_2_image'] . '" width="50" class="mt-1 rounded">'; ?>
                                    </div>

                                    <div class="col-md-4">
                                        <h6>Service 3</h6>
                                        <label class="fw-bold text-success mt-2">Judul</label>
                                        <input type="text" name="serv_3_title" class="form-control mb-1"
                                            value="<?php echo $data['serv_3_title']; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Depan</label>
                                        <input type="text" name="serv_3_desc" class="form-control mb-1"
                                            value="<?php echo $data['serv_3_desc']; ?>">

                                        <label class="fw-bold text-success mt-2">Judul Belakang</label>
                                        <input type="text" name="serv_3_back_title" class="form-control mb-1"
                                            value="<?php echo isset($data['serv_3_back_title']) ? $data['serv_3_back_title'] : ''; ?>">

                                        <label class="fw-bold text-success mt-2">Desc Belakang</label>
                                        <textarea name="serv_3_back_desc" class="form-control mb-1"
                                            rows="3"><?php echo $data['serv_3_back_desc']; ?></textarea>
                                        <label class="fw-bold text-success mt-2">Tombol</label>
                                        <input type="text" name="serv_3_btn_text" class="form-control mb-1"
                                            placeholder="Teks"
                                            value="<?php echo htmlspecialchars($data['serv_3_btn_text']); ?>">
                                        <select name="serv_3_btn_url"
                                            class="form-select form-select-sm"><?php foreach ($link_options as $u => $l)
                                                echo "<option value='$u' " . ($data['serv_3_btn_url'] == $u ? 'selected' : '') . ">$l</option>"; ?></select>

                                        <label class="fw-bold text-success mt-2">Gambar</label>
                                        <input type="file" name="serv_3_image" class="form-control">
                                        <?php if ($data['serv_3_image'])
                                            echo '<img src="../' . $data['serv_3_image'] . '" width="50" class="mt-1 rounded">'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header fw-bold text-white bg-success">4. CTA Section</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Judul CTA</label>
                                    <input type="text" name="cta_title" class="form-control"
                                        value="<?php echo htmlspecialchars($data['cta_title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Deskripsi CTA</label>
                                    <textarea name="cta_desc" class="form-control"
                                        rows="2"><?php echo htmlspecialchars($data['cta_desc']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Background Image</label><br>
                                    <?php if ($data['cta_image'])
                                        echo '<img src="../' . $data['cta_image'] . '" width="150" class="mb-2 rounded">'; ?>
                                    <input type="file" name="cta_image" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark bg-success btn-lg w-100">Simpan Semua
                            Perubahan</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>