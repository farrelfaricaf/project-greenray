<?php
// WAJIB ADA DI BARIS PALING ATAS
session_start();

// Hubungkan ke database
include '../koneksi.php';

// Cek status login
$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$profile_pic = '../img/default-profile.png';

if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}

// Ambil Data Produk dari Database
$products = [];
$sql = "SELECT * FROM products ORDER BY id ASC";
$result = $koneksi->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog</title>

    <link rel="stylesheet" href="..\css\katalog.css">
    <link rel="stylesheet" href="..\css\globals.css">
    <link rel="stylesheet" href="..\css\styleguide.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">

    <style>
        /* CSS untuk Dropdown Profil (Diambil dari home.php) */
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
            /* Memastikan teks rata kiri */
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

        /* Style tambahan untuk kartu produk agar gambar rapi */
        .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 10px;
            background-color: #f8f9fa;
        }

        /* Style list bullet points */
        .custom-bullet-list {
            list-style-type: disc;
            padding-left: 1.5rem;
            text-align: left;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .custom-bullet-list li {
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="beranda">

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
            <div class="title-heading">
                <div class="text-heading">
                    <span>
                        <span class="text-heading-span">Our </span>
                        <span class="text-heading-span2">Products</span>
                    </span>
                </div>
            </div>

            <div class="card-container">

                <?php if (empty($products)): ?>
                    <div class="col-12 text-center w-100">
                        <p class="text-muted">Belum ada produk yang ditambahkan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        // Decode JSON fitur untuk ditampilkan sebagai list
                        $features = json_decode($product['key_features_json'], true);
                        // Ambil maksimal 2 fitur agar tampilan kartu rapi
                        $display_features = array_slice($features ?? [], 0, 2);
                        ?>
                        <div class="card card-solar">
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">

                            <div class="card-body">
                                <h5 class="card-title text-center">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                    <br>
                                    <span style="font-size: 0.9em; font-weight: normal;">
                                        <?php echo htmlspecialchars($product['subtitle']); ?>
                                    </span>
                                </h5>
                                <ul class="custom-bullet-list">
                                    <?php if (!empty($display_features)): ?>
                                        <?php foreach ($display_features as $feat): ?>
                                            <li><?php echo htmlspecialchars($feat['title']); ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li style="list-style: none;">No features listed</li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <a href="katalog_detail.php?slug=<?php echo htmlspecialchars($product['slug']); ?>"
                                class="btn btn-cta-solar">
                                <span>View Details</span>
                                <span class="icon-arrow"></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>


        <div class="footer">
            <div class="frame-51">
                <div class="frame-42">
                    <img class="green-ray-logo-12" src="../img/GreenRay_Logo 1-1.png" />
                    <div class="dec-footer">
                        Powering a cleaner, brighter future for Indonesia. We are your
                        trusted partner in sustainable energy solutions, built on
                        transparency and long-term value.
                    </div>
                </div>
                <div class="copyright">
                    © 2025 GreenRay. All rights reserved.
                </div>
            </div>
            <div class="menu-footer">
                <div class="menu-container-footer">
                    <div class="title-footer">Quick Links</div>
                    <div class="dec-container-footer">
                        <div class="list-footer"><a href="home.php">Home</a></div>
                        <div class="list-footer"><a href="portofolio.php">Our Portfolio</a></div>
                        <div class="list-footer"><a href="calc.php">Saving Calculator</a></div>
                    </div>
                </div>
                <div class="menu-container-footer">
                    <div class="title-footer">Get In Touch</div>
                    <div class="dec-container-footer">
                        <div class="list-footer">
                            <a href="contact-us.php">Quick Consultation via WhatsApp</a>
                        </div>
                        <div class="list-footer">
                            <a href="contact-us.php">Send a Formal Inquiry Email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <script>
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