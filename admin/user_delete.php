<?php
// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek apakah ada ID yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {

    $user_id = $_GET['id'];

    // 3. Buat query DELETE
    // Database sudah di-set 'ON DELETE SET NULL' untuk foreign key di 'consultation_requests',
    // jadi ini aman untuk dijalankan.
    $stmt = $koneksi->prepare("DELETE FROM users WHERE id = ?");

    // 'i' berarti parameter ini adalah integer
    $stmt->bind_param("i", $user_id);

    // 4. Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, langsung arahkan kembali
    } else {
        // Jika gagal, catat error
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID user tidak ditemukan.";
}

// 5. Arahkan kembali (redirect) admin ke halaman daftar user
header("Location: users.php");
exit; // Pastikan untuk keluar setelah redirect

?>