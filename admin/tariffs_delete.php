<?php
include '../koneksi.php';
include 'auth_check.php';

if (isset($_GET['id'])) {
    $tariff_id = $_GET['id'];
    $stmt = $koneksi->prepare("DELETE FROM power_tariffs WHERE id = ?");
    $stmt->bind_param("i", $tariff_id);
    if ($stmt->execute()) {
    } else {
        echo "Error: Gagal menghapus data. " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error: ID tarif tidak ditemukan.";
}
header("Location: tariffs.php");
exit;
?>