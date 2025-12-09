<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = "localhost";
$user = "root";
$password = "";
$database = "db_greenray";

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

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

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['GMAIL_USER'];
        $mail->Password = $_ENV['GMAIL_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom($_ENV['GMAIL_USER'], $_ENV['GMAIL_SENDER_NAME']);
        $mail->addAddress($to_email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html_body;
        $mail->AltBody = $alt_body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>