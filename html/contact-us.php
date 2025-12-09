<?php
session_start();
include '../koneksi.php'; 


$stmtContact = $koneksi->prepare("SELECT * FROM contact_settings WHERE id = 1");
$stmtContact->execute();
$contact = $stmtContact->get_result()->fetch_assoc();

if (!$contact) {
  $contact = [
    'header_title' => 'Contact Us',
    'header_subtitle' => '',
    'address' => '',
    'phone' => '',
    'whatsapp' => '',
    'email' => '',
    'office_hours' => '',
    'map_embed' => ''
  ];
}


$alert_message = ""; 
$show_modal = false;   




$is_logged_in = isset($_SESSION['user_id']);
$user_name = '';
$profile_pic = '../img/default-profile.png'; 

if ($is_logged_in) {
  $user_name = $_SESSION['user_name'] ?? 'User';
  $profile_pic = $_SESSION['user_profile_pic'] ?? '../img/default-profile.png';
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {

  
  $full_name = trim($_POST['fullName']);
  $email = trim($_POST['emailAddress']);
  $subject = trim($_POST['subject']);
  $message = trim($_POST['message']);

  
  if (empty($full_name) || empty($email) || empty($subject) || empty($message)) {
    $alert_message = '<div class="alert alert-danger" role="alert">Error: Semua field wajib diisi.</div>';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $alert_message = '<div class="alert alert-danger" role="alert">Error: Alamat email tidak valid.</div>';
  } else {

    
    $sql = "INSERT INTO contact_messages (full_name, email, subject, message) VALUES (?, ?, ?, ?)";

    if ($stmt = $koneksi->prepare($sql)) {
      $stmt->bind_param("ssss", $full_name, $email, $subject, $message);

      
      if ($stmt->execute()) {
        $show_modal = true;
      } else {
        $alert_message = '<div class="alert alert-danger" role="alert">Error: Gagal mengirim pesan. ' . $stmt->error . '</div>';
      }
      $stmt->close();
    } else {
      $alert_message = '<div class="alert alert-danger" role="alert">Error: Gagal menyiapkan query. ' . $koneksi->error . '</div>';
    }
    
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="..\css\cs.css">
  <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">

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

    <div class="container main-content-area">
      <div class="row contact-row">

        <div class="col-lg-6 col-md-10 contact-form-column">
          <div class="contact-header mb-5">
            <div class="contact-form-title"><?php echo htmlspecialchars($contact['header_title']); ?></div>
            <div class="contact-description">
              <?php echo nl2br(htmlspecialchars($contact['header_subtitle'])); ?>
            </div>
          </div>

          <?php if (!empty($alert_message)) {
            echo $alert_message;
          } ?>

          <form class="needs-validation" novalidate id="contactForm" action="contact-us.php" method="POST">
            <div class="mb-4">
              <label for="fullName" class="form-label fw-bold">Full Name</label>
              <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Your full name"
                required>
              <div class="invalid-feedback" id="fullNameFeedback">
                Please enter your full name.
              </div>
            </div>

            <div class="mb-4">
              <label for="emailAddress" class="form-label fw-bold">Email Address</label>
              <input type="email" class="form-control" id="emailAddress" name="emailAddress"
                placeholder="example@email.com" required>
              <div class="invalid-feedback">
                Please enter a valid email address.
              </div>
            </div>

            <div class="mb-4">
              <label for="subject" class="form-label fw-bold">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject"
                placeholder="e.g., Question about installation" required>
              <div class="invalid-feedback">
                Please enter a subject.
              </div>
            </div>

            <div class="mb-4">
              <label for="message" class="form-label fw-bold">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5"
                placeholder="Write your message here..." required></textarea>
              <div class="invalid-feedback">
                Please write your message.
              </div>
            </div>

            <div class="d-grid mb-4 text-center">
              <button type="submit" class="btn btn-success btn-lg">Send Message <i
                  class="fa-regular fa-paper-plane ms-3"></i></button>
            </div>

            <div class="text-center my-4 contact-separator d-lg-none">or</div>
          </form>
        </div>

        <div class="col-lg-4 d-none d-lg-block pt-5 mt-5">
          <div class="d-grid gap-3 cta-sidebar">
            <h4 class="fw-bold mb-3">Contact Information</h4>
            <?php if (!empty($contact['map_embed'])): ?>
              <div class="mt-4">
                <h4 class="fw-bold mb-3">Find Us on the Map</h4>
                <div class="ratio ratio-16x9">
                  <?php echo $contact['map_embed']; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($contact['address'])): ?>
              <div class="mb-2">
                <strong>Office Address</strong><br>
                <?php echo nl2br(htmlspecialchars($contact['address'])); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($contact['phone'])): ?>
              <div class="mb-2">
                <strong>Phone</strong><br>
                <?php echo htmlspecialchars($contact['phone']); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($contact['email'])): ?>
              <div class="mb-2">
                <strong>Email</strong><br>
                <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>">
                  <?php echo htmlspecialchars($contact['email']); ?>
                </a>
              </div>
            <?php endif; ?>

            <?php if (!empty($contact['office_hours'])): ?>
              <div class="mb-3">
                <strong>Office Hours</strong><br>
                <?php echo htmlspecialchars($contact['office_hours']); ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($contact['whatsapp'])): ?>
              <a href="<?php echo htmlspecialchars($contact['whatsapp']); ?>?text=<?php echo urlencode('Halo GreenRay, ...'); ?>"
                class="btn btn-primary contact-wa-btn" target="_blank">
                <span class="mdi--whatsapp"></span>
                Contact us via WhatsApp
              </a>
            <?php endif; ?>


            <?php if (!empty($contact['email'])): ?>
              <a href="#" id="email-cta-btn" class="btn btn-primary contact-email-btn" target="_blank">
                <span class="ic--outline-email"></span>
                Contact us via Email
              </a>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>

    <?php include 'includes/footer.php'; ?>
  </div>

  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center p-5">
          <svg xmlns="https://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="#136000"
            class="mb-4">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2zm-2 15l-4-4l1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
          </svg>
          <h2 class="modal-title mb-3 fw-bold" id="successModalLabel" style="color: #136000;">Message Sent Successfully!
          </h2>
          <p class="lead">Thank you for reaching out to GreenRay. We have received your inquiry and will respond as
            quickly as possible.</p>
          <p class="text-muted mb-4">We usually reply within 1-2 business days. Please check your email for our
            response.</p>
          <button type="button" class="btn btn-success btn-lg" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

  <script>
    function capitalizeFirstLetter(inputElement) {
      let value = inputElement.value.toLowerCase().trim();
      if (value) {
        value = value.split(' ')
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');
        inputElement.value = value;
      }
    }
    function validateFullName(inputElement) {
      const value = inputElement.value;
      const invalidFeedback = document.getElementById('fullNameFeedback');
      const validNameRegex = /^[a-zA-Z\s]*$/;
      if (!validNameRegex.test(value)) {
        inputElement.setCustomValidity("Full name must contain only letters and spaces.");
        invalidFeedback.textContent = "Full Name tidak boleh mengandung angka atau simbol.";
      } else if (value.trim() === "") {
        inputElement.setCustomValidity("Please enter your full name.");
        invalidFeedback.textContent = "Please enter your full name.";
      } else {
        inputElement.setCustomValidity("");
      }
      const form = inputElement.closest('form');
      if (form.classList.contains('was-validated') || value.length > 0) {
        if (inputElement.checkValidity()) {
          inputElement.classList.remove('is-invalid');
          inputElement.classList.add('is-valid');
        } else {
          inputElement.classList.remove('is-valid');
          inputElement.classList.add('is-invalid');
        }
      }
    }
    function urlEncode(str) {
      return encodeURIComponent(str).replace(/[!'()*]/g, function (c) {
        return '%' + c.charCodeAt(0).toString(16);
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      const emailBtn = document.getElementById('email-cta-btn');
      const fullNameInput = document.getElementById('fullName');

      if (fullNameInput) {
        fullNameInput.addEventListener('input', () => validateFullName(fullNameInput));
        fullNameInput.addEventListener('blur', () => {
          capitalizeFirstLetter(fullNameInput);
          validateFullName(fullNameInput);
        });
      }

      const recipient = "<?php echo htmlspecialchars($contact['email'] ?: 'info@greenray.com'); ?>";
      const subject = "Pertanyaan Mengenai Potensi Instalasi Panel Surya";
      const bodyTemplate = "Halo Tim GreenRay,\n\nNama saya: [NAMA ANDA]\nNomor Telepon: [NOMOR TELEPON ANDA]\n\nSaya ingin menanyakan tentang ...";
      const encodedBody = urlEncode(bodyTemplate).replace(/%0A/g, '%0D%0A');
      const gmailLink = `https://mail.google.com/mail/?view=cm&fs=1&to=${urlEncode(recipient)}&su=${urlEncode(subject)}&body=${encodedBody}`;
      if (emailBtn) {
        emailBtn.addEventListener('click', function (e) {
          e.preventDefault();
          window.open(gmailLink, '_blank');
        });
      }


    });

    
    (function () {
      'use strict'
      const form = document.getElementById('contactForm');
      if (form) {
        form.addEventListener('submit', function (event) {
          validateFullName(document.getElementById('fullName'));
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false)
      }
    })();
  </script>

  <?php if ($show_modal): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        document.getElementById('contactForm').reset();
        document.getElementById('contactForm').classList.remove('was-validated');
      });
    </script>
  <?php endif; ?>

</body>

</html>