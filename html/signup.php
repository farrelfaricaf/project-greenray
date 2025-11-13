<?php
// WAJIB ADA DI BARIS PALING ATAS
session_start();

// 1. Hubungkan ke database
include '../koneksi.php';

$error_message = "";
$success_message = "";

// 2. Cek jika form sudah di-submit (REQUEST_METHOD == "POST")
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validasi: Pastikan semua data ada sebelum diakses
  if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 3. Validasi Sederhana
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
      $error_message = "Semua field wajib diisi.";
    } elseif (strlen($password) < 8) {
      $error_message = "Password harus minimal 8 karakter.";
    } elseif ($password !== $confirm_password) {
      $error_message = "Password dan Konfirmasi Password tidak cocok.";
    } else {

      // 4. HASH PASSWORD (Sangat Penting untuk Keamanan)
      $hashed_password = password_hash($password, PASSWORD_BCRYPT);

      // 5. Simpan ke database (Gunakan prepared statement)
      $stmt = $koneksi->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

      if ($stmt->execute()) {
        // *** INI ADALAH LOGIKA "PILIHAN A" ***
        $success_message = "Registrasi berhasil! Silakan <a href='signin.php' class='alert-link'>login di sini</a>.";
      } else {
        if ($koneksi->errno == 1062) { // Error email duplikat
          $error_message = "Email ini sudah terdaftar. Silakan gunakan email lain.";
        } else {
          $error_message = "Terjadi kesalahan: " . $stmt->error;
        }
      }
      $stmt->close();
    }
  } else {
    $error_message = "Form tidak terkirim dengan benar.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="..\css\signup.css" />
  <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
</head>

<body>
  <div class="signup-container">
    <div class="left-panel d-flex flex-column justify-content-end align-items-center text-white text-center">
      <div class="pb-4">
        <h2 class="fw-bold">Get Started With Us</h2>
        <p>Follow these simple steps to join our GreenRay community</p>
      </div>
    </div>

    <div class="right-panel">
      <h1 class="text-center fw-bold mb-2">Sign Up Account</h1>
      <p class="text-center text-muted mb-4">
        Enter your personal data to create your account
      </p>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <form class="form-content needs-validation" action="signup.php" method="POST" novalidate>
        <div class="row g-3">
          <div class="form-group col-md-6">
            <label for="firstName" class="fw-medium">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name"
              placeholder="Enter your first name" required />
            <div class="invalid-feedback">
              Please enter your first name.
            </div>
          </div>
          <div class="form-group col-md-6">
            <label for="lastName" class="fw-medium">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name"
              required />
            <div class="invalid-feedback">
              Please enter your last name.
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="email" class="fw-medium">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="eg. johnfrans@gmail.com"
            required />
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>

        <div class="form-group"> <label for="password" class="fw-medium">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password"
            required />
          <div class="invalid-feedback">
            Please enter a password.
          </div>
          <small class="text-muted">Must be at least 8 characters.</small>
        </div>

        <div class="form-group mb-4">
          <label for="confirm_password" class="fw-medium">Confirm Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password"
            placeholder="Confirm your password" required />
          <div class="invalid-feedback">
            Please confirm your password.
          </div>
        </div>

        <button type="submit" class="btn btn-success btn-block fw-bold" id="signupButton">
          Sign Up
        </button>

        <p class="text-center mt-3 login-text">
          Already have an account? <a href="signin.php" class="fw-semibold">Log In</a>
        </p>
      </form>
    </div>
  </div>

  <div class="modal fade" id="signupSuccessModal" tabindex="-1" aria-labelledby="signupSuccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content text-center p-4">
        <h5 class="fw-bold mb-3" id="welcomeMessage">Account Created Successfully</h5>
        <p>Your account has been successfully created.</p>
        <div class="mt-4">
          <a href="signin.php" class="btn btn-success px-4 py-2">Continue</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

  <script src="..\javascript\validation.js"></script>
</body>

</html>