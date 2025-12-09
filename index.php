<?php
session_start();
include 'koneksi.php';

function fixPath($path, $default = 'img/placeholder.png')
{

  $clean = str_replace('../', '', $path ?? '');


  if (empty($clean)) {
    return $default;
  }
  return $clean;
}


$lp = [];
$res_lp = $koneksi->query("SELECT * FROM landing_page WHERE id = 1");
if ($res_lp && $res_lp->num_rows > 0) {
  $lp = $res_lp->fetch_assoc();
} else {

  $lp = [
    'hero_title' => 'Solar Energy for a<br>Greener Tomorrow',
    'hero_subtitle' => 'Switch to clean energy.',
    'hero_button_primary' => 'Calculate Savings',
    'hero_button_secondary' => 'Explore Catalog',
    'stat_1_value' => '200+',
    'stat_1_label' => 'Projects',
    'stat_2_value' => '300+',
    'stat_2_label' => 'Clients',
    'stat_3_value' => '100%',
    'stat_3_label' => 'Trusted',
    'stat_4_value' => '50+',
    'stat_4_label' => 'Cities',
  ];
}


$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';
$profile_pic = isset($_SESSION['user_profile_pic']) ? $_SESSION['user_profile_pic'] : 'img/default-profile.png';
$profile_pic = str_replace('../', '', $profile_pic);

$projects = [];
$res_proj = $koneksi->query("SELECT * FROM projects ORDER BY id DESC LIMIT 3");
if ($res_proj) {
  while ($row = $res_proj->fetch_assoc()) {
    $projects[] = $row;
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GreenRay</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href=".\css\globals.css" />
  <link rel="stylesheet" href=".\css\styleguide.css" />
  <link rel="stylesheet" href=".\css\ldpage.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
    rel="stylesheet" />
  <link rel="icon" type="image/png" href=".\img\favicon.png" sizes="180px180" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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

    .hover-lift {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
      transform: translateY(-8px);
      box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.125) !important;
    }


    .service-card:hover .transition-transform {
      transform: scale(1.05);
      transition: transform 0.5s ease;
    }


    .service-card:hover .group-hover-arrow svg {
      transform: translateX(5px);
    }


    .object-fit-cover {
      object-fit: cover;
    }

    .flip-card-wrapper {
      background-color: transparent;
      width: 100%;
      height: 350px;

      perspective: 1000px;

    }

    .flip-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      text-align: center;
      transition: transform 0.8s;
      transform-style: preserve-3d;
    }


    .flip-card-wrapper.flipped .flip-card-inner {
      transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
      position: absolute;
      width: 100%;
      height: 100%;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      top: 0;
      left: 0;
    }


    .flip-card-back {
      transform: rotateY(180deg);
    }
  </style>
  <meta name="description"
    content="Go solar with GREENRAY and take control of your energy bills. Seamless solar panel installation for homes and businesses across Indonesia." />
</head>

<body>
  <div class="desktop-8">
    <main>
      <div class="header-wrapper">
        <?php include 'includes/landing/header_ldpage.php'; ?>
      </div>

      <section class="hero-section ps-5 pt-5 mt-5 mb-5 bg-soft-green position-relative overflow-hidden">
        <div class="container position-relative z-2 py-5">
          <div class="row align-items-center gy-5 min-vh-75">
            <div class="col-lg-6 order-2 order-lg-1 text-center text-lg-start">
              <div
                class="badge bg-white text-success shadow-sm px-3 py-2 rounded-pill mb-3 fw-bold border border-success-subtle">
                🌱 #1 Solar Energy Solution
              </div>
              <h1 class="hero-title display-4 fw-bold text-dark mb-3">
                <?php


                $lines = preg_split('/\r\n|\r|\n/', $lp['hero_title']);


                echo htmlspecialchars($lines[0] ?? '');


                if (isset($lines[1]) && !empty($lines[1])) {
                  echo '<br><span class="text-success">' . htmlspecialchars($lines[1]) . '</span>';
                }


                for ($i = 2; $i < count($lines); $i++) {
                  if (!empty($lines[$i])) {
                    echo '<br>' . htmlspecialchars($lines[$i]);
                  }
                }
                ?>
              </h1>
              <p class="lead text-secondary mb-4 mx-auto mx-lg-0" style="max-width: 500px;">
                <?php echo htmlspecialchars($lp['hero_subtitle']); ?>
              </p>
              <div class="d-flex gap-3 flex-wrap">
                <a href="<?php echo htmlspecialchars($lp['hero_button_primary_url']); ?>"
                  class="btn btn-success btn-lg rounded-pill px-4 py-3 fw-bold shadow-success">
                  <?php echo htmlspecialchars($lp['hero_button_primary']); ?>
                </a>
                <a href="<?php echo htmlspecialchars($lp['hero_button_secondary_url']); ?>"
                  class="btn btn-white btn-lg rounded-pill px-4 py-3 fw-bold border shadow-sm">
                  <?php echo htmlspecialchars($lp['hero_button_secondary']); ?>
                </a>
              </div>
              <div class="row mt-5 pt-4 g-4 stats-row border-top border-success-subtle">
                <div class="col-3 border-end border-success-subtle ps-4">
                  <h2 class="fw-bold text-success"><?php echo $lp['stat_1_value']; ?></h2>
                  <small class="text-muted fw-medium"><?php echo $lp['stat_1_label']; ?></small>
                </div>
                <div class="col-3 border-end border-success-subtle ps-4">
                  <h2 class="fw-bold text-success"><?php echo $lp['stat_2_value']; ?></h2>
                  <small class="text-muted fw-medium"><?php echo $lp['stat_2_label']; ?></small>
                </div>
                <div class="col-3 border-end border-success-subtle ps-4">
                  <h2 class="fw-bold text-success"><?php echo $lp['stat_3_value']; ?></h2>
                  <small class="text-muted fw-medium"><?php echo $lp['stat_3_label']; ?></small>
                </div>
                <div class="col-3 col-sm-3">
                  <h2 class="fw-bold text-success"><?php echo $lp['stat_4_value']; ?></h2>
                  <small class="text-muted fw-medium"><?php echo $lp['stat_4_label']; ?></small>
                </div>
              </div>
            </div>

            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <div class="hero-image-wrapper position-relative">
                <img src="<?php echo fixPath($lp['hero_image']); ?>" alt="Happy Family with Solar"
                  class="img-fluid hero-main-img rounded-4 shadow-lg position-relative z-2" />

                <div
                  class="position-absolute bottom-0 start-0 mb-4 ms-n4 bg-white p-3 rounded-4 shadow-lg d-flex align-items-center gap-3 animate-float z-3"
                  style="max-width: 200px; text-align: left">
                  <div class="bg-success text-white rounded-circle p-2 d-flex">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 2v20M2 12h20" />
                    </svg>
                  </div>
                  <div>
                    <span class="fw-bold d-block text-dark lh-1">Save up to</span>
                    <strong class="text-success fs-5">60%</strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="why-choose-us px-5 py-5 position-relative" style="background-color: #ffffff;">
        <div class="container py-lg-5">

          <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-8">
              <h2 class="display-5 fw-bold text-dark mb-3">
                Tired of <span class="text-danger position-relative d-inline-block">
                  High Electricity Bills?
                  <svg class="position-absolute start-0 bottom-0 w-100" height="10"
                    style="transform: translateY(5px); opacity: 0.3;" viewBox="0 0 200 9" fill="none"
                    xmlns="https://www.w3.org/2000/svg">
                    <path d="M2.00025 6.99997C30.5082 2.08486 97.1038 -2.3321 197.996 3.33329" stroke="#DC3545"
                      stroke-width="3" stroke-linecap="round" />
                  </svg>
                </span>
              </h2>
              <p class="text-secondary fs-5" style="line-height: 1.8;">
                It's time for a smarter, cleaner solution. Stop renting your energy from the grid—start generating your
                own asset from the sun.
              </p>
            </div>
          </div>

          <div class="row g-4 justify-content-center">

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div
                    class="flip-card-front bg-white border rounded-4 p-4 shadow-sm d-flex flex-column align-items-center justify-content-center">
                    <div
                      class="icon-box mb-4 bg-danger bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 80px; height: 80px;">
                      <img src="img/vector/bill.svg" width="40" alt="Savings">
                    </div>
                    <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($lp['why_1_title']); ?></h4>
                    <p class="text-secondary mb-4"><?php echo htmlspecialchars($lp['why_1_desc']); ?></p>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-danger text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['why_1_back_title']); ?></h4>
                    <p class="mb-4 lh-base">
                      <?php echo htmlspecialchars($lp['why_1_back_desc']); ?>
                    </p>
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div
                    class="flip-card-front bg-white border rounded-4 p-4 shadow-sm d-flex flex-column align-items-center justify-content-center">
                    <div
                      class="icon-box mb-4 bg-warning bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 80px; height: 80px;">
                      <img src="img/vector/energy.svg" width="40" alt="Energy">
                    </div>
                    <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($lp['why_2_title']); ?></h4>
                    <p class="text-secondary mb-4"><?php echo htmlspecialchars($lp['why_2_desc']); ?></p>
                    <button type="button" class="btn btn-outline-warning rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-warning text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['why_2_back_title']); ?></h4>
                    <p class="mb-4 lh-base">
                      <?php echo htmlspecialchars($lp['why_2_back_desc']); ?>
                    </p>
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div
                    class="flip-card-front bg-white border rounded-4 p-4 shadow-sm d-flex flex-column align-items-center justify-content-center">
                    <div
                      class="icon-box mb-4 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                      style="width: 80px; height: 80px;">
                      <img src="img/vector/contribute.svg" width="40" alt="Eco">
                    </div>
                    <h4 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($lp['why_3_title']); ?></h4>
                    <p class="text-secondary mb-4"><?php echo htmlspecialchars($lp['why_3_desc']); ?></p>
                    <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['why_3_back_title']); ?></h4>
                    <p class="mb-4 lh-base">
                      <?php echo htmlspecialchars($lp['why_3_back_desc']); ?>
                    </p>
                    <button type="button" class="btn btn-light rounded-pill px-4 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <section class="portfolio-section py-5 bg-light-custom" style="background-color: #f8f9fa;">
        <div class="container">

          <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
              <h2 class="display-5 fw-bold text-dark mb-2">Recent Projects</h2>
              <p class="text-secondary mb-0 fs-5">See how we help our clients achieve energy independence.</p>
            </div>
            <a href="html/portofolio.php"
              class="btn btn-outline-success rounded-pill px-4 d-none d-md-inline-block fw-bold">
              View All Projects
            </a>
          </div>

          <div class="row g-4">
            <?php if (!empty($projects)): ?>
              <?php foreach ($projects as $proj): ?>
                <?php

                $img_path = str_replace('../', '', $proj['hero_image_url']);


                $cat = htmlspecialchars($proj['category']);
                $badge_class = 'bg-success text-success';
                if (stripos($cat, 'Commercial') !== false)
                  $badge_class = 'bg-primary text-primary';
                if (stripos($cat, 'Industrial') !== false)
                  $badge_class = 'bg-warning text-warning';
                ?>

                <div class="col-md-4">
                  <div
                    class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift position-relative bg-white">

                    <div class="position-relative" style="height: 240px; overflow: hidden;">
                      <img src="<?php echo htmlspecialchars($img_path); ?>"
                        class="card-img-top w-100 h-100 object-fit-cover transition-image"
                        alt="<?php echo htmlspecialchars($proj['title']); ?>">
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                      <div class="mb-2">
                        <span class="badge <?php echo $badge_class; ?> bg-opacity-10 px-3 py-2 rounded-pill">
                          <?php echo $cat; ?>
                        </span>
                      </div>

                      <h5 class="card-title fw-bold text-dark mb-2">
                        <?php echo htmlspecialchars($proj['title']); ?>
                      </h5>

                      <p class="card-text text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                        <strong>Capacity:</strong> <?php echo htmlspecialchars($proj['stat_capacity']); ?><br>
                        <?php echo htmlspecialchars($proj['subtitle_goal']); ?>
                      </p>

                      <a href="html/project_detail.php?slug=<?php echo $proj['slug']; ?>"
                        class="stretched-link text-decoration-none fw-bold text-success d-flex align-items-center">
                        View Details
                        <svg xmlns="https://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="ms-2">
                          <path d="M5 12h14" />
                          <path d="m12 5 7 7-7 7" />
                        </svg>
                      </a>
                    </div>
                  </div>
                </div>

              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12 text-center">
                <p class="text-muted">Belum ada proyek yang ditampilkan.</p>
              </div>
            <?php endif; ?>
          </div>

          <div class="text-center mt-4 d-md-none">
            <a href="html/portofolio.php" class="btn btn-outline-success rounded-pill px-4 w-100 fw-bold">View All
              Projects</a>
          </div>

        </div>
      </section>

      <section class="our-services px-5 py-5" style="background-color: #f8f9fa;">
        <div class="container py-lg-4">

          <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark mb-3">Our Services</h2>
            <p class="text-secondary fs-5 mx-auto" style="max-width: 650px;">
              Tailored solar solutions for every need. Click to flip and learn more.
            </p>
          </div>

          <div class="row g-4">

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm p-0">
                    <div class="service-img-wrapper position-relative" style="height: 220px; width: 100%;">
                      <img src="<?php echo fixPath($lp['serv_1_image']); ?>"
                        class="img-fluid w-100 h-100 object-fit-cover" alt="Residential">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($lp['serv_1_title']); ?></h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3"><?php echo htmlspecialchars($lp['serv_1_desc']); ?></p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['serv_1_back_title']); ?></h4>
                    <p class="mb-4 text-center">
                      <?php echo htmlspecialchars($lp['serv_1_back_desc']); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($lp['serv_1_btn_url']); ?>"
                      class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      <?php echo htmlspecialchars($lp['serv_1_btn_text']); ?>
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm p-0">
                    <div class="service-img-wrapper position-relative" style="height: 220px; width: 100%;">
                      <img src="<?php echo fixPath($lp['serv_2_image']); ?>"
                        class="img-fluid w-100 h-100 object-fit-cover" alt="Commercial">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($lp['serv_2_title']); ?></h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3"><?php echo htmlspecialchars($lp['serv_2_desc']); ?></p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['serv_2_back_title']); ?></h4>
                    <p class="mb-4 text-center">
                      <?php echo htmlspecialchars($lp['serv_2_back_desc']); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($lp['serv_2_btn_url']); ?>"
                      class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      <?php echo htmlspecialchars($lp['serv_2_btn_text']); ?>
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="flip-card-wrapper">
                <div class="flip-card-inner">

                  <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm p-0">
                    <div class="service-img-wrapper position-relative" style="height: 220px; width: 100%;">
                      <img src="<?php echo fixPath($lp['serv_3_image']); ?>"
                        class="img-fluid w-100 h-100 object-fit-cover" alt="Maintenance">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($lp['serv_3_title']); ?></h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3"><?php echo htmlspecialchars($lp['serv_3_desc']); ?></p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($lp['serv_3_back_title']); ?></h4>
                    <p class="mb-4 text-center">
                      <?php echo htmlspecialchars($lp['serv_3_back_desc']); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($lp['serv_3_btn_url']); ?>"
                      class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      <?php echo htmlspecialchars($lp['serv_3_btn_text']); ?>
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      <i class="fa-solid fa-arrows-rotate fa-spin-pulse mr-3"></i> Back
                    </button>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <section class="cta-section px-5 py-5">
        <div class="container">
          <div class="cta-box text-center text-white rounded-5 p-5" style="
                background-image: url('<?php echo fixPath($lp['cta_image']); ?>');
                background-size: cover;
                background-position: center;
                position: relative;
              ">
            <div class="cta-overlay" style="
                  background: rgba(0, 0, 0, 0.6);
                  position: absolute;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  border-radius: 30px;
                "></div>
            <div class="position-relative" style="z-index: 2">
              <h2 class="fw-bold display-5 mb-3">
                Ready to Switch to Solar?
              </h2>
              <p class="fs-5 mb-5">
                Get a free quote and consultation today. Start your journey
                towards energy freedom.
              </p>
              <a href="html/home.php" class="btn btn-success btn-lg rounded-pill px-5 py-3 fw-bold">
                Get Started Now
              </a>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php include 'html/includes/footer.php'; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      const buttons = document.querySelectorAll('.btn-trigger-flip');

      buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();


          const cardWrapper = this.closest('.flip-card-wrapper');


          if (cardWrapper) {
            cardWrapper.classList.toggle('flipped');
          }
        });
      });
    });
  </script>
</body>

</html>