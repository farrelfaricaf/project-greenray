<?php
// Selalu mulai session
session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman home
header("Location: home.php");
exit;
?>