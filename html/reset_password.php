<?php
include "../koneksi.php";
session_start();

// ========================================================================
// KONFIGURASI PHPMailer DIMULAI DI SINI
// ========================================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Sesuaikan path ini jika folder vendor kamu ada di tempat lain
require '../vendor/autoload.php';

/**
 * Fungsi baru untuk mengirim email OTP menggunakan PHPMailer
 * @param string $to_email - Alamat email penerima
 * @param string $otp_code - Kode OTP 6 digit
 * @param string $subject - Judul email
 * @return bool - true jika sukses, false jika gagal
 */
function sendEmailOTP($to_email, $otp_code, $subject)
{
    $mail = new PHPMailer(true);

    try {
        // Pengaturan Server (SMTP Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // GANTI INI: Masukkan email Gmail kamu yang akan dipakai sebagai pengirim
        $mail->Username = 'greenraysolarpanel@gmail.com';
        // GANTI INI: Masukkan 16 KARAKTER "App Password" kamu
        $mail->Password = 'hqob klmc hyin djep';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Pengirim & Penerima
        // GANTI INI JUGA: Sesuaikan nama pengirim
        $mail->setFrom('greenraysolarpanel@gmail.com', 'GreenRay Solar Panel');
        $mail->addAddress($to_email); // Email target (user)

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = 'Halo, <br><br>Ini adalah kode OTP Anda: <b>' . $otp_code . '</b><br>Kode ini akan kedaluwarsa dalam 10 menit. Jangan berikan kode ini kepada siapa pun.';
        $mail->AltBody = 'Ini adalah kode OTP Anda: ' . $otp_code . '. Kode akan kedaluwarsa dalam 10 menit.';

        $mail->send();
        return true; // Sukses
    } catch (Exception $e) {
        // Kamu bisa mencatat error jika perlu, tapi jangan tampilkan ke user
        // error_log("Mailer Error: {$mail->ErrorInfo}");
        return false; // Gagal
    }
}
// ========================================================================
// KONFIGURASI PHPMailer SELESAI
// ========================================================================


if (!isset($_SESSION['fp_step']))
    $_SESSION['fp_step'] = 'email';
$step = $_SESSION['fp_step'];
$error = "";
$success = "";

function genOTP($len = 6)
{
    $d = "0123456789";
    $o = "";
    for ($i = 0; $i < $len; $i++)
        $o .= $d[random_int(0, 9)];
    return $o;
}

/* SEND EMAIL */
if (isset($_POST['send_email'])) {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid.";
    } else {
        $st = $koneksi->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $st->bind_param('s', $email);
        $st->execute();
        $res = $st->get_result();
        if ($res->num_rows == 0) {
            $error = "Email tidak ditemukan.";
        } else {
            $user = $res->fetch_assoc();
            $uid = $user['id'];
            $now = time();

            if (isset($_SESSION['fp_last_sent']) && $now - $_SESSION['fp_last_sent'] < 60) {
                $error = "Tunggu 60 detik sebelum kirim ulang.";
            } else {
                $otp = genOTP();
                $_SESSION['fp_email'] = $email;
                $_SESSION['fp_user_id'] = $uid;
                $_SESSION['fp_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
                $_SESSION['fp_otp_exp'] = $now + 600; // 10 menit
                $_SESSION['fp_last_sent'] = $now;
                // $_SESSION['fp_debug_otp']=$otp; // (Kita tidak butuh ini lagi jika email berfungsi)
                $_SESSION['fp_step'] = 'otp';
                $step = "otp";

                // === PERUBAHAN DI SINI ===
                // Mengirim email menggunakan fungsi PHPMailer yang baru
                $sent = sendEmailOTP($email, $otp, "Kode OTP Reset Password GreenRay");
                // =========================

                if ($sent) {
                    $success = "OTP dikirim ke email.";
                } else {
                    // Fallback untuk tes lokal jika GAGAL kirim email
                    $success = "(Local) Email gagal. Kode OTP: $otp";
                }
            }
        }
        $st->close();
    }
}

/* VERIFY OTP */
if (isset($_POST['verify_otp'])) {
    $input = trim($_POST['otp_full']);
    $now = time();
    if (!isset($_SESSION['fp_otp_hash'])) {
        $error = "Tidak ada OTP aktif.";
        $_SESSION['fp_step'] = 'email';
        $step = 'email';
    } elseif ($now > $_SESSION['fp_otp_exp']) {
        $error = "OTP kadaluarsa.";
        $_SESSION['fp_step'] = 'email';
        $step = 'email';
    } elseif (!password_verify($input, $_SESSION['fp_otp_hash'])) {
        $error = "OTP salah.";
        $step = 'otp';
    } else {
        $_SESSION['fp_step'] = 'newpass';
        $step = 'newpass';
        unset($_SESSION['fp_otp_hash'], $_SESSION['fp_otp_exp']);
        $success = "OTP benar. Buat password baru.";
    }
}

/* RESEND OTP */
if (isset($_POST['resend_otp'])) {
    if (!isset($_SESSION['fp_email'])) {
        $error = "Sesi tidak valid.";
        $_SESSION['fp_step'] = 'email';
        $step = 'email';
    } else {
        $now = time();
        if (isset($_SESSION['fp_last_sent']) && $now - $_SESSION['fp_last_sent'] < 60) {
            $error = "Tunggu 60 detik.";
            $step = 'otp';
        } else {
            $otp = genOTP();
            $_SESSION['fp_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
            $_SESSION['fp_otp_exp'] = $now + 600;
            $_SESSION['fp_last_sent'] = $now;
            // $_SESSION['fp_debug_otp']=$otp; // (Tidak perlu lagi)

            // === PERUBAHAN DI SINI ===
            // Mengirim email menggunakan fungsi PHPMailer yang baru
            $sent = sendEmailOTP($_SESSION['fp_email'], $otp, "Kode OTP Reset Password GreenRay (Baru)");
            // =========================

            $success = $sent ? "OTP baru dikirim." : "(Local) Gagal kirim. OTP: $otp";

            $_SESSION['fp_step'] = 'otp';
            $step = 'otp';
        }
    }
}

/* SAVE PASSWORD */
if (isset($_POST['change_pass'])) {
    $new = $_POST['new_pass'];
    $confirm = $_POST['confirm_pass'];

    if ($new !== $confirm) {
        $error = "Konfirmasi tidak cocok.";
        $step = 'newpass';
    } elseif (strlen($new) < 8) {
        $error = "Minimal 8 karakter.";
        $step = 'newpass';
    } elseif (!preg_match('/[A-Za-z]/', $new) || !preg_match('/[0-9]/', $new)) {
        $error = "Harus huruf + angka.";
        $step = 'newpass';
    } else {
        $uid = $_SESSION['fp_user_id'] ?? null;
        if (!$uid) {
            $error = "Sesi tidak valid.";
            $step = 'email';
        } else {
            $r = $koneksi->prepare("SELECT password FROM users WHERE id=?");
            $r->bind_param('i', $uid);
            $r->execute();
            $old = $r->get_result()->fetch_assoc()['password'] ?? '';
            $r->close();

            if (password_verify($new, $old)) {
                $error = "Tidak boleh sama dengan password lama.";
                $step = 'newpass';
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $u = $koneksi->prepare("UPDATE users SET password=? WHERE id=?");
                $u->bind_param('si', $hash, $uid);
                $u->execute();
                $u->close();

                session_unset();
                session_destroy();
                session_start();
                $_SESSION['fp_done'] = true;
                header("Location: reset_password.php?done=1");
                exit;
            }
        }
    }
}

$show_modal = isset($_GET['done']) && isset($_SESSION['fp_done']);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Reset Password</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f7f9fc;
            font-family: Inter, Arial, sans-serif;
        }

        .card-mat {
            max-width: 760px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 8px 26px rgba(0, 0, 0, .06);
        }

        .header {
            display: flex;
            gap: 16px;
        }

        .logo-dot {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c4dff, #b84cff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 20px;
        }

        .stepper {
            display: flex;
            gap: 8px;
            margin: 20px 0;
        }

        .step {
            flex: 1;
            height: 8px;
            border-radius: 8px;
            background: #e5e7eb;
        }

        .step.active {
            background: linear-gradient(90deg, #7c4dff, #b84cff);
        }

        .otp-box {
            width: 46px;
            height: 55px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, .06);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .otp-box input {
            width: 100%;
            text-align: center;
            font-size: 20px;
            border: 0;
            background: transparent;
        }

        .strength {
            height: 10px;
            background: #ececec;
            border-radius: 8px;
            margin-top: 6px;
        }

        .strength-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #ff6b6b, #f59e0b, #10b981);
            transition: .2s;
        }

        .eye-btn {
            cursor: pointer;
            user-select: none;
        }

        .valid {
            color: #10b981;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="card-mat">

        <div class="header">
            <div class="logo-dot">G</div>
            <div>
                <h4 style="margin:0;">Reset Password</h4>
                <div style="color:#6b7280;font-size:13px;">Ikuti langkah berikut</div>
            </div>
        </div>

        <div class="stepper">
            <div class="step <?= in_array($step, ['email', 'otp', 'newpass']) ? 'active' : '' ?>"></div>
            <div class="step <?= in_array($step, ['otp', 'newpass']) ? 'active' : '' ?>"></div>
            <div class="step <?= $step === 'newpass' ? 'active' : '' ?>"></div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <?php if ($step === 'email'): ?>
            <form method="POST">
                <label>Email Terdaftar</label>
                <div class="input-group mb-2">
                    <input type="email" class="form-control" name="email" required placeholder="nama@domain.com">
                    <button class="btn btn-primary" name="send_email">Kirim OTP</button>
                </div>
                <div style="font-size:13px;color:#6b7280;">Kode OTP akan dikirim ke email.</div>
            </form>
        <?php endif; ?>

        <?php if ($step === 'otp'): ?>
            <form method="POST" class="mt-3">

                <label>Masukkan Kode OTP</label>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="otp-box"><input maxlength="1" id="o<?= $i ?>" class="otp-input"></div>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="otp_full" id="otp_full">

                <div class="d-flex justify-content-between mt-3">
                    <div id="cooldown_label" style="font-size:13px;color:#6b7280;"></div>
                    <div>
                        <button class="btn btn-primary btn-sm me-2" name="verify_otp">Verifikasi</button>
                        <button class="btn btn-link btn-sm" name="resend_otp" id="btn_resend" disabled>Kirim ulang</button>
                    </div>
                </div>

                <?php if (isset($_SESSION['fp_debug_otp'])): // Ini masih ada dari kode aslimu, saya biarkan ?>
                    <div class="mt-2" style="font-size:13px;color:#6b7280;">
                        Tidak menerima email? OTP Anda: <b><?= $_SESSION['fp_debug_otp'] ?></b>
                    </div>
                <?php endif; ?>

            </form>
        <?php endif; ?>

        <?php if ($step === 'newpass'): ?>
            <form method="POST" class="mt-2">

                <label>Password Baru</label>
                <div class="input-group mb-2">
                    <input type="password" id="new_pass" name="new_pass" class="form-control" required>
                    <span class="input-group-text eye-btn" onclick="toggleEye('new_pass', this)">👁</span>
                </div>

                <div class="strength">
                    <div id="strengthFill" class="strength-fill"></div>
                </div>

                <div class="mt-2" style="font-size:13px;">
                    <div id="rule_len">• Minimal 8 karakter</div>
                    <div id="rule_combo">• Kombinasi huruf + angka</div>
                    <div id="rule_diff">• Tidak sama dengan password lama</div>
                </div>

                <label class="mt-3">Konfirmasi Password</label>
                <div class="input-group mb-3">
                    <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" required>
                    <span class="input-group-text eye-btn" onclick="toggleEye('confirm_pass', this)">👁</span>
                </div>

                <button class="btn btn-primary w-100" name="change_pass">Simpan Password Baru</button>

            </form>
        <?php endif; ?>

    </div>

    <div class="modal fade" id="doneModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <h5>Password Berhasil Diubah 🎉</h5>
                <p style="font-size:13px;color:#6b7280;">Silakan login kembali.</p>
                <a href="signin.php" class="btn btn-primary w-100">Login Sekarang</a>
            </div>
        </div>
    </div>

    <?php if ($show_modal): ?>
        <script>window.onload = () => new bootstrap.Modal(document.getElementById('doneModal')).show();</script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // =======================
        //   EYE ICON — FIXED ✓
        // =======================
        function toggleEye(id, el) {
            let input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                el.textContent = "👁‍🗨";
            } else {
                input.type = "password";
                el.textContent = "👁";
            }
        }

        // =======================
        //   OTP INPUT LOGIC ✓
        // =======================
        let boxes = [];
        for (let i = 1; i <= 6; i++) { boxes.push(document.getElementById("o" + i)); }

        if (boxes[0]) {
            boxes.forEach((b, idx) => {
                b.addEventListener("input", () => {
                    b.value = b.value.replace(/\D/g, '');
                    if (b.value && idx < 5) boxes[idx + 1].focus();
                    collectOTP();
                });
                b.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !b.value && idx > 0) {
                        boxes[idx - 1].focus();
                    }
                });
                if (idx === 0) {
                    b.addEventListener("paste", (e) => {
                        e.preventDefault();
                        let txt = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                        txt.split('').forEach((v, i) => { boxes[i].value = v; });
                        collectOTP();
                    });
                }
            });
        }

        function collectOTP() {
            let full = boxes.map(b => b.value).join("");
            document.getElementById("otp_full").value = full;
        }

        // =======================
        //   RESEND COOLDOWN ✓
        // =======================
        let cooldown = <?= isset($_SESSION['fp_last_sent']) ? max(0, 60 - (time() - $_SESSION['fp_last_sent'])) : 0 ?>;
        let cdLabel = document.getElementById("cooldown_label");
        let btnResend = document.getElementById("btn_resend");

        function runCD() {
            if (!cdLabel) return;
            if (cooldown > 0) {
                cdLabel.textContent = "Kirim ulang tersedia dalam " + cooldown + " dtk";
                btnResend.disabled = true;
                cooldown--;
                setTimeout(runCD, 1000);
            } else {
                cdLabel.textContent = "";
                btnResend.disabled = false;
            }
        }
        runCD();

        // =======================
        //   STRENGTH METER ✓
        // =======================
        const newPass = document.getElementById("new_pass");
        const fill = document.getElementById("strengthFill");
        const rLen = document.getElementById("rule_len");
        const rCombo = document.getElementById("rule_combo");
        const rDiff = document.getElementById("rule_diff");

        if (newPass) {
            newPass.addEventListener("input", () => {
                let v = newPass.value;
                let score = 0;

                if (v.length >= 8) { rLen.classList.add("valid"); score++; }
                else rLen.classList.remove("valid");

                if (/[A-Za-z]/.test(v) && /[0-9]/.test(v)) { rCombo.classList.add("valid"); score++; }
                else rCombo.classList.remove("valid");

                if (v.length > 0) rDiff.classList.add("valid");
                else rDiff.classList.remove("valid");

                fill.style.width = (score / 2) * 100 + "%";
            });
        }
    </script>
</body>

</html>