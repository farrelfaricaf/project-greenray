<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = "localhost";
$user = "root";
$password = ""; // Password default Laragon biasanya kosong
$database = "db_greenray";

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// ======================================================
// KODE BARU DIMULAI DARI SINI
// ======================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Fungsi Universal untuk mengirim email menggunakan PHPMailer
 *
 * @param string $to_email Alamat email penerima
 * @param string $subject Judul email
 * @param string $html_body Isi email dalam format HTML
 * @param string $alt_body Isi email dalam format Teks (untuk fallback)
 * @return bool true jika sukses, false jika gagal
 */
function sendEmail($to_email, $subject, $html_body, $alt_body)
{
    $mail = new PHPMailer(true);

    try {
        // Pengaturan Server (SMTP Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // GANTI INI: Masukkan email Gmail kamu
        $mail->Username   = $_ENV['GMAIL_USER'];
        // GANTI INI: Masukkan 16 KARAKTER "App Password" kamu
        $mail->Password   = $_ENV['GMAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Pengirim & Penerima
        // GANTI INI JUGA: Sesuaikan nama pengirim
        $mail->setFrom($_ENV['GMAIL_USER'], $_ENV['GMAIL_SENDER_NAME']);
        $mail->addAddress($to_email); // Email target (user)

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        $mail->AltBody = $alt_body;

        $mail->send();
        return true; // Sukses
    } catch (Exception $e) {
        // error_log("Mailer Error: {$mail->ErrorInfo}"); // Untuk debug
        return false; // Gagal
    }
}
?>