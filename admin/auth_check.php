<?php
session_start();

// Cek apakah sesi 'admin_id' ada DAN sesi 'admin_role' adalah 'admin'
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    
    // Jika salah satu tidak terpenuhi, lempar ke halaman login
    header('Location: login.php');
    exit; // Wajib ada 'exit' setelah header location
}

// Jika lolos, biarkan skrip berlanjut...
// (Kamu bisa panggil ID admin jika perlu)
// $current_admin_id = $_SESSION['admin_id'];
// $current_admin_name = $_SESSION['admin_name'];
?>