<?php
// Dapatkan nama file halaman saat ini (misal: 'products.php')
$page = basename($_SERVER['PHP_SELF']);
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Utama</div>
                <a class="nav-link <?php echo ($page == 'index.php') ? 'active' : ''; ?>" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Manajemen Konten</div>
                <a class="nav-link <?php echo ($page == 'projects.php' || $page == 'project_add.php' || $page == 'project_edit.php' || $page == 'project_gallery.php') ? 'active' : ''; ?>"
                    href="projects.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                    Proyek
                </a>
                <a class="nav-link <?php echo ($page == 'products.php' || $page == 'products_add.php' || $page == 'products_edit.php' || $page == 'product_gallery.php') ? 'active' : ''; ?>"
                    href="products.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-solar-panel"></i></div>
                    Produk
                </a>
                <a class="nav-link <?php echo ($page == 'clients.php' || $page == 'clients_add.php' || $page == 'clients_edit.php') ? 'active' : ''; ?>"
                    href="clients.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                    Klien
                </a>
                <a class="nav-link <?php echo ($page == 'reviews.php' || $page == 'reviews_add.php' || $page == 'reviews_edit.php') ? 'active' : ''; ?>" href="reviews.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                    Reviews
                </a>
                <a class="nav-link <?php echo ($page == 'faqs.php' || $page == 'faqs_add.php' || $page == 'faqs_edit.php') ? 'active' : ''; ?>" href="faqs.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-question-circle"></i></div>
                    FAQ
                </a>

                <div class="sb-sidenav-menu-heading">Interaksi User</div>
                <a class="nav-link <?php echo ($page == 'consultations.php' || $page == 'consultations_edit.php' || $page == 'consultations_view.php') ? 'active' : ''; ?>"
                    href="consultations.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calculator"></i></div>
                    Konsultasi (Leads)
                </a>
                <a class="nav-link <?php echo ($page == 'contact_messages.php' || $page == 'message_view.php') ? 'active' : ''; ?>"
                    href="contact_messages.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                    Pesan Kontak
                </a>

                <div class="sb-sidenav-menu-heading">Pengaturan Sistem</div>
                <a class="nav-link <?php echo ($page == 'users.php' || $page == 'user_edit.php' || $page == 'user_reset_password.php') ? 'active' : ''; ?>" href="users.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Users
                </a>
                <a class="nav-link <?php echo ($page == 'admins.php') ? 'active' : ''; ?>" href="admins.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                    Administrator
                </a>

                <?php
                $is_calc_active = ($page == 'locations.php' || $page == 'locations_add.php' || $page == 'locations_edit.php' || $page == 'tariffs.php' || $page == 'tariffs_add.php' || $page == 'tariffs_edit.php');
                ?>
                <a class="nav-link <?php echo $is_calc_active ? '' : 'collapsed'; ?>" href="#" data-bs-toggle="collapse"
                    data-bs-target="#collapseKalkulator"
                    aria-expanded="<?php echo $is_calc_active ? 'true' : 'false'; ?>"
                    aria-controls="collapseKalkulator">
                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                    Setting Kalkulator
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php echo $is_calc_active ? 'show' : ''; ?>" id="collapseKalkulator"
                    aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?php echo ($page == 'locations.php' || $page == 'locations_add.php' || $page == 'locations_edit.php') ? 'active' : ''; ?>"
                            href="locations.php">Manajemen Lokasi</a>
                        <a class="nav-link <?php echo ($page == 'tariffs.php' || $page == 'tariffs_add.php' || $page == 'tariffs_edit.php') ? 'active' : ''; ?>"
                            href="tariffs.php">Manajemen Tarif</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
        </div>
    </nav>
</div>