<?php
session_start(); // Mulai sesi

// Hapus semua variabel session
session_unset();

// Hancurkan sesi
session_destroy();

// Arahkan (redirect) kembali ke halaman login
header("Location: login.php?status=logout");
exit;
?>