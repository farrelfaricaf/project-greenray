<section class="py-5">
    <div class="container">
        <div class="cta-box text-center text-white rounded-5 p-5"
            style="background-image: url('<?php echo getImg($lp['cta_image'], 'img/image 10.png'); ?>'); background-size: cover; position: relative;">
            <div
                style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%; border-radius: 30px;">
            </div>
            <div class="position-relative z-2">
                <h2 class="fw-bold display-5"><?php echo $lp['cta_title']; ?></h2>
                <p class="fs-5"><?php echo $lp['cta_desc']; ?></p>
                <a href="html/contact-us.php" class="btn btn-success btn-lg rounded-pill mt-3">Get Started Now</a>
            </div>
        </div>
    </div>
</section>