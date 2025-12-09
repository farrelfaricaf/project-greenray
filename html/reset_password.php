<?php
include "../koneksi.php";
session_start();

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
                $_SESSION['fp_otp_exp'] = $now + 600; 
                $_SESSION['fp_last_sent'] = $now;
                
                $_SESSION['fp_step'] = 'otp';
                $step = "otp";

                
                
                $subject = "Kode OTP Reset Password GreenRay";
                $html_body = "Halo, <br><br>Ini adalah kode OTP Anda: <b>$otp</b><br>Kode ini akan kedaluwarsa dalam 10 menit. Jangan berikan kode ini kepada siapa pun.";
                $alt_body = "Ini adalah kode OTP Anda: $otp. Kode akan kedaluwarsa dalam 10 menit.";
                $sent = sendEmail($email, $subject, $html_body, $alt_body);
                

                if ($sent) {
                    $success = "OTP dikirim ke email.";
                } else {
                    
                    $success = "(Local) Email gagal. Kode OTP: $otp";
                }
            }
        }
        $st->close();
    }
}


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
            

            
            
            $subject = "Kode OTP Reset Password GreenRay (Baru)";
            $html_body = "Halo, <br><br>Ini adalah kode OTP baru Anda: <b>$otp</b><br>Kode ini akan kedaluwarsa dalam 10 menit.";
            $alt_body = "Ini adalah kode OTP baru Anda: $otp.";
            $sent = sendEmail($_SESSION['fp_email'], $subject, $html_body, $alt_body);
            

            $success = $sent ? "OTP baru dikirim." : "(Local) Gagal kirim. OTP: $otp";

            $_SESSION['fp_step'] = 'otp';
            $step = 'otp';
        }
    }
}


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
    <link rel="icon" type="image/png" href="..\img\favicon.png" sizes="180px180">
    <style>
        body {
            background: #f7f9fc;
            font-family: Inter, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }

        .card-mat {
            max-width: 760px;
            margin: 0;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 8px 26px rgba(0, 0, 0, .06);
        }

        .header {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .logo-dot {
            width: 44px;
            height: 44px;
            border-radius: 12px;
           
            background: linear-gradient(135deg, #198754, #20c997);
           
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
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
           
            background: linear-gradient(90deg, #198754, #20c997);
           
        }

       
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
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
            flex-grow: 1;
            flex-basis: 0;
        }

        .otp-box input {
            width: 100%;
            text-align: center;
            font-size: 20px;
            border: 0;
            background: transparent;
            padding: 0;
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
            background: linear-gradient(90deg, #ff6b6b, #f59e0b, #198754);
            transition: .2s;
        }

        .eye-btn {
            cursor: pointer;
            user-select: none;
        }

       
        .valid {
            color: #198754;
            font-weight: 600;
        }

       
        @media (max-width: 576px) {
            .card-mat {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .otp-container {
                gap: 5px;
            }

            .otp-box {
                height: 48px;
            }

            .otp-box input {
                font-size: 18px;
            }
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

                <?php if (isset($_SESSION['fp_debug_otp'])):?>
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