<?php
include "../koneksi.php";
session_start();

// 1. Cek apakah user sudah login. Jika belum, lempar ke halaman signin.
if (!isset($_SESSION['user_id'])) {
  header("Location: signin.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$alert_message = "";

// 2. Logika Update Profil (Nama & Email)
if (isset($_POST["save_profile"])) {
  $first_name = $koneksi->real_escape_string($_POST["first_name"]);
  $last_name = $koneksi->real_escape_string($_POST["last_name"]);
  $email = $koneksi->real_escape_string($_POST["email"]);
  $koneksi->query("UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE id=$user_id");
  $_SESSION['user_name'] = $first_name;
  $_SESSION['user_email'] = $email;
  $alert_message = '<div class="alert alert-success">Profil berhasil diperbarui.</div>';
}

// 3. Logika Ganti Foto Profil
if (isset($_POST["change_photo"])) {
  if (!empty($_FILES["photo"]["name"])) {
    $target_dir = "../uploads/profiles/";
    if (!is_dir($target_dir))
      mkdir($target_dir, 0755, true);
    $file_name = "user_" . $user_id . "_" . time() . "." . pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $file_name;
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
      if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $photo_path_db = "uploads/profiles/" . $file_name;
        $old_photo_path = $_SESSION['user_profile_pic'];
        if ($old_photo_path != 'img/default-profile.png' && file_exists("../" . $old_photo_path)) {
          unlink("../" . $old_photo_path);
        }
        $koneksi->query("UPDATE users SET profile_image_url='$photo_path_db' WHERE id=$user_id");
        $_SESSION['user_profile_pic'] = $photo_path_db;
        $alert_message = '<div class="alert alert-success">Foto profil berhasil diganti.</div>';
      } else {
        $alert_message = '<div class="alert alert-danger">Gagal meng-upload foto.</div>';
      }
    } else {
      $alert_message = '<div class="alert alert-danger">File bukan gambar.</div>';
    }
  } else {
    $alert_message = '<div class="alert alert-warning">Pilih file gambar terlebih dahulu.</div>';
  }
}

// 4. Logika Ganti Password
if (isset($_POST["change_password"])) {
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_confirm = $_POST['pass_confirm'];
  $user_data = $koneksi->query("SELECT password FROM users WHERE id=$user_id")->fetch_assoc();
  if (password_verify($pass_old, $user_data['password'])) {
    if (strlen($pass_new) >= 8) {
      if ($pass_new == $pass_confirm) {
        $hashed_password = password_hash($pass_new, PASSWORD_BCRYPT);
        $koneksi->query("UPDATE users SET password='$hashed_password' WHERE id=$user_id");
        $alert_message = '<div class="alert alert-success">Password berhasil diubah.</div>';
      } else {
        $alert_message = '<div class="alert alert-danger">Password baru dan konfirmasi tidak cocok.</div>';
      }
    } else {
      $alert_message = '<div class="alert alert-danger">Password baru harus minimal 8 karakter.</div>';
    }
  } else {
    $alert_message = '<div class="alert alert-danger">Password lama Anda salah.</div>';
  }
}

// 5. Ambil data terbaru user
$user = $koneksi->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
$is_logged_in = true;
$user_name = $user['first_name'];
$profile_pic = $user['profile_image_url'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Saya - GreenRay</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/globals.css">
  <link rel="stylesheet" href="../css/home.css">
  <link rel="icon" type="image/png" href="../img/favicon.png">

  <style>
    /* CSS untuk Dropdown Profil */
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
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dropdown-menu-header .dropdown-item:hover {
      background-color: #f1f1f1;
    }

    .dropdown-menu-header .dropdown-item-info {
      background-color: #f9f9f9;
      font-weight: 500;
    }

    .dropdown-menu-header .dropdown-item-info strong {
      font-weight: 700;
    }

    /* Style Halaman Profil */
    .profile-header {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      padding: 2rem;
    }

    .profile-picture-wrapper {
      position: relative;
      width: 120px;
      height: 120px;
      margin: 0 auto 1rem;
    }

    .profile-picture {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #198754;
    }

    .profile-picture-wrapper .upload-icon {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #fff;
      border-radius: 50%;
      padding: 6px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      cursor: pointer;
    }

    /* Perbaikan Style Menu Kiri */
    .list-group-item-action {
      padding: 1rem 1.5rem !important;
      font-weight: 500;
      margin-bottom: 8px;
      border-width: 2px !important;
      /* Buat border jadi 2px */
      border-radius: 0.375rem !important;
    }

    .list-group-item-action.active {
      background-color: #198754;
      border-color: #198754;
    }

    .list-group-item-action:hover,
    .list-group-item-action:focus {
      background-color: #f8f9fa;
      color: #198754;
      z-index: 2;
    }

    /* Perbaikan Style Riwayat */
    tr[data-bs-toggle="collapse"] {
      cursor: pointer;
    }

    tr[data-bs-toggle="collapse"][aria-expanded="true"] {
      border-width: 2px;
      border-bottom: none !important;
    }

    tr.collapse-row td {
      padding: 1.5rem !important;
      background-color: #f9f9f9 !important;
      border-top: none !important;
      border-bottom: 1px solid #dee2e6 !important;
      border-left: 1px solid #dee2e6 !important;
      border-right: 1px solid #dee2e6 !important;
      border-width: 0 2px 2px 2px !important;
      border-style: solid !important;
    }

    .collapse-row strong {
      display: inline-block;
      min-width: 120px;
      /* Atur lebar minimum label */
    }

    .tab-content .card.shadow-sm {
      border: 2px solid #dee2e6 !important;
      /* Paksa ada border 2px */
    }

    tr.clickable button {
        transition: transform 0.35s ease;
        line-height: 1;
        width: 28px; /* Pastikan ukurannya konsisten */
        height: 28px;
    }
    tr.clickable[aria-expanded="true"] button {
        transform: rotate(45deg); /* Putar 45 derajat */
    }

    /* 2. Transisi Fade-in + Slide untuk baris detail */
    tr.collapse-row {
        /* Memastikan transisi berjalan mulus */
        transition: opacity 0.15s ease-out;
    }
    
    tr.collapse-row td {
        /* ... (style border & padding kamu yang sudah ada) ... */
        
        /* Mulai dengan transparan */
        opacity: 0; 
    }

    /* Saat baris terbuka penuh (class .show ditambahkan Bootstrap), buat jadi terlihat */
    tr.collapse-row.show td {
        opacity: 1;
    }

    #nav-history .table thead th {
        font-weight: 700; /* Membuat font tebal */
        color: #212529;   /* Menggelapkan warna font (standar Bootstrap) */
        text-transform: uppercase; /* Opsional: Membuatnya huruf kapital */
        font-size: 0.85rem;      /* Opsional: Sedikit mengecilkan */
    }
  </style>
</head>

<body>
  <div class="home-wrapper">
    <div class="hero-wrapper" style="height: auto; min-height: auto; padding-top: 60px;">
      <div class="hero">
        <img class="green-ray-logo-1" src="../img/GreenRay_Logo 1-1.png" />
        <div class="header-menu">
          <div class="non-active"><a href="home.php">Home</a></div>
          <div class="non-active"><a href="portofolio.php">Portfolio</a></div>
          <div class="non-active"><a href="calc.php">Calculator</a></div>
          <div class="non-active"><a href="katalog.php">Catalog</a></div>
        </div>
        <div class="header-actions">
          <?php if ($is_logged_in): ?>
            <div class="profile-dropdown">
              <a href="#" class="profile-toggle" id="profileToggle">
                <img src="../<?php echo htmlspecialchars($profile_pic); ?>" alt="Profil" class="profile-picture-header">
              </a>
              <div class="dropdown-menu-header" id="profileDropdownMenu">
                <div class="dropdown-item-info">Halo, <strong><?php echo htmlspecialchars($user_name); ?></strong>!</div>
                <a class="dropdown-item" href="profile.php">Profil Saya</a>
                <a class="dropdown-item" href="contact-us.php">Bantuan / Kontak</a>
                <a class="dropdown-item" href="logout.php">Logout</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="container" style="margin-top: 80px; margin-bottom: 100px;">
    <div class="row">
      <div class="col-lg-4">
        <div class="profile-header text-center">

          <form action="profile.php" method="POST" enctype="multipart/form-data" id="formChangePhoto">
            <div class="profile-picture-wrapper" onclick="document.getElementById('upload-photo-input').click();">
              <img src="../<?php echo htmlspecialchars($user['profile_image_url']); ?>" alt="Foto Profil"
                class="profile-picture">
              <div class="upload-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-camera-fill" viewBox="0 0 16 16">
                  <path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                  <path
                    d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z" />
                </svg>
              </div>
            </div>
            <input type="file" name="photo" id="upload-photo-input" style="display: none;"
              onchange="document.getElementById('formChangePhoto').submit();">
            <input type="hidden" name="change_photo" value="1">
          </form>

          <h4 class="fw-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
          <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="list-group mt-3" id="nav-tab" role="tablist">
          <a class="list-group-item list-group-item-action active" id="nav-profile-tab" data-bs-toggle="tab"
            href="#nav-profile" role="tab">Edit Profil</a>
          <a class="list-group-item list-group-item-action" id="nav-password-tab" data-bs-toggle="tab"
            href="#nav-password" role="tab">Ganti Password</a>
          <a class="list-group-item list-group-item-action" id="nav-history-tab" data-bs-toggle="tab"
            href="#nav-history" role="tab">Riwayat Konsultasi</a>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="tab-content" id="nav-tabContent">

          <?php if (!empty($alert_message))
            echo $alert_message; ?>

          <div class="tab-pane fade show active" id="nav-profile" role="tabpanel">
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Informasi Profil</h5>
                <form action="profile.php" method="POST">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="first_name" class="form-label">Nama Depan</label>
                      <input type="text" class="form-control" id="first_name" name="first_name"
                        value="<?php echo htmlspecialchars($user['first_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="last_name" class="form-label">Nama Belakang</label>
                      <input type="text" class="form-control" id="last_name" name="last_name"
                        value="<?php echo htmlspecialchars($user['last_name']); ?>">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                      value="<?php echo htmlspecialchars($user['email']); ?>">
                  </div>
                  <button type="submit" name="save_profile" class="btn btn-success">Simpan Perubahan</button>
                </form>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-password" role="tabpanel">
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Ubah Password</h5>
                <form action="profile.php#nav-password" method="POST">
                  <div class="mb-3">
                    <label for="pass_old" class="form-label">Password Lama</label>
                    <input type="password" class="form-control" id="pass_old" name="pass_old" required>
                  </div>
                  <div class="mb-3">
                    <label for="pass_new" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="pass_new" name="pass_new" minlength="8" required>
                    <small class="form-text text-muted">Minimal 8 karakter.</small>
                  </div>
                  <div class="mb-3">
                    <label for="pass_confirm" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" required>
                  </div>
                  <button type="submit" name="change_password" class="btn btn-success">Simpan Password Baru</button>
                </form>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="nav-history" role="tabpanel">
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Riwayat Konsultasi Anda</h5>
                <div class="table-responsive">
                  <table class="table table-hover align-middle">
                    <thead>
                      <tr>
                        <th></th>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th class="text-end">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Ambil semua data konsultasi
                      $consult_history = $koneksi->query("SELECT * FROM consultation_requests WHERE user_id=$user_id ORDER BY created_at DESC");
                      if ($consult_history->num_rows > 0) {
                        while ($row = $consult_history->fetch_assoc()) {
                          ?>
                          <tr class="clickable" data-bs-toggle="collapse" data-bs-target="#detail-<?php echo $row['id']; ?>"
                            aria-expanded="false">
                            <td><button class="btn btn-sm btn-outline-success">+</button></td>
                            <td><strong><?php echo htmlspecialchars($row['order_number']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($row['calc_location']); ?></td>
                            <td class="text-end">
                              <span class="badge bg-success">Selesai</span>
                            </td>
                          </tr>

                          <tr class="collapse collapse-row" id="detail-<?php echo $row['id']; ?>">
                            <td colspan="5" class="p-4">
                              <h6 class="fw-bold">Detail Konsultasi #<?php echo htmlspecialchars($row['order_number']); ?>
                              </h6>
                              <div class="row">
                                <div class="col-md-6">
                                  <strong>Data Pemesan:</strong>
                                  <ul class="list-unstyled mt-2">
                                    <li><strong>Nama:</strong> <?php echo htmlspecialchars($row['full_name']); ?></li>
                                    <li><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></li>
                                    <li><strong>Telepon:</strong> <?php echo htmlspecialchars($row['phone']); ?></li>
                                    <li><strong>Alamat:</strong>
                                      <?php echo htmlspecialchars($row['address'] . ', ' . $row['kelurahan'] . ', ' . $row['kecamatan'] . ' ' . $row['postal_code']); ?>
                                    </li>
                                  </ul>
                                </div>
                                <div class="col-md-6">
                                  <strong>Hasil Kalkulasi:</strong>
                                  <ul class="list-unstyled mt-2">
                                    <li><strong>Sistem:</strong>
                                      <?php echo htmlspecialchars($row['result_system_capacity_kwp']); ?> kWp</li>
                                    <li><strong>Hemat/bln:</strong> Rp
                                      <?php echo number_format($row['result_monthly_savings'], 0, ',', '.'); ?>
                                    </li>
                                    <li><strong>Investasi:</strong> Rp
                                      <?php echo number_format($row['result_investment_estimate'], 0, ',', '.'); ?>
                                    </li>
                                  </ul>
                                  <strong>Catatan Tambahan:</strong>
                                  <ul class="list-unstyled mt-2">
                                    <li><strong>Luas Atap:</strong>
                                      <?php echo htmlspecialchars($row['calc_roof_area'] ? $row['calc_roof_area'] : '-'); ?>
                                    </li>
                                    <li><strong>Catatan:</strong>
                                      <?php echo htmlspecialchars($row['calc_notes'] ? $row['calc_notes'] : '-'); ?></li>
                                  </ul>
                                </div>
                              </div>
                            </td>
                          </tr>
                          <?php
                        } // Akhir while loop
                      } else {
                        echo '<tr><td colspan="5" class="text-center p-4">Anda belum memiliki riwayat konsultasi.</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="footer">
    <div class="footer-content">
      <div class="footer-info">
        <div class="footer-logo-text">
          <img class="green-ray-logo-12" src="../img/GreenRay_Logo 1-1.png" />
          <div class="footer-desc">
            Powering a cleaner, brighter future for Indonesia. We are your
            trusted partner in sustainable energy solutions, built on
            transparency and long-term value.
          </div>
        </div>
      </div>
      <div class="copyright">
        © 2025 GreenRay. All rights reserved.
      </div>
    </div>
    <div class="footer-menu">
      <div class="menu-container-footer">
        <div class="title-footer">Quick Links</div>
        <div class="dec-container-footer">
          <div class="list-footer"><a href="home.php">Home</a></div>
          <div class="list-footer"><a href="portofolio.php">Our Portfolio</a></div>
          <div class="list-footer"><a href="calc.php">Saving Calculator</a></div>
        </div>
      </div>
      <div class="menu-container-footer">
        <div class="title-footer">Get In Touch</div>
        <div class="dec-container-footer">
          <div class="list-footer">
            <a href="contact-us.php">Quick Consultation via WhatsApp</a>
          </div>
          <div class="list-footer">
            <a href="contact-us.php">Send a Formal Inquiry Email</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Logika dropdown profil
      const profileToggle = document.getElementById('profileToggle');
      const profileDropdownMenu = document.getElementById('profileDropdownMenu');
      if (profileToggle) {
        profileToggle.addEventListener('click', function (e) {
          e.preventDefault();
          profileDropdownMenu.classList.toggle('show');
        });
        window.addEventListener('click', function (e) {
          if (profileToggle && !profileToggle.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
            profileDropdownMenu.classList.remove('show');
          }
        });
      }

      // Logika untuk tetap di tab yang aktif setelah refresh (khususnya setelah ganti password)
      var hash = window.location.hash;
      if (hash) {
        var tab = document.querySelector('a[href="' + hash + '"]');
        if (tab) {
          new bootstrap.Tab(tab).show();
        }
      }
    });
  </script>
</body>

</html>