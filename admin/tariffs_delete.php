<?php
// 1. Hubungkan ke database
include '../koneksi.php';

// 2. Cek apakah ada ID yang dikirim melalui URL (GET)
if (isset($_GET['id'])) {

    $tariff_id = $_GET['id'];

    // 3. Buat query DELETE
    $stmt = $koneksi->prepare("DELETE FROM power_tariffs WHERE id = ?");

    // 'i' berarti parameter ini adalah integer
    $stmt->bind_param("i", $tariff_id);

    // 4. Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, langsung arahkan kembali
    } else {
        // Jika gagal, catat error
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Error: ID tarif tidak ditemukan.";
}

// 5. Arahkan kembali (redirect) admin ke halaman daftar tarif
header("Location: tariffs.php");
exit; // Pastikan untuk keluar setelah redirect

?>