<?php
// WAJIB ADA DI BARIS PALING ATAS
session_start();

// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek status login untuk header
$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$profile_pic = '../img/default-profile.png'; // Path default

if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}

// 3. Ambil data Klien untuk Marquee
$clients = [];
$result_clients = $koneksi->query("SELECT * FROM clients");
if ($result_clients) {
    while ($row = $result_clients->fetch_assoc()) {
        $clients[] = $row;
    }
}

// 4. Ambil data Proyek untuk Card
$projects = [];
$sql_projects = "SELECT id, title, subtitle_goal, category, stat_capacity, hero_image_url, overview_details, slug 
                 FROM projects 
                 ORDER BY id ASC";
$result_projects = $koneksi->query($sql_projects);
if ($result_projects) {
    while ($row = $result_projects->fetch_assoc()) {
        $projects[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio - GreenRay</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link rel="stylesheet" href="..\css\portofolio.css" />

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
    </style>
</head>

<body>
    <div class="main-container">
        <div class="header-wrapper">
            <div class="hero">
                <img class="green-ray-logo-1" src="..\img\GreenRay_Logo 1-1.png" />
                <div class="header-menu">
                    <div class="non-active"><a href="home.php">Home</a></div>
                    <div class="active-head"><a href="portofolio.php">Portfolio</a></div>
                    <div class="non-active"><a href="calc.php">Calculator</a></div>
                    <div class="non-active"><a href="katalog.html">Catalog</a></div>
                </div>

                <div class="header-actions">
                    <?php if ($is_logged_in): // JIKA USER SUDAH LOGIN ?>
                        <div class="profile-dropdown">
                            <a href="#" class="profile-toggle" id="profileToggle">
                                <img src="../<?php echo htmlspecialchars($profile_pic); ?>" alt="Profil"
                                    class="profile-picture-header">
                            </a>
                            <div class="dropdown-menu-header" id="profileDropdownMenu">
                                <div class="dropdown-item-info">
                                    Halo, <strong><?php echo htmlspecialchars($user_name); ?></strong>!
                                </div>
                                <a class="dropdown-item" href="profile.php">Profil Saya</a>
                                <a class="dropdown-item" href="contact-us.php">Bantuan / Kontak</a>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </div>
                    <?php else: // JIKA USER ADALAH TAMU (BELUM LOGIN) ?>
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

        <div class="container my-5 portfolio-content">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <img class="img-head w-100 rounded-3 shadow" src="..\img\cover-header.png"
                        alt="Portfolio Header Image" />
                </div>
            </div>

            <div class="row justify-content-center mb-5">
                <div class="col-lg-10 text-center my-4">
                    <h2 class="display-6 fw-bold">Our Commercial and Industrial Projects</h2>
                    <p class="lead">
                        Greenray has been trusted to carry out more than 150 solar power
                        plant (PLTS) projects in Indonesia.
                    </p>
                </div>

                <div class="col-lg-12">
                    <div class="client-marquee-container">
                        <div class="client-marquee" id="client-marquee">
                            <div class="marquee-content">
                                <?php if (empty($clients)): ?>
                                    <p>Tidak ada data klien.</p>
                                <?php else: ?>
                                    <?php foreach ($clients as $client): ?>
                                        <img class="client-logo"
                                            src="../<?php echo htmlspecialchars(ltrim(str_replace('../', '', $client['logo_url']), '/')); ?>"
                                            alt="<?php echo htmlspecialchars($client['name']); ?>" />
                                    <?php endforeach; ?>
                                    <?php foreach ($clients as $client): ?>
                                        <img class="client-logo"
                                            src="../<?php echo htmlspecialchars(ltrim(str_replace('../', '', $client['logo_url']), '/')); ?>"
                                            alt="<?php echo htmlspecialchars($client['name']); ?>" />
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center my-5">
                    <h2 class="display-6 fw-bold">Solar Panel Installation Project</h2>
                </div>

                <?php if (empty($projects)): ?>
                    <div class="col-12 text-center">
                        <p class="lead">Belum ada proyek yang ditambahkan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-lg-6 mb-5">
                            <img class="project-img rounded-3 mb-4 w-100"
                                src="../<?php echo htmlspecialchars($project['hero_image_url']); ?>"
                                alt="<?php echo htmlspecialchars($project['title']); ?>" />

                            <h3 class="h4 fw-bold mb-3 text-center">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </h3>

                            <ul class="project-details">
                                <li><b>Capacity:</b> <?php echo htmlspecialchars($project['stat_capacity']); ?></li>
                                <li><b>Goal:</b> <?php echo htmlspecialchars($project['subtitle_goal']); ?></li>
                                <li>
                                    <b>Details:</b> <?php echo htmlspecialchars($project['overview_details']); ?>
                                </li>
                            </ul>

                            <a href="project_detail.php?slug=<?php echo htmlspecialchars($project['slug']); ?>"
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
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo-text">
                        <img class="green-ray-logo-12" src="..\img\GreenRay_Logo 1-1.png" />
                        <div class="footer-desc">
                            Powering a cleaner, brighter future for Indonesia. We are your
                            trusted partner in sustainable energy solutions, built on
                            transparency and long-term value.
                        </div>
                    </div>
                </div>
                <div class="copyright">
                    © 2025 GreenRay. All rights reserved.
                </div>
            </div>
            <div class="footer-menu">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika Marquee (tetap sama)
            const marquee = document.getElementById('client-marquee');
            if (marquee) {
                const marqueeContent = marquee.querySelector('.marquee-content');
                // Cek jika marqueeContent ADA dan punya anak (data dari PHP)
                if (marqueeContent && marqueeContent.children.length > 0) {
                    // Tidak perlu duplikasi lagi karena PHP sudah melakukannya
                }
            }

            // ============================================
            // JAVASCRIPT UNTUK PROFILE DROPDOWN (BARU)
            // ============================================
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdownMenu = document.getElementById('profileDropdownMenu');
            if (profileToggle) {
                profileToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    profileDropdownMenu.classList.toggle('show');
                });
                // Klik di luar untuk menutup
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