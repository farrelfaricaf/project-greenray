<?php
include '../koneksi.php';
include 'auth_check.php';

$alert_message = "";
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: products.php");
    exit;
}


$segments = [];
$resSeg = $koneksi->query("SELECT id, name, slug FROM product_segments ORDER BY name ASC");
if ($resSeg) {
    while ($row = $resSeg->fetch_assoc()) {
        $segments[] = $row;
    }
}


$current_segment_ids = [];
$resCurr = $koneksi->query(
    "SELECT segment_id FROM product_segment_map WHERE product_id = " . (int) $product_id
);
if ($resCurr) {
    while ($row = $resCurr->fetch_assoc()) {
        $current_segment_ids[] = (int) $row['segment_id'];
    }
}


$stmt = $koneksi->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $subtitle = $_POST['subtitle'];
    $description = $_POST['description'];

    
    $raw_segments = $_POST['segment_ids'] ?? [];
    $segment_ids = [];

    if (!is_array($raw_segments)) {
        $raw_segments = [$raw_segments];
    }

    foreach ($raw_segments as $raw) {
        $raw = trim($raw);
        if ($raw === '')
            continue;

        if (ctype_digit($raw)) {
            $segment_ids[] = (int) $raw;
        } else {
            $segment_name = $raw;
            $segment_slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $segment_name));

            $stmtSeg = $koneksi->prepare(
                "INSERT INTO product_segments (slug, name) VALUES (?, ?)"
            );
            $stmtSeg->bind_param("ss", $segment_slug, $segment_name);
            if ($stmtSeg->execute()) {
                $segment_ids[] = $stmtSeg->insert_id;
            }
        }
    }

    $primary_segment_id = !empty($segment_ids) ? $segment_ids[0] : null;

    
    $image_path_db = $_POST['old_image']; 
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "../uploads/products/";
        $file_name = uniqid() . '-main-' . basename($_FILES["image_file"]["name"]);
        move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_dir . $file_name);
        $image_path_db = "uploads/products/" . $file_name;
    }

    
    $features_array = [];
    if (isset($_POST['feature_title'])) {
        for ($i = 0; $i < count($_POST['feature_title']); $i++) {
            if (!empty($_POST['feature_title'][$i])) {
                $features_array[] = [
                    'title' => $_POST['feature_title'][$i],
                    'description' => $_POST['feature_desc'][$i]
                ];
            }
        }
    }
    $key_features_json = json_encode($features_array);

    
    $specs_array = [];
    if (isset($_POST['spec_title'])) {
        $target_icon_dir = "../uploads/icons/";
        if (!file_exists($target_icon_dir)) {
            mkdir($target_icon_dir, 0777, true);
        }

        for ($i = 0; $i < count($_POST['spec_title']); $i++) {
            $title = $_POST['spec_title'][$i];
            $sub = $_POST['spec_subtitle'][$i];
            $type = $_POST['spec_icon_type'][$i];

            
            $icon_value = $_POST['spec_old_value'][$i];

            if ($type == 'class') {
                $icon_value = $_POST['spec_icon_class'][$i];
            } else {
                if (
                    isset($_FILES['spec_icon_file']['name'][$i]) &&
                    $_FILES['spec_icon_file']['error'][$i] == 0
                ) {

                    $icon_name = uniqid() . '-icon-' . basename($_FILES['spec_icon_file']['name'][$i]);
                    move_uploaded_file($_FILES['spec_icon_file']['tmp_name'][$i], $target_icon_dir . $icon_name);
                    $icon_value = "uploads/icons/" . $icon_name;
                }
            }

            if (!empty($title)) {
                $specs_array[] = [
                    'title' => $title,
                    'subtitle' => $sub,
                    'icon_type' => $type,
                    'icon_val' => $icon_value
                ];
            }
        }
    }
    $specifications_json = json_encode($specs_array);

    
    $stmt = $koneksi->prepare(
        "UPDATE products
         SET name=?, slug=?, subtitle=?, image_url=?, description=?,
             key_features_json=?, specifications_json=?, segment_id=?
         WHERE id=?"
    );
    $stmt->bind_param(
        "sssssssii",
        $name,
        $slug,
        $subtitle,
        $image_path_db,
        $description,
        $key_features_json,
        $specifications_json,
        $primary_segment_id,
        $product_id
    );

    if ($stmt->execute()) {
        
        $koneksi->query(
            "DELETE FROM product_segment_map WHERE product_id = " . (int) $product_id
        );

        if (!empty($segment_ids)) {
            $stmtMap = $koneksi->prepare(
                "INSERT INTO product_segment_map (product_id, segment_id) VALUES (?, ?)"
            );
            foreach ($segment_ids as $sid) {
                $stmtMap->bind_param("ii", $product_id, $sid);
                $stmtMap->execute();
            }
        }

        $alert_message = '<div class="alert alert-success">Produk berhasil diupdate!</div>';
    } else {
        $alert_message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
    }
}


$features_data = json_decode($product['key_features_json'], true) ?? [];
$specs_data = json_decode($product['specifications_json'], true) ?? [];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Produk - GreenRay Admin</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .dynamic-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            position: relative;
            margin-bottom: 10px;
        }

        .remove-row-btn {
            position: absolute;
            top: 10px;
            right: 10px;
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
                    <h1 class="mt-4">Edit Produk</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Data Proyek</a></li>
                        <li class="breadcrumb-item active">Edit Produk #<?php echo $product_id; ?></li>
                    </ol>
                    <?php echo $alert_message; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="card mb-4">
                            <div class="card-header">Info Dasar</div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>Nama Produk</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Slug</label>
                                        <input type="text" name="slug" id="slug" class="form-control"
                                            value="<?php echo htmlspecialchars($product['slug']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Subtitle</label>
                                    <input type="text" name="subtitle" class="form-control"
                                        value="<?php echo htmlspecialchars($product['subtitle']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Gambar Utama</label> <br>
                                    <img src="../<?php echo $product['image_url']; ?>" width="100" class="mb-2 border">
                                    <input type="hidden" name="old_image" value="<?php echo $product['image_url']; ?>">
                                    <input type="file" name="image_file" class="form-control">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti
                                        gambar.</small>
                                </div>
                                <div class="mb-3">
                                    <label>Deskripsi</label>
                                    <textarea name="description" class="form-control"
                                        rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>

                                <?php $current_segment_ids = $current_segment_ids ?? []; ?>

                                <div class="mb-3">
                                    <label for="segment_ids" class="form-label">Segment / Tag Produk</label>
                                    <select name="segment_ids[]" id="segment_ids" class="form-select segment-select"
                                        multiple="multiple">
                                        <?php foreach ($segments as $seg): ?>
                                            <option value="<?php echo $seg['id']; ?>" <?php echo in_array($seg['id'], $current_segment_ids) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($seg['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Key Features</span>
                                <button type="button" class="btn btn-sm btn-success" onclick="addFeature()">+
                                    Tambah
                                    Fitur</button>
                            </div>
                            <div class="card-body" id="features_container">
                                <?php foreach ($features_data as $feat): ?>
                                    <div class="dynamic-row">
                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn"
                                            onclick="this.parentElement.remove()">X</button>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="small">Judul Fitur</label>
                                                <input type="text" name="feature_title[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($feat['title']); ?>">
                                            </div>
                                            <div class="col-md-8">
                                                <label class="small">Deskripsi Fitur</label>
                                                <input type="text" name="feature_desc[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($feat['description']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Specifications</span>
                                <button type="button" class="btn btn-sm btn-success" onclick="addSpec()">+
                                    Tambah
                                    Spesifikasi</button>
                            </div>
                            <div class="card-body" id="specs_container">
                                <?php foreach ($specs_data as $spec):
                                    
                                    
                                    if (isset($spec['icon_type'])) {
                                        $type = $spec['icon_type'];
                                        $val = $spec['icon_val'] ?? '';
                                    }
                                    
                                    elseif (isset($spec['icon_class'])) {
                                        $type = 'class';
                                        $val = $spec['icon_class'];
                                    }
                                    
                                    else {
                                        $type = 'class';
                                        $val = '';
                                    }

                                    $isClass = ($type == 'class');
                                    $classVal = $isClass ? $val : '';
                                    $imgVal = !$isClass ? $val : '';
                                    ?>
                                    <div class="dynamic-row">
                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn"
                                            onclick="this.parentElement.remove()">X</button>
                                        <input type="hidden" name="spec_old_value[]"
                                            value="<?php echo htmlspecialchars($val); ?>">

                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <label class="small">Judul Spesifikasi</label>
                                                <input type="text" name="spec_title[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($spec['title']); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small">Subtitle</label>
                                                <input type="text" name="spec_subtitle[]" class="form-control"
                                                    value="<?php echo htmlspecialchars($spec['subtitle']); ?>">
                                            </div>
                                        </div>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <label class="small">Tipe Icon</label>
                                                <select name="spec_icon_type[]" class="form-select"
                                                    onchange="toggleIconInput(this)">
                                                    <option value="class" <?php echo $isClass ? 'selected' : ''; ?>>
                                                        Icon
                                                        Class (Web)</option>
                                                    <option value="image" <?php echo !$isClass ? 'selected' : ''; ?>>Upload
                                                        Gambar</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8 icon-input-area">
                                                <div class="input-class <?php echo !$isClass ? 'd-none' : ''; ?>">
                                                    <label class="small">Nama Class Icon</label>
                                                    <input type="text" name="spec_icon_class[]" class="form-control"
                                                        value="<?php echo htmlspecialchars($classVal); ?>">
                                                </div>
                                                <div class="input-file <?php echo $isClass ? 'd-none' : ''; ?>">
                                                    <label class="small">Upload Icon (Ganti jika perlu)</label>
                                                    <?php if ($imgVal): ?>
                                                        <div class="mb-1"><img src="../<?php echo $imgVal; ?>" width="30">
                                                            <small class="text-muted">Icon saat ini</small>
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" name="spec_icon_file[]" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mb-5">Update Produk</button>
                        <a href="products.php" class="btn btn-secondary mb-5">Batal</a>
                    </form>
                </div>
            </main>

            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script>
        
        document.getElementById('name').addEventListener('input', function () {
            let slug = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
            document.getElementById('slug').value = slug;
        });

        
        function addFeature() {
            const div = document.createElement('div');
            div.className = 'dynamic-row';
            div.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" onclick="this.parentElement.remove()">X</button>
                <div class="row">
                    <div class="col-md-4"><label class="small">Judul Fitur</label><input type="text" name="feature_title[]" class="form-control"></div>
                    <div class="col-md-8"><label class="small">Deskripsi Fitur</label><input type="text" name="feature_desc[]" class="form-control"></div>
                </div>
            `;
            document.getElementById('features_container').appendChild(div);
        }

        function addSpec() {
            const div = document.createElement('div');
            div.className = 'dynamic-row';
            div.innerHTML = `
                <button type="button" class="btn btn-danger btn-sm remove-row-btn" onclick="this.parentElement.remove()">X</button>
                <input type="hidden" name="spec_old_value[]" value=""> <div class="row mb-2">
                    <div class="col-md-6"><label class="small">Judul Spesifikasi</label><input type="text" name="spec_title[]" class="form-control"></div>
                    <div class="col-md-6"><label class="small">Subtitle</label><input type="text" name="spec_subtitle[]" class="form-control"></div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="small">Tipe Icon</label>
                        <select name="spec_icon_type[]" class="form-select" onchange="toggleIconInput(this)">
                            <option value="class">Icon Class (Web)</option>
                            <option value="image">Upload Gambar</option>
                        </select>
                    </div>
                    <div class="col-md-8 icon-input-area">
                        <div class="input-class"><label class="small">Nama Class Icon</label><input type="text" name="spec_icon_class[]" class="form-control"></div>
                        <div class="input-file d-none"><label class="small">Upload Icon</label><input type="file" name="spec_icon_file[]" class="form-control"></div>
                    </div>
                </div>
            `;
            document.getElementById('specs_container').appendChild(div);
        }

        function toggleIconInput(selectElement) {
            const parent = selectElement.closest('.row');
            const classInput = parent.querySelector('.input-class');
            const fileInput = parent.querySelector('.input-file');
            if (selectElement.value === 'class') {
                classInput.classList.remove('d-none');
                fileInput.classList.add('d-none');
            } else {
                classInput.classList.add('d-none');
                fileInput.classList.remove('d-none');
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('.segment-select').select2({
                tags: true,
                placeholder: 'Pilih atau buat segment',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function () {
                        return "Tidak ada segment. Ketik untuk membuat.";
                    }
                },
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                }
            });
        });
    </script>
</body>

</html>