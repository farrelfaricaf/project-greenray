<?php
session_start();
include '../koneksi.php'; // Hubungkan ke database

$error_message = "";

// 1. Cek: Jika SUDAH login, lempar ke dashboard
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header('Location: index.php'); // Arahkan ke dashboard
    exit;
}

// 2. Cek: Jika user mengirim form login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {

        $email = $koneksi->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        // 3. Query untuk mencari user dengan email DAN role admin
        $sql = "SELECT id, password, first_name FROM users WHERE email = ? AND role = 'admin' LIMIT 1";

        if ($stmt = $koneksi->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $admin = $result->fetch_assoc();

                // 4. Verifikasi password
                if (password_verify($password, $admin['password'])) {

                    // 5. SUKSES! Buat sesi admin
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['first_name'];
                    $_SESSION['admin_role'] = 'admin'; // Tandai sebagai admin

                    header('Location: index.php'); // Lempar ke dashboard
                    exit;
                } else {
                    $error_message = "Email atau password salah.";
                }
            } else {
                $error_message = "Email atau password salah, atau Anda bukan admin.";
            }
            $stmt->close();
        } else {
            $error_message = "Terjadi kesalahan pada database.";
        }
    } else {
        $error_message = "Mohon isi semua field.";
    }
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Login</title>
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #e0f2f1 0%, #c8e6c9 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            /* Padding untuk jarak di layar kecil */
        }

        .card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 25px;
            /* Kita tidak perlu margin-top di sini karena body sudah flex */
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
            padding-bottom: 0;
        }

        .card-header h3 {
            color: #1a1a1a;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .form-floating label {
            color: #6c757d;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .card-footer {
            background-color: transparent;
            border-top: none;
            padding-top: 15px;
        }

        .card-footer a {
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
        }

        .card-footer a:hover {
            color: #28a745;
        }

        .alert-danger {
            border-radius: 0.75rem;
            font-size: 0.9rem;
        }

        .forgot-pass {
            font-size: 0.9rem;
            text-decoration: none;
            color: #28a745;
            font-weight: 500;
        }
        .forgot-pass:hover {
            text-decoration: underline;
            color: #1e7e34;
        }

        @media (max-width: 576px) {
            .card {
                padding: 20px;
            }
            .card-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header">
            <h3 class="text-center my-4">Admin Login GreenRay</h3>
        </div>
        <div class="card-body">

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputEmail" name="email" type="email" placeholder="nama@contoh.com"
                        required />
                    <label for="inputEmail">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputPassword" name="password" type="password"
                        placeholder="Password" required />
                    <label for="inputPassword">Password</label>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                    <a class="forgot-pass" href="../html/reset_password.php">Lupa Password?</a>

                    <button type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center py-3">
            <div class="small"><a href="../html/home.php">Kembali ke Halaman Utama</a></div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>