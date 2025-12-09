<?php
// Ambil nama file saat ini
$page = basename($_SERVER['PHP_SELF']);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? 'User';
$profile_pic = $_SESSION['user_profile_pic'] ?? 'img/default-profile.png';
?>

<head>
    <script src="https://use.fontawesome.com/releases/v7.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<style>
    .header-wrapper {
        padding: 0rem 6.25rem 0rem 6.25rem;
        display: flex;
        flex-direction: column;
        gap: 4.0rem;
        align-items: center;
        justify-content: flex-start;
        max-width: 90rem;
        width: 100%;
        margin: 3.125rem auto 0 auto;
        position: relative;
        z-index: 30;
    }

    .master-header-wrapper {
        /* Margin Kiri/Kanan 6.25rem */
        display: flex;
        flex-direction: column;
        gap: 4.0rem;
        align-items: center;
        justify-content: flex-start;
        max-width: 90rem;
        width: 100%;
        position: relative;
        z-index: 30;
    }

    .hero-navbar {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        /* Lebar maksimum agar tidak terlalu lebar di layar besar */
        max-width: 1440px;
        width: 100%;
        margin: 0 auto;
        /* Tengah */
        box-sizing: border-box;
    }

    .green-ray-logo-nav {
        width: 180px;
        /* Ukuran logo tetap */
        height: auto;
        object-fit: contain;
    }

    .header-menu-nav {
        display: flex;
        gap: 2.5rem;
        align-items: center;
    }

    .nav-link-item {
        text-decoration: none;
        color: #000;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.11rem;
        font-weight: 500;
        transition: color 0.2s;
    }

    .nav-link-item:hover,
    .nav-link-item.active-head {
        font-weight: 700;
        color: #136000;
    }

    .header-actions-nav {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    /* Tombol Login & Contact */
    .btn-nav-login,
    .btn-nav-contact {
        border-radius: 50px;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-nav-login {
        background: #136000;
        color: white;
        border: none;
    }

    .btn-nav-contact {
        background: white;
        border: 1px solid black;
        color: black;
    }

    .btn-nav-login:hover,
    .btn-nav-contact:hover {
        transform: scale(1.05);
    }

    /* Responsive: Sembunyikan menu di HP */
    @media (max-width: 992px) {
        .header-menu-nav {
            display: none;
        }
    }
</style>

<div class="master-header-wrapper">
    <div class="hero-navbar">
        <a href="html/home.php">
            <img class="green-ray-logo-nav" src="img/GreenRay_Logo 1-1.png" alt="GreenRay Logo" />
        </a>

        <div class="header-menu-nav">
            <a href="html/home.php" class="nav-link-item <?php echo ($page == 'home.php') ? 'active-head' : ''; ?>">Home</a>
            <a href="html/portofolio.php"
                class="nav-link-item <?php echo ($page == 'portofolio.php' || $page == 'project_detail.php') ? 'active-head' : ''; ?>">Portfolio</a>
            <a href="html/calc.php"
                class="nav-link-item <?php echo ($page == 'calc.php') ? 'active-head' : ''; ?>">Calculator</a>
            <a href="html/katalog.php"
                class="nav-link-item <?php echo ($page == 'katalog.php' || $page == 'katalog_detail.php') ? 'active-head' : ''; ?>">Catalog</a>
        </div>

        <div class="header-actions-nav">
            <?php if ($is_logged_in): ?>
                <div class="profile-dropdown">
                    <a href="#" class="profile-toggle" id="profileToggle">
                        <img src="<?php echo fixPath($profile_pic); ?>" alt="Profil"
                            class="profile-picture-header"
                            style="width:45px; height:45px; border-radius:50%; object-fit:cover; border:2px solid #136000;">
                    </a>
                    <div class="dropdown-menu-header" id="profileDropdownMenu">
                        <div class="dropdown-item-info">Halo, <strong><?php echo htmlspecialchars($user_name); ?></strong>!
                        </div>
                        <a class="dropdown-item" href="../projectgreenray/html/profile.php"><span class="fa-regular fa-user me-2"></span> Profil
                            Saya</a>
                        <a class="dropdown-item" href="../projectgreenray/html/contact-us.php"><i class="fa-solid fa-address-card me-2"></i> Bantuan
                            / Kontak</a>
                        <a class="dropdown-item" href="../projectgreenray/html/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>
                            Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn-nav-login p-3" href="html/signin.php">
                    Login
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                </a>
                <a class="btn-nav-contact p-3" href="html/contact-us.php">
                    Contact Us
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="../../node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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