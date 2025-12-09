<?php

session_start();


include '../koneksi.php';

$error_message = ""; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {

  
  
  if (isset($_POST['email']) && isset($_POST['password'])) {

    $email = $koneksi->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    
    $sql_select = "SELECT id, first_name, email, password, profile_image_url FROM users WHERE email = '$email'";
    $result = $koneksi->query($sql_select);

    if ($result && $result->num_rows == 1) {
      $user = $result->fetch_assoc();

      
      if (password_verify($password, $user['password'])) {

        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_profile_pic'] = $user['profile_image_url'];

        
        header("Location: home.php");
        exit;

      } else {
        $error_message = "Email atau password yang Anda masukkan salah.";
      }
    } else {
      $error_message = "Email atau password yang Anda masukkan salah.";
    }

  } else {
    
    $error_message = "Terjadi kesalahan pada form. Silakan coba lagi.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="..\css\signin.css" />
  <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
</head>

<body>
  <div class="signup-container">
    <div class="left-panel">
      <h1 class="fw-bold">Sign In Account</h1>
      <p class="text-muted">Enter your personal data to access your account</p>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <form class="form-content" action="signin.php" method="POST" novalidate>
        <div class="form-group mb-4">
          <label for="email" class="fw-medium">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="eg. johnfrans@gmail.com"
            required />
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>

        <div class="form-group mb-4">
          <label for="password" class="fw-medium">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password"
            required minlength="8" />
          <div class="invalid-feedback">
            Password must be at least 8 characters.
          </div>
          <small class="text-muted">Must be at least 8 characters.</small>
        </div>

        <div class="d-flex justify-content-end mb-3 mt-n3">
          <a href="reset_password.php" class="text-muted small text-decoration-none">Lupa Password?</a>
        </div>
        <button type="submit" class="btn btn-success fw-bold w-100" id="signinButton">
          Sign In
        </button>

        <p class="login-text mt-3">
          Don’t have an account? <a href="signup.php" class="fw-semibold">Sign Up</a> </p>
      </form>
    </div>

    <div class="right-panel d-flex flex-column justify-content-end align-items-center text-white text-center">
      <div class="pb-4">
        <h2 class="fw-bold">Welcome Back!</h2>
        <p>Join our GreenRay community and start your journey with us.</p>
      </div>
    </div>
  </div>

  <div class="modal fade" id="loginSuccessModal" tabindex="-1" aria-labelledby="loginSuccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center border-0 shadow">
        <div class="modal-body p-5">
          <h5 class="modal-title mb-3 fw-bold" id="loginSuccessModalLabel">
            Login Successful
          </h5>
          <p class="text-muted mb-4" id="welcomeBackMessage">
            Welcome back! You have successfully logged in to your GreenRay account.
          </p>
          <a href="..\html\home.html" class="btn btn-success px-4 py-2">Continue</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

  <script src="..\javascript\login.js"></script>
</body>

</html>