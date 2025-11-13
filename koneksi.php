<?php
$host = "localhost";
$user = "root";
$password = ""; // Password default Laragon biasanya kosong
$database = "db_greenray";

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>