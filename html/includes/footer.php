<?php

if (!isset($koneksi)) {

    if (file_exists('../koneksi.php'))
        include_once '../koneksi.php';
    elseif (file_exists('../../koneksi.php'))
        include_once '../../koneksi.php';
}


$res_f = $koneksi->query("SELECT * FROM footer_settings WHERE id = 1");
$f_data = ($res_f && $res_f->num_rows > 0) ? $res_f->fetch_assoc() : [];


$footer_links = [];
$res_l = $koneksi->query("SELECT * FROM footer_links WHERE section='quick_links' ORDER BY id ASC");
if ($res_l) {
    while ($row = $res_l->fetch_assoc()) {
        $footer_links[] = $row;
    }
}



$link_chunks = array_chunk($footer_links, 4);


$base_url_footer = (basename($_SERVER['PHP_SELF']) == 'index.php') ? '' : '../';
$img_path_footer = (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'img/GreenRay_Logo 1-1.png' : '../img/GreenRay_Logo 1-1.png';

function cleanLink($url, $base)
{
    if ($url == '#' || strpos($url, 'http') === 0 || strpos($url, 'mailto') === 0)
        return $url;
    if ($base == '')
        return $url;
    return '../' . str_replace('../', '', $url);
}
?>

<footer class="py-5 px-5 border-top mt-5 mb-5 bg-white">
    <div class="container">
        <div class="row gy-5 justify-content-between">

            <div class="col-lg-3 col-md-6">
                <a href="<?php echo $base_url_footer; ?>index.php" class="d-block mb-3">
                    <img src="<?php echo $img_path_footer; ?>" alt="GreenRay" style="height: 40px; width: auto;">
                </a>
                <p class="text-secondary small mb-5" style="line-height: 1.6;">
                    <?php echo htmlspecialchars($f_data['description'] ?? ''); ?>
                </p>
                <p class="small text-muted mt-4 mb-0">&copy;
                    <?php echo htmlspecialchars($f_data['copyright_text'] ?? ''); ?>
                </p>
            </div>

            <div class="col-lg-5 col-md-12 offset-lg-1">
                <h5 class="fw-bold text-dark mb-4">Quick Links</h5>

                <div class="d-flex flex-wrap gap-5">

                    <?php if (!empty($link_chunks)): ?>
                        <?php foreach ($link_chunks as $chunk): ?>
                            <ul class="list-unstyled d-flex flex-column gap-3" style="min-width: 140px;">
                                <?php foreach ($chunk as $link): ?>
                                    <li>
                                        <a href="<?php echo cleanLink($link['url'], $base_url_footer); ?>"
                                            class="text-decoration-none text-secondary link-hover">
                                            <?php echo htmlspecialchars($link['label']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">Belum ada link.</p>
                    <?php endif; ?>

                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5 class="fw-bold text-dark mb-4">Get In Touch</h5>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <li>
                        <a href="<?php echo cleanLink($f_data['whatsapp_url'] ?? '#', $base_url_footer); ?>"
                            class="text-decoration-none text-secondary link-hover d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success d-flex align-items-center justify-content-center"
                                style="width: 35px; height: 35px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                    </path>
                                </svg>
                            </div>
                            <span class="small fw-bold">WhatsApp Support</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo cleanLink($f_data['email_url'] ?? '#', $base_url_footer); ?>"
                            class="text-decoration-none text-secondary link-hover d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success d-flex align-items-center justify-content-center"
                                style="width: 35px; height: 35px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                    </path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <span class="small fw-bold">Email Inquiry</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</footer>

<style>
    .link-hover {
        transition: all 0.3s ease;
    }

    .link-hover:hover {
        color: #136000 !important;
        padding-left: 5px;
    }
</style>