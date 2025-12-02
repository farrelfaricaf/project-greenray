<?php
session_start();
include '../koneksi.php';

// 1. Cek Status Login (Header)
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';
$profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';

// 2. Cek Slug
if (!isset($_GET['slug'])) {
    die("Error: Produk tidak ditemukan (Slug kosong).");
}

$slug = $_GET['slug'];

// 3. Ambil Data Produk Utama
$stmt = $koneksi->prepare("SELECT * FROM products WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Error: Produk tidak ditemukan di database.");
}

// 4. Ambil Gambar Galeri untuk Carousel
$gallery = [];
$stmt_gal = $koneksi->prepare("SELECT image_url FROM product_gallery_images WHERE product_id = ?");
$stmt_gal->bind_param("i", $product['id']);
$stmt_gal->execute();
$res_gal = $stmt_gal->get_result();
while ($row = $res_gal->fetch_assoc()) {
    $gallery[] = $row['image_url'];
}

// Jika galeri kosong, gunakan gambar utama sebagai fallback agar carousel tidak rusak
if (empty($gallery) && !empty($product['image_url'])) {
    $gallery[] = $product['image_url'];
}

// 5. Decode JSON Data
$features = json_decode($product['key_features_json'], true) ?? [];
$specs = json_decode($product['specifications_json'], true) ?? [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Detail Produk</title>

    <link rel="stylesheet" href="../css/detKatalog.css">
    <link rel="stylesheet" href="../css/globals.css">
    <link rel="stylesheet" href="../css/styleguide.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/favicon.png" sizes="180px180">

    <style>
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-picture-header {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #136000;
        }

        .dropdown-menu-header {
            display: none;
            position: absolute;
            right: 0;
            top: 60px;
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.1);
            z-index: 100;
            border-radius: 8px;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-align: left;
        }

        .dropdown-menu-header.show {
            display: block;
        }

        .dropdown-menu-header .dropdown-item,
        .dropdown-menu-header .dropdown-item-info {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.95rem;
        }

        .dropdown-menu-header .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .dropdown-menu-header .dropdown-item-info {
            background-color: #f9f9f9;
            font-weight: 500;
        }

        /* Fix Image Size in Carousel */
        .carousel-item img {
            height: 400px;
            /* Atur tinggi sesuai keinginan */
            object-fit: contain;
            /* Agar gambar tidak gepeng */
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="detail-catalog">

        <div class="header-wrapper">
            <div class="hero">
                <img class="green-ray-logo-1" src="../img/GreenRay_Logo 1-1.png" />
                <div class="header-menu">
                    <div class="non-active"><a href="home.php">Home</a></div>
                    <div class="non-active"><a href="portofolio.php">Portfolio</a></div>
                    <div class="non-active"><a href="calc.php">Calculator</a></div>
                    <div class="active-head"><a href="katalog.php">Catalog</a></div>
                </div>
                <div class="header-actions">
                    <?php if ($is_logged_in): ?>
                        <div class="profile-dropdown">
                            <a href="#" class="profile-toggle" id="profileToggle">
                                <img src="../<?php echo htmlspecialchars($profile_pic); ?>" alt="Profil"
                                    class="profile-picture-header">
                            </a>
                            <div class="dropdown-menu-header" id="profileDropdownMenu">
                                <div class="dropdown-item-info">Halo,
                                    <strong><?php echo htmlspecialchars($user_name); ?></strong>!
                                </div>
                                <a class="dropdown-item" href="profile.php">Profil Saya</a>
                                <a class="dropdown-item" href="contact-us.php">Bantuan / Kontak</a>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a class="login-btn" href="signin.php">
                            <div class="login-text">Login</div>
                            <span class="akar-icons--door"></span>
                        </a>
                        <a class="contact-us-btn" href="contact-us.php">
                            <div class="contact-us-text">Contact Us</div>
                            <span class="mynaui--arrow-right"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="big-container">
            <div class="image-catalog">

                <div class="nav-area">
                    <a href="#" class="back-button" onclick="history.back(); return false;">
                        <span class="formkit--arrowleft"></span>
                        <div class="contact-us2">Back</div>
                    </a>

                    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                        aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="katalog.php"
                                    class="text-decoration-none text-secondary fw-medium text-dark">Catalog</a></li>
                            <li class="breadcrumb-item active fw-medium text-dark" aria-current="page">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </li>
                        </ol>
                    </nav>
                </div>

                <div class="container-text">

                    <div class="image-container carousel slide" id="productCarousel" data-bs-ride="carousel"
                        data-bs-interval="3000">
                        <?php if (count($gallery) > 1): ?>
                            <div class="carousel-indicators">
                                <?php foreach ($gallery as $index => $img): ?>
                                    <button type="button" data-bs-target="#productCarousel"
                                        data-bs-slide-to="<?php echo $index; ?>"
                                        class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="carousel-inner">
                            <?php foreach ($gallery as $index => $img): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="../<?php echo htmlspecialchars($img); ?>" class="d-block w-100 rounded-3"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($gallery) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"
                                    style="background-color: black; border-radius: 50%;"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"
                                    style="background-color: black; border-radius: 50%;"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="title-heading">
                        <div class="text-heading">
                            <?php echo htmlspecialchars($product['name']); ?>
                            <span style="font-size: 0.6em; color: #666; display:block; margin-top:5px;">
                                <?php echo htmlspecialchars($product['subtitle']); ?>
                            </span>
                        </div>
                        <div class="dec-heading">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($features)): ?>
                <div class="section1">
                    <div class="title2">Key Features</div>
                    <div class="card-container">
                        <?php foreach ($features as $feat): ?>
                            <div class="fitur">
                                <div class="text-card">
                                    <strong><?php echo htmlspecialchars($feat['title']); ?></strong>
                                    <br><br>
                                    <?php echo htmlspecialchars($feat['description']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($specs)): ?>
                <div class="section1">
                    <div class="title-heading">
                        <div class="title2">
                            The Powerhouse Under the Hood: Full Specifications
                        </div>
                    </div>
                    <div class="card-container">
                        <?php foreach ($specs as $spec): ?>
                            <?php
                            // Cek apakah icon berupa Class atau Gambar
                            $iconHtml = '';
                            if (isset($spec['icon_type']) && $spec['icon_type'] == 'image') {
                                // Jika gambar upload
                                $iconHtml = '<img src="../' . htmlspecialchars($spec['icon_val']) . '" style="width: 48px; height: 48px; object-fit: contain; margin-right: 15px;">';
                            } else {
                                // Jika icon class (atau format lama/manual)
                                // Kita ambil valuenya, default fallback ke 'material-symbols--check' jika kosong
                                $val = $spec['icon_val'] ?? ($spec['icon_class'] ?? 'material-symbols--check');
                                $iconHtml = '<span class="' . htmlspecialchars($val) . '"></span>';
                            }
                            ?>
                            <div class="fitur">
                                <?php echo $iconHtml; ?>
                                <div class="text-card">
                                    <span>
                                        <span class="text-card-span">
                                            <?php echo htmlspecialchars($spec['title']); ?>
                                            <br />
                                        </span>
                                        <span class="text-card-span2">
                                            <?php echo htmlspecialchars($spec['subtitle']); ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cta">
                <div class="title2 text-center">NEED EXPERT GUIDANCE?</div>
                <a href="contact-us.php" class="login cta-book">
                    <span class="material-symbols--chat"></span>
                    <div class="login2">BOOK A FREE CONSULTATION</div>
                </a>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        // Script Profile Dropdown
        document.addEventListener('DOMContentLoaded', function () {
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdownMenu = document.getElementById('profileDropdownMenu');
            if (profileToggle) {
                profileToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    profileDropdownMenu.classList.toggle('show');
                });
                window.addEventListener('click', function (e) {
                    if (profileToggle && !profileToggle.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                        profileDropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>

</html>