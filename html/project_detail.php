<?php
session_start();
include '../koneksi.php';

// 1. Cek status login untuk header
$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$profile_pic = '../img/default-profile.png';

if ($is_logged_in) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}

// 2. Ambil slug dari URL
if (!isset($_GET['slug'])) {
    die("Error: Proyek tidak ditemukan. (Slug tidak ada)");
}

$slug = $_GET['slug'];

// 3. Ambil data proyek dari database
$stmt = $koneksi->prepare("SELECT * FROM projects WHERE slug = ? LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Proyek dengan slug '$slug' tidak ditemukan.");
}

$project = $result->fetch_assoc();
$project_id = $project['id']; // Ambil ID untuk query galeri

// 4. Ambil gambar galeri (jika ada)
$gallery_images = [];
$stmt_gallery = $koneksi->prepare("SELECT * FROM project_gallery_images WHERE project_id = ?");
$stmt_gallery->bind_param("i", $project_id);
$stmt_gallery->execute();
$result_gallery = $stmt_gallery->get_result();
while ($row = $result_gallery->fetch_assoc()) {
    $gallery_images[] = $row;
}

// 5. Decode JSON untuk Technical Specs
$tech_specs = json_decode($project['tech_specs_json'], true);
// Pastikan $tech_specs adalah array, walau JSON-nya error/kosong
if (!is_array($tech_specs)) {
    $tech_specs = [];
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - GreenRay</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="..\css\detail-portofolio.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">

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
    <div class="portfolio-detail-wrapper">

        <div class="hero">
            <img class="green-ray-logo-1" src="..\img\GreenRay_Logo 1-1.png" />
            <div class="header-menu">
                <div class="non-active"><a href="home.php">Home</a></div>
                <div class="active-head"><a href="portofolio.php">Portfolio</a></div>
                <div class="non-active"><a href="calc.php">Calculator</a></div>
                <div class="non-active"><a href="katalog.html">Catalog</a></div>
            </div>

            <div class="header-actions">
                <?php if ($is_logged_in): ?>
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

        <a href="portofolio.php" class="back-button">
            <span class="formkit--arrowleft"></span>
            <div class="contact-us2">Back to Projects</div>
        </a>

        <div class="card shadow-sm border-0 overflow-hidden mb-4">
            <div class="position-relative">
                <img src="../<?php echo htmlspecialchars($project['hero_image_url']); ?>"
                    class="card-img-top hero-image" alt="<?php echo htmlspecialchars($project['title']); ?>">
                <div class="hero-image-overlay"></div>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span
                        class="badge bg-light text-dark rounded-pill px-3 py-2"><?php echo htmlspecialchars($project['category']); ?></span>
                    <div class="d-flex align-items-center gap-1 text-muted small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                            class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                        </svg>
                        <span><?php echo htmlspecialchars($project['location_text']); ?></span>
                    </div>
                </div>
                <h1 class="h4 fw-bold text-dark mb-1"><?php echo htmlspecialchars($project['title']); ?></h1>
                <p class="mb-0 text-secondary"><?php echo htmlspecialchars($project['subtitle_goal']); ?></p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="stat-label" style="color: #155DFC;">Capacity</span>
                        </div>
                        <div class="h4 fw-bold text-dark"><?php echo htmlspecialchars($project['stat_capacity']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="stat-label" style="color: #00A63E;">CO₂ Reduction</span>
                        </div>
                        <div class="h4 fw-bold text-dark">
                            <?php echo htmlspecialchars($project['stat_co2_reduction']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="stat-label" style="color: #9810FA;">Timeline</span>
                        </div>
                        <div class="h4 fw-bold text-dark"><?php echo htmlspecialchars($project['stat_timeline']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="stat-label" style="color: #F54900;">Investment</span>
                        </div>
                        <div class="h4 fw-bold text-dark"><?php echo htmlspecialchars($project['stat_investment']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-bold mb-4">Project Overview</h2>
                <div class="mb-3">
                    <h3 class="h6 fw-bold text-dark">Result</h3>
                    <p class="mb-0 text-secondary"><?php echo htmlspecialchars($project['overview_result']); ?></p>
                </div>
                <hr class="my-3">
                <div class="mb-3">
                    <h3 class="h6 fw-bold text-dark">Details</h3>
                    <p class="mb-0 text-secondary"><?php echo htmlspecialchars($project['overview_details']); ?></p>
                </div>
                <hr class="my-3">
                <div>
                    <h3 class="h6 fw-bold text-dark">Energy Generation</h3>
                    <p class="mb-0 text-secondary">
                        <?php echo nl2br(htmlspecialchars($project['overview_generation'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-bold mb-4">Project Gallery</h2>
                <div class="row g-3">
                    <?php if (empty($gallery_images)): ?>
                        <p class="text-muted">Tidak ada gambar galeri untuk proyek ini.</p>
                    <?php else: ?>
                        <?php foreach ($gallery_images as $index => $image): ?>
                            <div class="col-lg-6">
                                <img src="../<?php echo htmlspecialchars($image['image_url']); ?>"
                                    alt="Gallery Image <?php echo $index + 1; ?>"
                                    class="img-fluid rounded-3 gallery-image-secondary">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-bold mb-4">Challenges & Solutions</h2>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h3 class="h6 fw-bold text-danger challenges-title mb-3">Challenges</h3>
                        <ul class="list-unstyled custom-list challenges">
                            <?php echo $project['challenges_html']; ?>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h6 fw-bold text-success solutions-title mb-3">Solutions</h3>
                        <ul class="list-unstyled custom-list solutions">
                            <?php echo $project['solutions_html']; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-bold mb-4">Project Impact</h2>
                <ul class="list-unstyled custom-list solutions">
                    <?php echo $project['impact_html']; ?>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 card-title fw-bold mb-4">Technical Specifications</h2>
                <ul class="list-group list-group-flush">
                    <?php if (empty($tech_specs)): ?>
                        <li class="list-group-item px-0">Tidak ada spesifikasi teknis.</li>
                    <?php else: ?>
                        <?php foreach ($tech_specs as $spec): ?>
                            <li class="list-group-item px-0">
                                <div class="spec-label"><?php echo htmlspecialchars($spec['label']); ?></div>
                                <div class="spec-value"><?php echo htmlspecialchars($spec['value']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // (Logika Profile Dropdown)
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