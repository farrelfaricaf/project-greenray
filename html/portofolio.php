<?php
// WAJIB ADA DI BARIS PALING ATAS
session_start();

// 1. Hubungkan ke database
include '../koneksi.php';

$page_data = [];
$res_page = $koneksi->query("SELECT * FROM page_portfolio WHERE id = 1");
if ($res_page && $res_page->num_rows > 0) {
    $page_data = $res_page->fetch_assoc();
} else {
    // Default jika DB kosong
    $page_data = [
        'header_title' => 'Our Projects',
        'header_desc' => 'Welcome to our portfolio gallery.',
        'header_image' => 'img/cover-header.png',
        'projects_title' => 'Solar Panel Installation Projects'
    ];
}

// Fungsi Helper Gambar
function fixPath($path)
{
    return !empty($path) ? str_replace('../', '', $path) : 'img/placeholder.png';
}

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
    <title>Portfolio</title>
    <link rel="stylesheet" href="..\css\portofolio.css" />
    <link rel="stylesheet" href="..\css\globals.css">
    <link rel="stylesheet" href="..\css\styleguide.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">

    <style>
        :root {
            /* Colors */
            --putih: #ffffff;
            --hitam: #000000;
            --hijau: #136000;
            --hijau-gelap: #072300;

            /* Fonts */
            /* Effects */
            --efek-shadow-tipis-box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.26),
                0px 5px 5px 0px rgba(0, 0, 0, 0.23), 0px 11px 7px 0px rgba(0, 0, 0, 0.13),
                0px 20px 8px 0px rgba(0, 0, 0, 0.04), 0px 31px 9px 0px rgba(0, 0, 0, 0);
        }

        .btn-cta-solar {
            background: var(--hijau) !important;
            border-radius: 0.756875rem;
            padding: 0.756875rem 1.51375rem;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            align-self: stretch;
            border: none;
            transition: background-color 0.3s ease;
            width: 100%;
            text-decoration: none;
            color: var(--putih);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .btn-cta-solar:hover {
            background-color: #0f4d00;
            color: white;
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .portfolio-header-wrapper {
            width: 100%;
            max-width: 1280px;
            /* Batas lebar maksimal container gambar */
            margin: 0 auto 40px auto;
            /* Tengah horizontal & jarak bawah */
            text-align: center;
            /* Agar gambar di tengah jika lebih kecil */
        }

        .portfolio-header-img {
            width: 100%;
            /* Lebar menyesuaikan container */
            height: auto;
            /* Tinggi otomatis proporsional */
            max-height: 350px;
            /* BATAS MAKSIMAL TINGGI */
            object-fit: cover;
            /* Potong rapi jika rasio beda */
            object-position: center;
            /* Fokus tengah */
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            <?php include 'includes/header.php'; ?>
        </div>

        <div class="container my-5 portfolio-content">
            <div class="row mb-5">
                <div class="portfolio-header-wrapper">
                    <img class="img-head w-100 rounded-3 shadow portfolio-header-img"
                        src="../<?php echo fixPath($page_data['header_image']); ?>" alt="Portfolio Header" />
                </div>
            </div>

            <div class="row justify-content-center mb-5">
                <div class="col-lg-10 text-center my-4">
                    <h2 class="display-6 fw-bold"><?php echo htmlspecialchars($page_data['header_title']); ?></h2>
                    <p class="lead text-secondary">
                        <?php echo nl2br(htmlspecialchars($page_data['header_desc'])); ?>
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
                    <h2 class="display-6 fw-bold">
                        <?php echo htmlspecialchars($page_data['projects_title'] ?? 'Solar Panel Installation Projects'); ?>
                    </h2>
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
                                class="btn btn btn-cta-solar">
                                <span>View Details</span>
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
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
        });
    </script>
</body>

</html>