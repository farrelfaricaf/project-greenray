<section class="hero-section pt-5 mt-5 bg-soft-green position-relative overflow-hidden">
    <div class="container position-relative z-2 py-5">
        <div class="row align-items-center gy-5 min-vh-75">
            <div class="col-lg-6 order-2 order-lg-1 text-center text-lg-start">
                <h1 class="hero-title display-4 fw-bold text-dark mb-3 lh-sm">
                    <?php echo html_entity_decode($lp['hero_title']); ?>
                </h1>
                <p class="hero-desc lead text-secondary mb-4 mx-auto mx-lg-0" style="max-width: 500px;">
                    <?php echo htmlspecialchars($lp['hero_subtitle']); ?>
                </p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    <a href="html/calc.php"
                        class="btn btn-success btn-lg rounded-pill px-5"><?php echo htmlspecialchars($lp['hero_button_primary']); ?></a>
                    <a href="html/katalog.php"
                        class="btn btn-outline-dark btn-lg rounded-pill px-5"><?php echo htmlspecialchars($lp['hero_button_secondary']); ?></a>
                </div>
                <div class="row mt-5 pt-4 g-4 justify-content-start">
                    <div class="col-6 col-sm-3 border-end">
                        <h2 class="fw-bold text-success"><?php echo $lp['stat_1_value']; ?></h2>
                        <small><?php echo $lp['stat_1_label']; ?></small>
                    </div>
                    <div class="col-6 col-sm-3 border-end">
                        <h2 class="fw-bold text-success"><?php echo $lp['stat_2_value']; ?></h2>
                        <small><?php echo $lp['stat_2_label']; ?></small>
                    </div>
                    <div class="col-6 col-sm-3 border-end">
                        <h2 class="fw-bold text-success"><?php echo $lp['stat_3_value']; ?></h2>
                        <small><?php echo $lp['stat_3_label']; ?></small>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="fw-bold text-success"><?php echo $lp['stat_4_value']; ?></h2>
                        <small><?php echo $lp['stat_4_label']; ?></small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
                <div class="hero-image-wrapper position-relative">
                    <img src="<?php echo getImg($lp['hero_image'], 'img/image-couple.png'); ?>" alt="Happy Family with Solar"
                        class="img-fluid hero-main-img rounded-4 shadow-lg position-relative z-2" />

                    <div class="position-absolute bottom-0 start-0 mb-4 ms-n4 bg-white p-3 rounded-4 shadow-lg d-flex align-items-center gap-3 animate-float z-3"
                        style="max-width: 200px; text-align: left">
                        <div class="bg-success text-white rounded-circle p-2 d-flex">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
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