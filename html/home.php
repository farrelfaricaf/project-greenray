<?php
session_start();
include '../koneksi.php';

// 2. Ambil Data PRODUK (Limit 4)
$products = [];
$res_prod = $koneksi->query("SELECT * FROM products ORDER BY id ASC LIMIT 4");
if ($res_prod) {
    while ($row = $res_prod->fetch_assoc()) {
        $products[] = $row;
    }
}

// 3. Ambil Data PROYEK/PORTFOLIO (Limit 3)
$projects = [];
$res_proj = $koneksi->query("SELECT * FROM projects ORDER BY id ASC LIMIT 3");
if ($res_proj) {
    while ($row = $res_proj->fetch_assoc()) {
        $projects[] = $row;
    }
}

// 4. Ambil Data REVIEWS (Limit 6)
$reviews = [];
$res_rev = $koneksi->query("SELECT * FROM reviews WHERE is_visible = 1 ORDER BY id ASC");
if ($res_rev) {
    while ($row = $res_rev->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// 5. Ambil Data FAQ (Limit 3)
$faqs = [];
$res_faq = $koneksi->query("SELECT * FROM faqs ORDER BY id DESC");
if ($res_faq) {
    while ($row = $res_faq->fetch_assoc()) {
        $faqs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="..\css\home.css">
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
        /* CSS Dropdown Profil */
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

        /* --- STYLE CARD DARI KATALOG.PHP (DISAMAKAN & DIFIX LEBARNYA) --- */
        .card-img-top {
            height: 200px;
            object-fit: contain;
            padding: 10px;
            background-color: #f8f9fa;
        }

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

        /* PERBAIKAN LEBAR CARD DI HOME */
        .catalog-container .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            /* Jarak antar kartu */
            justify-content: center;
            width: 100%;
            /* Pastikan container mengambil lebar penuh */
        }

        /* Style Khusus untuk Kartu agar lebarnya sama persis */
        .card.card-solar {
            width: 280px;
            /* Lebar tetap (fixed width) agar seragam */
            min-width: 280px;
            /* Mencegah kartu mengecil */
            max-width: 280px;
            /* Mencegah kartu melebar */
            flex: 0 0 auto;
            /* Mencegah flexbox mengubah ukuran kartu */
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            /* Sesuaikan dengan desain */
            border: 2px solid #e0e0e0;
            overflow: hidden;
            box-shadow: none;

            /* Set transform ke nilai awal (agar transisi bekerja) */
            transform: translateY(0);

            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .card-body {
            flex: 1;
            /* Agar body mengisi sisa ruang */
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        /* Tombol View Details (Disamakan dengan Katalog) */
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

        .icon-arrow {
            display: inline-block;
            width: 1.26125rem;
            height: 1.26125rem;
            margin-left: 0.5rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4.5 12h15m0 0l-5.625-6m5.625 6l-5.625 6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        /* Responsive: Di HP jadi 1 kolom, di Tablet 2 kolom */
        @media (max-width: 768px) {
            .card.card-solar {
                width: 100%;
                /* Di HP lebar penuh */
                max-width: 100%;
            }
        }

        .card-solar:hover {
            /* Tambahkan efek scale dan shadow saat di-hover */
            box-shadow:
                0rem 0.0625rem 0.1875rem 0rem rgba(0, 0, 0, 0.26),
                0rem 0.3125rem 0.3125rem 0rem rgba(0, 0, 0, 0.23),
                0rem 0.6875rem 0.4375rem 0rem rgba(0, 0, 0, 0.13),
                0rem 1.25rem 0.5rem 0rem rgba(0, 0, 0, 0.04),
                0rem 1.9375rem 0.5625rem 0rem rgba(0, 0, 0, 0);
            transform: translateY(-5px);
            /* Efek melayang */
        }

        .portfolio-section .project-card-home {
            height: 100%;
            /* Agar semua kartu sama tinggi */
            display: flex;
            flex-direction: column;
            border: none;
            /* Hilangkan border bawaan bootstrap jika ada */
            background: transparent;
        }

        .portfolio-section .project-img {
            height: 250px;
            /* Tinggi gambar tetap */
            width: 100%;
            object-fit: cover;
            /* Gambar tidak gepeng */
            border-radius: 12px;
        }

        .portfolio-section .project-details {
            flex-grow: 1;
            /* Isi ruang kosong agar tombol sejajar di bawah */
            text-align: left;
            /* Rata kiri */
            padding-left: 0;
            /* Hapus padding default UL */
        }

        .portfolio-section .btn-cta-solar {
            /* Tombol proyek pakai style yang sama dengan produk */
            width: 100%;
            /* Lebar penuh */
        }

        .review-card-wrapper {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 2px solid #a0a0a0ff;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .review-profile-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #136000;
            margin-bottom: 15px;
        }

        .review-text {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
            font-style: italic;
        }

        .review-author {
            font-weight: 700;
            color: #136000;
            font-size: 1rem;
        }

        .star-rating {
            color: #FFD700;
            /* Warna Emas */
            font-size: 2rem;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .hero-navbar {
            padding: 0 100px;
        }
    </style>
</head>

<body>
    <div class="beranda">
        <div class="frame-136">
            <div class="frame-138">
                <?php include 'includes/header.php'; ?>
                <img class="head-img" src="..\img\home-img.png" />
            </div>

            <div class="abou-us-container">
                <img class="img-container" src="..\img\home-img-container1.png" />
                <div class="dec-about-us">
                    <div class="heading-container">Who We Are? More Than Just Solar Panels.</div>
                    <div class="dec-container">
                        We are a dedicated team of experts committed to transforming how the region uses energy. Since
                        2015, we have successfully completed 400 projects, ranging from private homes to large
                        commercial complexes. Our mission is simple: to provide efficient, affordable, and sustainable
                        solar energy solutions.
                    </div>
                </div>
            </div>
            <div class="abou-us-container">
                <div class="dec-about-us">
                    <div class="heading-container">Why We Exist? Powering the Future, Together.</div>
                    <div class="dec-container">
                        We believe every roof has the potential to be a source of clean energy. GreenRay doesn't just
                        install panels—we create energy independence for every client. We combine cutting-edge
                        technology with personalized customer service.
                    </div>
                </div>
                <img class="img-container" src="..\img\home-img-container2.png" />
            </div>

            <div class="catalog-container">
                <div class="heading-container-2">
                    <div class="heading-container2">Find the Perfect Solar Solution for Your Needs</div>
                    <div class="dec-container2">
                        Whether you’re a homeowner looking to cut utility costs or a business seeking energy
                        independence, we have a tailored package to fit your budget and capacity requirements.
                    </div>
                </div>

                <div class="card-container">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $prod): ?>
                            <?php
                            $feats = json_decode($prod['key_features_json'], true) ?? [];
                            $display_feats = array_slice($feats, 0, 2);
                            ?>
                            <div class="card card-solar">
                                <img src="../<?php echo htmlspecialchars($prod['image_url']); ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($prod['name']); ?>">

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center fw-bold mb-2">
                                        <?php echo htmlspecialchars($prod['name']); ?>
                                    </h5>
                                    <p class="text-center text-muted mb-3" style="font-size: 0.9em;">
                                        (<?php echo htmlspecialchars($prod['subtitle']); ?>)
                                    </p>

                                    <ul class="custom-bullet-list flex-grow-1">
                                        <?php if (!empty($display_feats)): ?>
                                            <?php foreach ($display_feats as $f): ?>
                                                <li><?php echo htmlspecialchars($f['title']); ?></li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li style="list-style: none;">No features listed</li>
                                        <?php endif; ?>
                                    </ul>

                                    <a href="katalog_detail.php?slug=<?php echo $prod['slug']; ?>"
                                        class="btn btn-cta-solar w-100">
                                        <span>View Details</span>
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted w-100">Belum ada produk.</p>
                    <?php endif; ?>
                </div>

                <div class="cta-home">
                    <a href="katalog.php">
                        <div class="view-more">View More</div>
                    </a>
                </div>
            </div>

            <div class="portfolio-container portfolio-section">
                <div class="heading-container2">Our Portfolio</div>

                <div class="container">
                    <div class="row g-4 justify-content-center">

                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $proj): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="project-card-home">
                                        <img class="project-img rounded-3 mb-4"
                                            src="../<?php echo htmlspecialchars($proj['hero_image_url']); ?>"
                                            alt="<?php echo htmlspecialchars($proj['title']); ?>">

                                        <h3 class="h4 fw-bold mb-3 text-center">
                                            <?php echo htmlspecialchars($proj['title']); ?>
                                        </h3>

                                        <ul class="project-details mb-4" style="list-style: none; padding: 0;">
                                            <li class="mb-2"><b>Capacity:</b>
                                                <?php echo htmlspecialchars($proj['stat_capacity']); ?></li>
                                            <li class="mb-2"><b>Goal:</b>
                                                <?php echo htmlspecialchars($proj['subtitle_goal']); ?></li>
                                            <li class="mb-2">
                                                <b>Details:</b>
                                                <?php
                                                $details = htmlspecialchars($proj['overview_details']);
                                                // Potong teks jika terlalu panjang agar kartu tetap rapi
                                                if (strlen($details) > 100) {
                                                    echo substr($details, 0, 100) . '...';
                                                } else {
                                                    echo $details;
                                                }
                                                ?>
                                            </li>
                                        </ul>

                                        <a href="project_detail.php?slug=<?php echo htmlspecialchars($proj['slug']); ?>"
                                            class="btn btn-cta-solar mt-auto">
                                            <span>View Details</span>
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center text-muted">Belum ada proyek.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="cta-home mt-5">
                    <a href="portofolio.php">
                        <div class="view-more">View More</div>
                    </a>
                </div>
            </div>

            <div class="reviews-bigcontain">
                <div class="heading-container-2">
                    <div class="heading-container2">Real Results, Trusted by Our Customers</div>
                    <div class="dec-container2">Hear firsthand how homeowners are saving money, enhancing their homes,
                        and gaining energy independence.</div>
                </div>
                <div class="review-container">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div class="col-md-4 d-flex">
                                <div class="review-card-wrapper w-100">

                                    <?php
                                    $cleanPath = ltrim(str_replace('../', '', $rev['image_url']), '/');
                                    $photo = !empty($cleanPath) ? '../' . $cleanPath : '../img/default-profile.png';
                                    ?>
                                    <img class="review-profile-img" src="<?php echo htmlspecialchars($photo); ?>" alt="Profile">

                                    <div class="star-rating">
                                        <?php
                                        $rating = (int) $rev['rating'];
                                        for ($i = 0; $i < $rating; $i++)
                                            echo "★";
                                        ?>
                                    </div>

                                    <div class="review-text">
                                        "<?php echo htmlspecialchars($rev['review_text']); ?>"
                                    </div>

                                    <div class="review-author">
                                        <?php echo htmlspecialchars($rev['customer_name']); ?>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center text-muted">No reviews yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="faq-container">
                <div class="title-contain">
                    <div class="title-faq">Frequently Asked Questions</div>
                    <div class="desc-contain">
                        We understand that investing in solar power brings up many questions. Get answers to your
                        most
                        common concerns here.
                    </div>
                </div>
                <div class="question-contain">
                    <?php
                    // FIX 1: Ambil semua FAQ terbaru (ORDER BY id DESC) agar data baru muncul
                    $faqs = [];
                    $res_faq = $koneksi->query("SELECT * FROM faqs ORDER BY order_index ASC");
                    if ($res_faq) {
                        while ($row = $res_faq->fetch_assoc()) {
                            $faqs[] = $row;
                        }
                    }

                    if (!empty($faqs)):
                        ?>
                        <?php foreach ($faqs as $faq): ?>
                            <div class="faq-item">
                                <div class="dropdown-faq">
                                    <div class="text-faq-card"><?php echo htmlspecialchars($faq['question']); ?></div>
                                    <i class="fa-solid fa-chevron-down arrow-icon"></i>
                                </div>
                                <div class="faq-answer-content">
                                    <div class="faq-answer-text"><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Belum ada FAQ.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer">
                <div class="footer-content">
                    <div class="footer-info">
                        <div class="footer-logo-text">
                            <img class="green-ray-logo-12" src="..\img\GreenRay_Logo 1-1.png" />
                            <div class="footer-desc">
                                Powering a cleaner, brighter future for Indonesia. We are your trusted partner in
                                sustainable energy solutions.
                            </div>
                        </div>
                    </div>
                    <div class="copyright">© 2025 GreenRay. All rights reserved.</div>
                </div>
                <div class="footer-menu">
                    <div class="menu-container-footer">
                        <div class="title-footer">Quick Links</div>
                        <div class="dec-container-footer">
                            <div class="list-footer"><a href="home.php">Home</a></div>
                            <div class="list-footer"><a href="portofolio.php">Portfolio</a></div>
                            <div class="list-footer"><a href="calc.php">Saving Calculator</a></div>
                        </div>
                    </div>
                    <div class="menu-container-footer">
                        <div class="title-footer">Get In Touch</div>
                        <div class="dec-container-footer">
                            <div class="list-footer"><a href="contact-us.php">Quick Consultation via WhatsApp</a></div>
                            <div class="list-footer"><a href="contact-us.php">Send a Formal Inquiry Email</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 2. FAQ Logic (DIPERBAIKI)
            const faqItems = document.querySelectorAll('.faq-item');
            const faqHeaders = document.querySelectorAll('.dropdown-faq');

            faqHeaders.forEach(header => {
                header.addEventListener('click', function () {
                    // Ambil item induk dari header yang diklik
                    const currentItem = this.closest('.faq-item');

                    // Cek apakah item ini sedang terbuka
                    const isOpen = currentItem.classList.contains('active');

                    // LANGKAH 1: Tutup SEMUA FAQ terlebih dahulu
                    faqItems.forEach(item => {
                        item.classList.remove('active');
                    });

                    // LANGKAH 2: Jika item yang diklik tadi posisinya tertutup, barulah kita buka
                    if (!isOpen) {
                        currentItem.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>