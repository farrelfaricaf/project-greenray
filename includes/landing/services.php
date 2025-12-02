<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="flip-card-wrapper">
                    <div class="flip-card-inner">
                        <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm">
                            <img src="<?php echo getImg($lp['serv_1_image'], 'img/image 7.png'); ?>" class="w-100"
                                style="height: 200px; object-fit: cover;">
                            <div class="p-4 text-center">
                                <h5 class="fw-bold"><?php echo $lp['serv_1_title']; ?></h5>
                                <p class="text-muted"><?php echo $lp['serv_1_desc']; ?></p>
                                <button class="btn btn-outline-success rounded-pill btn-trigger-flip">Info</button>
                            </div>
                        </div>
                        <div
                            class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                            <p class="text-center"><?php echo $lp['serv_1_back_desc']; ?></p>
                            <a href="html/katalog.php" class="btn btn-light fw-bold rounded-pill mb-2">Catalog</a>
                            <button class="btn btn-outline-light btn-sm rounded-pill btn-trigger-flip">Back</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="flip-card-wrapper">
                    <div class="flip-card-inner">
                        <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm">
                            <img src="<?php echo getImg($lp['serv_2_image'], 'img/image 8.png'); ?>" class="w-100"
                                style="height: 200px; object-fit: cover;">
                            <div class="p-4 text-center">
                                <h5 class="fw-bold"><?php echo $lp['serv_2_title']; ?></h5>
                                <p class="text-muted"><?php echo $lp['serv_2_desc']; ?></p>
                                <button class="btn btn-outline-success rounded-pill btn-trigger-flip">Info</button>
                            </div>
                        </div>
                        <div
                            class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                            <p class="text-center"><?php echo $lp['serv_2_back_desc']; ?></p>
                            <a href="html/portofolio.php" class="btn btn-light fw-bold rounded-pill mb-2">Portfolio</a>
                            <button class="btn btn-outline-light btn-sm rounded-pill btn-trigger-flip">Back</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="flip-card-wrapper">
                    <div class="flip-card-inner">
                        <div class="flip-card-front bg-white border rounded-4 overflow-hidden shadow-sm">
                            <img src="<?php echo getImg($lp['serv_3_image'], 'img/image 9.png'); ?>" class="w-100"
                                style="height: 200px; object-fit: cover;">
                            <div class="p-4 text-center">
                                <h5 class="fw-bold"><?php echo $lp['serv_3_title']; ?></h5>
                                <p class="text-muted"><?php echo $lp['serv_3_desc']; ?></p>
                                <button class="btn btn-outline-success rounded-pill btn-trigger-flip">Info</button>
                            </div>
                        </div>
                        <div
                            class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                            <p class="text-center"><?php echo $lp['serv_3_back_desc']; ?></p>
                            <a href="html/contact-us.php" class="btn btn-light fw-bold rounded-pill mb-2">Contact</a>
                            <button class="btn btn-outline-light btn-sm rounded-pill btn-trigger-flip">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>