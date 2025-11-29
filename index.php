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
    /* Efek Hover Kartu Naik */
    .hover-lift {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
      transform: translateY(-8px);
      box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.125) !important;
    }

    /* Efek Zoom Gambar saat Hover */
    .service-card:hover .transition-transform {
      transform: scale(1.05);
      transition: transform 0.5s ease;
    }

    /* Panah Geser saat Hover */
    .service-card:hover .group-hover-arrow svg {
      transform: translateX(5px);
    }

    /* Helper object-fit */
    .object-fit-cover {
      object-fit: cover;
    }

    .flip-card-wrapper {
      background-color: transparent;
      width: 100%;
      height: 350px;
      /* Tinggi tetap agar konsisten */
      perspective: 1000px;
      /* Efek 3D */
    }

    .flip-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      text-align: center;
      transition: transform 0.8s;
      transform-style: preserve-3d;
    }

    /* Class ini yang akan ditambahkan oleh JS */
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

    /* Sisi Belakang harus diputar dulu 180 derajat */
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
        <?php include 'includes/header_ldpage.php'; ?>
      </div>

      <section class="hero-section ps-5 pt-5 mt-5 mb-5 bg-soft-green position-relative overflow-hidden">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="container position-relative z-2">
          <div class="row align-items-center gy-5 min-vh-75">
            <div class="col-lg-6 order-2 order-lg-1">
              <div class="hero-content pe-lg-5">
                <div
                  class="badge bg-white text-success shadow-sm px-3 py-2 rounded-pill mb-3 fw-bold border border-success-subtle">
                  🌱 #1 Solar Energy Solution
                </div>
                <h1 class="hero-title display-4 fw-bold text-dark mb-3">
                  Power Your Future,<br />
                  <span class="text-success">Brighten Your World.</span>
                </h1>
                <p class="hero-desc lead text-secondary mb-4">
                  Switch to clean energy with GreenRay. Seamless solar panel
                  installation for homes and businesses across Indonesia.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                  <a href="html/calc.php" class="btn btn-success btn-lg rounded-pill px-4 py-3 fw-bold shadow-success">
                    Calculate Savings
                  </a>
                  <a href="html/katalog.php"
                    class="btn btn-white btn-lg rounded-pill px-4 py-3 fw-bold border shadow-sm">
                    Explore Catalog
                  </a>
                </div>

                <div class="row mt-5 pt-4 g-4 stats-row border-top border-success-subtle">
                  <div class="col-4">
                    <h3 class="fw-bold mb-0 text-dark">200+</h3>
                    <small class="text-muted fw-medium">Projects</small>
                  </div>
                  <div class="col-4 border-start border-success-subtle ps-4">
                    <h3 class="fw-bold mb-0 text-dark">300+</h3>
                    <small class="text-muted fw-medium">Clients</small>
                  </div>
                  <div class="col-4 border-start border-success-subtle ps-4">
                    <h3 class="fw-bold mb-0 text-dark">50+</h3>
                    <small class="text-muted fw-medium">Cities</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6 order-1 order-lg-2 text-center">
              <div class="hero-image-wrapper position-relative">
                <img src="img/mariana-proenca-GXiHwHkIdVs-unsplash.jpg" alt="Happy Family with Solar"
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
                    xmlns="http://www.w3.org/2000/svg">
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
                    <h4 class="fw-bold text-dark mb-2">Save on Bills</h4>
                    <p class="text-secondary mb-4">Drastically reduce your monthly expenses.</p>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-danger text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">Maximum Savings</h4>
                    <p class="mb-4 lh-base">
                      Our solar systems are designed to maximize output and minimize loss, giving you the best ROI and
                      reducing your electricity bills by up to 60%.
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
                    <h4 class="fw-bold text-dark mb-2">Energy Independence</h4>
                    <p class="text-secondary mb-4">Protect yourself from rising costs.</p>
                    <button type="button" class="btn btn-outline-warning rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-warning text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">Be Independent</h4>
                    <p class="mb-4 lh-base">
                      Less reliance on the public grid. Protect yourself from rising energy costs and unexpected power
                      outages with your own power source.
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
                    <h4 class="fw-bold text-dark mb-2">Eco-Friendly</h4>
                    <p class="text-secondary mb-4">Reduce your carbon footprint.</p>
                    <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                      More
                    </button>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">Go Green</h4>
                    <p class="mb-4 lh-base">
                      Switching to solar is one of the most impactful ways to contribute to a cleaner earth. Save the
                      planet while saving money.
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

      <section class="portfolio-section px-5 py-5 bg-light-custom">
        <div class="container">
          <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
              <h2 class="section-title display-5 fw-bold text-dark mb-2">Recent Projects</h2>
              <p class="text-secondary mb-0">See how we help our clients achieve energy independence.</p>
            </div>
            <a href="html/portofolio.php"
              class="btn btn-outline-success rounded-pill px-4 d-none d-md-inline-block">View All Projects</a>
          </div>

          <div class="row g-4">
            <div class="col-md-4">
              <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift">
                <img src="img/rumah.jpg" class="card-img-top object-fit-cover" alt="Surabaya Home"
                  style="height: 250px;">
                <div class="card-body p-4">
                  <div class="badge bg-success bg-opacity-10 text-success mb-2">Residential</div>
                  <h5 class="card-title fw-bold">Surabaya Smart Home</h5>
                  <p class="card-text text-muted small mb-3">Capacity: 5 kWp • Bill Reduction: 60%</p>
                  <a href="html/portofolio.php" class="stretched-link text-decoration-none fw-bold text-success">View
                    Details &rarr;</a>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift">
                <img src="img/toko.jpg" class="card-img-top object-fit-cover" alt="Bandung Store"
                  style="height: 250px;">
                <div class="card-body p-4">
                  <div class="badge bg-primary bg-opacity-10 text-primary mb-2">Commercial</div>
                  <h5 class="card-title fw-bold">Bandung Retail Store</h5>
                  <p class="card-text text-muted small mb-3">Capacity: 20 kWp • ROI: 3.5 Years</p>
                  <a href="html/portofolio.php" class="stretched-link text-decoration-none fw-bold text-success">View
                    Details &rarr;</a>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-lift">
                <img src="img/industri.jpg" class="card-img-top object-fit-cover" alt="Semarang Factory"
                  style="height: 250px;">
                <div class="card-body p-4">
                  <div class="badge bg-warning bg-opacity-10 text-warning mb-2">Industrial</div>
                  <h5 class="card-title fw-bold">Semarang Textile Factory</h5>
                  <p class="card-text text-muted small mb-3">Capacity: 200 kWp • CO2 Saved: 120 Tons</p>
                  <a href="html/portofolio.php" class="stretched-link text-decoration-none fw-bold text-success">View
                    Details &rarr;</a>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center mt-4 d-md-none">
            <a href="html/portofolio.php" class="btn btn-outline-success rounded-pill px-4 w-100">View All Projects</a>
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
                      <img src="img/image 7.png" class="img-fluid w-100 h-100 object-fit-cover" alt="Residential">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0">Residential Installation</h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3">Perfect for homeowners looking to save money.</p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">For Your Home</h4>
                    <p class="mb-4 text-center">
                      Increase your property value and embrace sustainable living with our residential solar packages.
                      We handle everything from roof assessment to grid connection.
                    </p>
                    <a href="html/katalog.php" class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      Go to Catalog
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      &larr; Back
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
                      <img src="img/image 8.png" class="img-fluid w-100 h-100 object-fit-cover" alt="Commercial">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0">Commercial Projects</h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3">Scale up your business sustainability.</p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">For Business</h4>
                    <p class="mb-4 text-center">
                      Reduce operational costs significantly. Our commercial solutions are scalable and designed to
                      maximize ROI for factories, offices, and retail spaces.
                    </p>
                    <a href="html/portofolio.php" class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      View Portfolio
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      &larr; Back
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
                      <img src="img/image 9.png" class="img-fluid w-100 h-100 object-fit-cover" alt="Maintenance">
                      <div class="overlay-gradient position-absolute bottom-0 w-100"
                        style="height: 50%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                      <div class="position-absolute bottom-0 start-0 p-3 text-white z-2">
                        <h4 class="fw-bold mb-0">Maintenance & Support</h4>
                      </div>
                    </div>
                    <div class="p-4 text-center">
                      <p class="text-muted mb-3">Ensure peak efficiency for decades.</p>
                      <button type="button" class="btn btn-outline-success rounded-pill px-4 btn-trigger-flip">
                        More
                      </button>
                    </div>
                  </div>

                  <div
                    class="flip-card-back bg-success text-white rounded-4 p-4 d-flex flex-column align-items-center justify-content-center">
                    <h4 class="fw-bold mb-3">Long-term Support</h4>
                    <p class="mb-4 text-center">
                      We provide regular check-ups, cleaning services, and system monitoring to ensure your solar
                      investment keeps performing at 100%.
                    </p>
                    <a href="html/contact-us.php" class="btn btn-light fw-bold rounded-pill px-4 mb-3">
                      Contact Support
                    </a>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 btn-trigger-flip">
                      &larr; Back
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
                background-image: url('img/image 10.png');
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
              <p class="fs-5 mb-4">
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

    <div class="footer">
      <div class="footer-content">
        <div class="footer-info">
          <div class="footer-logo-text">
            <img class="green-ray-logo-12" src=".\img\GreenRay_Logo 1-1.png" />
            <div class="footer-desc">
              Powering a cleaner, brighter future for Indonesia. We are your
              trusted partner in sustainable energy solutions, built on
              transparency and long-term value.
            </div>
          </div>
        </div>
        <div class="copyright">© 2025 GreenRay. All rights reserved.</div>
      </div>
      <div class="footer-menu">
        <div class="menu-container-footer">
          <div class="title-footer">Quick Links</div>
          <div class="dec-container-footer">
            <div class="list-footer"><a href=".\html\home.html">Home</a></div>
            <div class="list-footer">
              <a href=".\html\portofolio.html">Our Portfolio</a>
            </div>
            <div class="list-footer">
              <a href=".\html\calc.html">Saving Calculator</a>
            </div>
          </div>
        </div>
        <div class="menu-container-footer">
          <div class="title-footer">Get In Touch</div>
          <div class="dec-container-footer">
            <div class="list-footer">
              <a href=".\html\contact-us.html">Quick Consultation via WhatsApp</a>
            </div>
            <div class="list-footer">
              <a href=".\html\contact-us.html">Send a Formal Inquiry Email</a>
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
      // Pilih semua tombol pemicu
      const buttons = document.querySelectorAll('.btn-trigger-flip');

      buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault(); // Mencegah reload jika ada href

          // Cari elemen pembungkus kartu terdekat
          const cardWrapper = this.closest('.flip-card-wrapper');

          // Toggle class 'flipped'
          if (cardWrapper) {
            cardWrapper.classList.toggle('flipped');
          }
        });
      });
    });
  </script>
</body>

</html>